app.controller('NewsCtrl', function ($scope,newsData,jsonDataPromise,$timeout,$anchorScroll,$rootScope) {

    angular.extend($scope, newsData);

    var self = this;
    $scope.state={pageNum:1};

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getNewsLog(function(data) {
            scrollLoadCallback(data.log.hasMore);
        });
    };

    this.getNewsLog = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-news/list',{
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
                    self.getNewsLog(callback);
                }
                callback(data);
            });
    };

    if($rootScope.readMoreNewsId) {
        $timeout(function() {
            $anchorScroll('news'+$rootScope.readMoreNewsId);
            $rootScope.readMoreNewsId = null;
        });
    }




});