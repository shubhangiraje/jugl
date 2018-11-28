app.controller('OfferUpdateCtrl', function ($scope,jsonPostDataPromise,$state,modal,$rootScope) {

    this.save=function() {
        $scope.offerUpdateForm.saving = true;
        jsonPostDataPromise('/api-offer/update-save',{offerUpdateForm:$scope.offerUpdateForm})
            .then(function (data) {
                if (data.result) {

                    $rootScope.$broadcast('offerUpdate', data.offer);

                    modal.hide();
                } else {
                    angular.extend($scope,data);
                    $scope.offerUpdateForm.saving = false;
                }
            },function(data) {
                $scope.offerUpdateForm.saving = false;
            });
    };

    this.goPayIn=function() {
        modal.hide();
        $state.go('funds.payin');
    };

});
