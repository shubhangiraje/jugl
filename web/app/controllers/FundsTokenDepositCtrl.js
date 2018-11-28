app.controller('FundsTokenDepositCtrl', function ($scope, fundsTokenDepositData, jsonDataPromise, jsonPostDataPromise, modal, gettextCatalog) {

    var self=this;

    angular.extend($scope, fundsTokenDepositData);

    $scope.state={pageNum:1};

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getResults(function(data) {
            scrollLoadCallback(data.log.hasMore);
        });
    };

    this.getResults = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-funds-token-deposit/log',{
            pageNum:$scope.state.pageNum,
        }).then(function (data) {
                $scope.state.loading=false;

                if ($scope.state.pageNum > 1) {
                    data.log.items = $scope.log.items.concat(data.log.items);
                }

                angular.extend($scope, data);

                if ($scope.state.modifiedWhileLoading) {
                    $scope.state.modifiedWhileLoading=false;
                    self.getResults(callback);
                }
                callback(data);
            });
    };

    this.payoutTypeToggle=function(item) {
        item.saving=true;
        jsonPostDataPromise('/api-funds-token-deposit/payout-type-toggle',{
            id: item.id,
        }).then(function (data) {
            item.saving=false;

            item.payout_type=data.payout_type;

            var message=gettextCatalog.getString("Auszahlung wurde zu")+" ";
            message=message+(item.payout_type=='TOKENS' ? gettextCatalog.getString("Tokens"):gettextCatalog.getString("Jugls"))+" ";
            message=message+gettextCatalog.getString("geändert");

            modal.alert({message:message});
        },function(){
            item.saving=false;
        });
    };
});
