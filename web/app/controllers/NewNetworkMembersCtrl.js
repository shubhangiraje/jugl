app.controller('NewNetworkMembersCtrl', function ($scope,NewNetworkMembersData,jsonDataPromise) {

    angular.extend($scope, NewNetworkMembersData);

    var self = this;
    $scope.state={pageNum:1};

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getUsersLog(function(data) {
            scrollLoadCallback(data.log.hasMore);
        });
    };

    this.getUsersLog = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-network-members/new-users',{
            pageNum: $scope.state.pageNum
        })
            .then(function (data) {
                $scope.state.loading=false;

                if ($scope.state.pageNum > 1) {
                    data.log.items = $scope.log.items.concat(data.log.items);
                }

                angular.extend($scope, data);

                if ($scope.state.modifiedWhileLoading) {
                    $scope.state.modifiedWhileLoading=false;
                    self.getUsersLog(callback);
                }
                callback(data);
            });
    };


});