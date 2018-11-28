app.controller('OfferMyRequestsCtrl', function ($scope, offerMyRequestsData, $timeout, $state, $stateParams, jsonDataPromise) {

    var self=this;

    angular.extend($scope, offerMyRequestsData);

    $scope.filter={
        status:''
    };

    $scope.state={pageNum:1};

    $scope.$watch('filter',function(newValue,oldValue) {
        if (newValue != oldValue) {
            $scope.state.pageNum = 1;
            self.getResults();
        }
    },true);

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getResults(function(data) {
            scrollLoadCallback(data.results.hasMore);
        });
    };

    this.getResults = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-offer-my-request/list',{filter:$scope.filter,pageNum:$scope.state.pageNum})
            .then(function (data) {
                $scope.state.loading=false;

                if ($scope.state.pageNum > 1) {
                    data.results.items = $scope.results.items.concat(data.results.items);
                }

                angular.extend($scope, data);

                if ($scope.state.modifiedWhileLoading) {
                    $scope.state.modifiedWhileLoading=false;
                    self.getResults(callback);
                }
                callback(data);
            });
    };
});