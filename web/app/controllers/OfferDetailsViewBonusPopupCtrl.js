app.controller('OfferDetailsViewBonusPopupCtrl', function ($scope,jsonPostDataPromise,modal,$interval,userStatus) {

    $scope.offer=modal.data.offer;

    $scope.data={secondsLeft:10};

    var intervalPromise=$interval(function(){
        $scope.data.secondsLeft--;
        if ($scope.data.secondsLeft===0) {
            modal.hide();
        }
    },1000);

    $scope.$on('$destroy',function() {
        $interval.cancel(intervalPromise);
    });

    this.accept=function() {
        $scope.data.saving = true;
            jsonPostDataPromise('/api-offer/accept-view-bonus',{offer_id:$scope.offer.id,code:$scope.offer.viewBonusCode})
                .then(function (data) {
                    if (data.result===true) {
                        modal.hide();
                        userStatus.update();
                    } else {
                        if (angular.isString(data.result)) {
                            modal.alert({message: data.result});
                        }
                    }
                });
    };

});
