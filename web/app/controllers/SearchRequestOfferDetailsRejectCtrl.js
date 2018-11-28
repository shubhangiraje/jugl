app.controller('SearchRequestOfferDetailsRejectCtrl', function ($scope,jsonPostDataPromise,$state,modal) {

    $scope.reject={};

    this.save=function() {
        $scope.reject.saving = true;
        jsonPostDataPromise('/api-search-request-offer/reject',{id:modal.data.searchRequestOffer.id, reject: $scope.reject})
            .then(function (data) {
                if (data.result) {
                    modal.hide();
                    $state.go('searches.myList');
                } else {
                    angular.extend($scope,data);
                    $scope.reject.saving = false;
                }
            },function(data) {
                $scope.reject.saving = false;
            });

    };

});
