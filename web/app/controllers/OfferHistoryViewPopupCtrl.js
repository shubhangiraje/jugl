app.controller('OfferHistoryViewPopupCtrl', function ($scope,$state,modal,jsonDataPromise,gettextCatalog) {

    $scope.log=modal.data.log;

    var self = this;
    $scope.state={pageNum:1};

    $scope.dateTimeFormat=gettextCatalog.getString("dd.MM.yyyy 'um' HH:mm");

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getHistory(function(data) {
            scrollLoadCallback(data.log.hasMore);
        });
    };

    this.getHistory = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-offer-view-log/history',{
            id: $scope.log.user.id,
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
                    self.getHistory(callback);
                }
                callback(data);
            });
    };


});