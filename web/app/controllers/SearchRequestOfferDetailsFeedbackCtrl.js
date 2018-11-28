app.controller('SearchRequestOfferDetailsFeedbackCtrl', function ($scope,jsonPostDataPromise,$state,modal,userStatus) {

    $scope.feedback={};

    this.save=function() {
        $scope.feedback.saving = true;
            jsonPostDataPromise('/api-search-request-offer/feedback',{id:modal.data.searchRequestOffer.id,feedback:$scope.feedback})
                .then(function (data) {
                    if (data.result) {
                        modal.hide();
                        userStatus.update();
                        $state.go('searches.myList');
                    } else {
                        angular.extend($scope,data);
                        $scope.feedback.saving = false;
                    }
                },function(data) {
                    $scope.feedback.saving = false;
                });

    };

});
