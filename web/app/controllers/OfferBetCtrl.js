app.controller('OfferBetCtrl', function ($scope,$state,$stateParams,jsonPostDataPromise,modal,betData) {

    var self=this;

    angular.extend($scope,betData);

    this.bet = function () {
        $scope.offerBet.saving = true;
        jsonPostDataPromise('/api-offer-request/bet', {offerBet: $scope.offerBet}).then(function(data) {
            $scope.offerBet.saving = false;
            if (data.result===true) {
                $state.go('offers.myRequests');
                return;
            }
            $scope.offerBet = data.offerBet;
            if (data.price_is_not_best===true) {
                modal.show({
                    template:'/app-view/offers-bet-popup',
                    classes: {'modal-offer-bet':true},
                    ok: function() {
                        $scope.offerBet.dont_check_price=true;
                        modal.hide();
                        self.bet();
                    },
                    cancel: function() {
                        modal.hide();
                    },
                    best_price: data.best_price,
                    price: $scope.offerBet.price
                });
            }
        },function(){
            $scope.offerBet.saving = false;
        });
    };

});
