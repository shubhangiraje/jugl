app.controller('OfferRequestPaymentComplaintPopupCtrl', function ($scope,$rootScope,jsonPostDataPromise,$state,modal,gettextCatalog) {

    $scope.paymentComplaint=modal.data.paymentComplaint;

    this.save=function() {
        $scope.paymentComplaint.saving = true;
            jsonPostDataPromise('/api-offer-request/payment-complaint-save',{paymentComplaint:$scope.paymentComplaint})
                .then(function (data) {
                    if (data.result===true) {
                        $rootScope.$broadcast('activityListUpdate',data);
                        modal.alert({message:gettextCatalog.getString('Mahnung erfolgreich gesendet')});
                        $scope.paymentComplaint.saving=false;
                    } else {
                        modal.alert({message:data.result});
                        $scope.paymentComplaint.saving=false;
                    }
                },function(data) {
                    $scope.paymentComplaint.saving = false;
                });

    };

});
