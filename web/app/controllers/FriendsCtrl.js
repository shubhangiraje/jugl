app.controller('FriendsCtrl', function ($scope, friendsData, jsonDataPromise, $state, $http, modal, gettextCatalog) {

    angular.extend($scope, friendsData);

    var self = this;

    try {
        $scope.urlState=angular.fromJson($state.params.urlState);
    } catch (e) {
        $scope.urlState={
            friends: {
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

    $scope.filter = $scope.urlState.friends.filter;
    $scope.state={};

    this.loadMore=function(scrollLoadCallback) {
        $scope.urlState.friends.pageNum++;
        self.load(function(data) {
            scrollLoadCallback(data.friends.hasMore);
        });
    };

    this.load=function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-friends/friends',{urlState:angular.toJson($scope.urlState)}).then(function (data) {
            $scope.state.loading=false;

            if ($scope.urlState.friends.pageNum > 1) {
                data.friends.users = $scope.friends.users.concat(data.friends.users);
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
            $scope.urlState.friends.pageNum = 1;
            self.load();
        }
    }, true);

    this.deleteFromFriends = function(friendId) {
        modal.confirmation({message:gettextCatalog.getString('You really want to delete this user from friends?')},function(result){
            if (!result)
                return false;

            var params = {
                urlState:angular.toJson($scope.urlState),
                friendId:friendId
            };

            $http.post('/api-friends/delete-friend', params)
            .error(function (data, status, headers, config) {
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                angular.extend($scope,data);
            });
        });
    };
});
