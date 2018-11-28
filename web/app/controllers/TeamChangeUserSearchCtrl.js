app.controller('TeamChangeUserSearchCtrl', function ($scope,TeamChangeUserSearchCtrlData,jsonDataPromise, modal, $rootScope, $state) {

    var self=this;

    angular.extend($scope, TeamChangeUserSearchCtrlData);

    try {
        $scope.urlState=angular.fromJson($state.params.urlState);
    } catch (e) {
        $scope.urlState={
            users: {
                filter: {},
                pageNum: 1
            }
        };
    }

    $scope.$watch('urlState',function(newValue,oldValue){
        if (oldValue!=newValue) {
            $state.transitionTo($state.current.name, {urlState: angular.toJson(newValue)}, { location: 'replace', inherit: true, relative: $state.$current, notify: false });
        }
    },true);

    $scope.filter=$scope.urlState.users.filter;
    $scope.state={};

    this.loadMore=function(scrollLoadCallback) {
        $scope.urlState.users.pageNum++;
        self.load(function(data) {
            scrollLoadCallback(data.hasMore);
        });
    };

    this.load=function(callback) {
        callback = callback || function() {};
        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-team-change-user-search/users',{urlState:angular.toJson($scope.urlState)}).then(function (data) {
            $scope.state.loading=false;

            if ($scope.urlState.users.pageNum > 1) {
                data.items = $scope.items.concat(data.items);
            }

            if (!angular.isArray(data.items)) {
                data.items=null;
            }

            if (!data.searchUserCount) {
                data.searchUserCount=0;
            }

            angular.extend($scope, data);

            if ($scope.state.modifiedWhileLoading) {
                $scope.state.modifiedWhileLoading=false;
                self.load(callback);
            }
            callback(data);
        });
    };

    $rootScope.$on('userTeamRequestAdded',function(event,user_id) {
        for(var userIdx in $scope.items) {
            var item=$scope.items[userIdx];
            if (item.id==user_id) {
                var newItem=angular.copy(item);
                newItem.invitation_sent=true;
                $scope.items[userIdx]=newItem;
            }
        }
    });

    this.requestTeamChange=function(user) {
        var config={
            template:'/app-view/team-change-request-popup',
            classes: {'modal-offer':true},
            userTeamRequest: {
                user: {name: (user.first_name ? user.first_name:'')+' '+(user.last_name ? user.last_name:'')},
                second_user_id: user.id
            }
        };

        modal.show(config);
    };

    $scope.$watch('filter',function(new_value, old_value) {
        if (new_value != old_value) {
            $scope.urlState.users.pageNum = 1;
            self.load();
        }
    }, true);






});