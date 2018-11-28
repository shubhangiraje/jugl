app.controller('ManageNetworkCtrl', function ($scope,manageNetworkData,jsonPostDataPromise,$timeout) {

    angular.extend($scope, manageNetworkData);

    var self = this;
    $scope.state={pageNum:1};
    $scope.filter = {
        name: ''
    };

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getUsersLog(function(data) {
            scrollLoadCallback(data.log.hasMore);
        });
    };

    $scope.$watch('filter',function(newValue,oldValue) {
        if (newValue!=oldValue) {
            $scope.log.items=[];
            $scope.state.pageNum = 1;
            self.getUsersLog();
        }
    },true);


    this.getUsersLog = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonPostDataPromise('/api-manage-network/list',{
            filter: $scope.filter,
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