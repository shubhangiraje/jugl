app.controller('FaqsCtrl', function ($scope,faqsData,jsonDataPromise,$timeout,$anchorScroll,$rootScope) {

    angular.extend($scope, faqsData);

    var self = this;
    $scope.state={pageNum:1};

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getFaqsLog(function(data) {
            scrollLoadCallback(data.log.hasMore);
        });
    };

    this.getFaqsLog = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-faq/list',{
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
                    self.getFaqsLog(callback);
                }
                callback(data);
            });
    };

    if($rootScope.readMoreFaqId) {
        $timeout(function() {
            $anchorScroll('faq'+$rootScope.readMoreFaqId);
            $rootScope.readMoreFaqId = null;
        });
    }




});