app.controller('UserSearchCtrl', function ($scope,$rootScope,userSearchData,jsonDataPromise,$state) {

    var self=this;

    angular.extend($scope, userSearchData);

    try {
        $scope.urlState=angular.fromJson($state.params.urlState);
    } catch (e) {
        $scope.urlState={
            users: {
                filter: {
				country_ids:[]
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

    $scope.filter=$scope.urlState.users.filter;
    $scope.state={};

    this.loadMore=function(scrollLoadCallback) {
        $scope.urlState.users.pageNum++;
        self.load(function(data) {
            scrollLoadCallback(data.users.hasMore);
        });
    };

    this.load=function(callback) {
        callback = callback || function() {};
        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-user-search/users',{urlState:angular.toJson($scope.urlState)}).then(function (data) {
            $scope.state.loading=false;

            if ($scope.urlState.users.pageNum > 1) {
                data.users.users = $scope.users.users.concat(data.users.users);
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
            $scope.urlState.users.pageNum = 1;
            self.load();
        }
    }, true);


	$scope.labels=$rootScope.status.labels;	

});
