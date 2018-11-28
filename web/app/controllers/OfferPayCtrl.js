app.controller('OfferPayCtrl', function ($scope,$state,offerPayData,jsonPostDataPromise,userStatus) {

    if (offerPayData.result===false) {
        $state.go('activityList');
        return;
    }

    angular.extend($scope, offerPayData);

    $scope.$watch('pay.payment_method',function(newValue){
        if (newValue=='POD') {
            delete $scope.pay.delivery_address;
        }
    });

    $scope.$watch('pay',function(newValue,oldValue){
        var oldStrVal='';

        function strVal(val) {
            if (!angular.isString(val)) return '';
            return val;
        }

        if (oldValue) {
            oldStrVal=
                strVal(oldValue.address_street)+
                strVal(oldValue.address_house_number)+
                strVal(oldValue.address_zip)+
                strVal(oldValue.address_city);
        }
        var newStrVal='';
        if (newValue) {
            newStrVal=
                strVal(newValue.address_street)+
                strVal(newValue.address_house_number)+
                strVal(newValue.address_zip)+
                strVal(newValue.address_city);
        }

        if (oldStrVal==='' && newStrVal!=='') {
            $scope.pay.delivery_address='address';
        }
    },true);

    $scope.data={
        showAllBankDatas:false,
        showAllDeliveryAddresses:false
    };

    $scope.pay={
        offer_request_id:$scope.offer.request.id
    };

    this.pay = function () {
        $scope.pay.saving = true;
        jsonPostDataPromise('/api-offer/pay-save', {pay: $scope.pay}).then(function(data) {
            if (data.result===true) {
                userStatus.update();
                $state.go('activityList');
                return;
            }
            $scope.pay = data.pay;
            $scope.pay.saving = false;
        },function(){
            $scope.pay.saving = false;
        });
    };

});
