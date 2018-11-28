app.controller('OfferDetailsRequestCtrl', function ($scope,jsonPostDataPromise,$state,modal) {

    $scope.offerRequest={'offer_id':modal.data.offer.id};

    this.save=function() {
        $scope.offerRequest.saving = true;
            jsonPostDataPromise('/api-offer-request/save',{offerRequest:$scope.offerRequest})
                .then(function (data) {
                    if (data.result) {
                        modal.hide();
                        $state.go('offers.search');
                    } else {
                        angular.extend($scope,data);
                        $scope.offerRequest.saving = false;
                    }
                },function(data) {
                    $scope.offerRequest.saving = false;
                });

    };

});
