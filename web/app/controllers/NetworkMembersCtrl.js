app.controller('NetworkMembersCtrl', function ($scope, userStatus, networkMembersData, jsonPostDataPromise, jsonDataPromise, $state, $http, modal) {

    angular.extend($scope, networkMembersData);

    var self = this;

    if (userStatus.status.new_network_members>0) {
        jsonPostDataPromise('/api-network-members/mark-as-seen').then(function(){userStatus.update();});
    }

    try {
        $scope.urlState=angular.fromJson($state.params.urlState);
    } catch (e) {
        $scope.urlState={
            networkMembers: {
                filter: {
                    sort: 'dt',
                    statusFilter: 'all',
                    nameFilter: ''
                },
                pageNum: 1
            }
        };
    }

    $scope.$watch('urlState',function(newValue,oldValue){
        if (oldValue!=newValue) {
            $state.transitionTo($state.current.name, {urlState: angular.toJson(newValue)}, { location: 'replace', inherit: true, relative: $state.$current, notify: false });
        }
    },true);

    $scope.filter = $scope.urlState.networkMembers.filter;
    $scope.state={};

    this.loadMore=function(scrollLoadCallback) {
        $scope.urlState.networkMembers.pageNum++;
        self.load(function(data) {
            scrollLoadCallback(data.networkMembers.hasMore);
        });
    };

    this.load=function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-network-members/network-members',{urlState:angular.toJson($scope.urlState)}).then(function (data) {
            $scope.state.loading=false;

            if ($scope.urlState.networkMembers.pageNum > 1) {
                data.networkMembers.users = $scope.networkMembers.users.concat(data.networkMembers.users);
            }

            angular.extend($scope, data);

            if ($scope.state.modifiedWhileLoading) {
                $scope.state.modifiedWhileLoading=false;
                self.load(callback);
            }
            callback(data);
        });
    };

    $scope.$watch('filter',function(new_value, old_value) {
        if (new_value != old_value) {
            $scope.urlState.networkMembers.pageNum = 1;
            self.load();
        }
    }, true);
});
