app.controller('UserFeedbackUpdateCtrl', function ($scope,jsonPostDataPromise,$state,modal,$rootScope) {

    $scope.feedback={};

    this.save=function() {
        $scope.feedback.saving = true;
            jsonPostDataPromise('/api-user-feedback/save',{feedback:$scope.feedback})
                .then(function (data) {
                    if (data.result) {

                        var params = {
                            search_request_offer_id: $scope.feedback.search_request_offer_id,
                            offer_request_id: $scope.feedback.offer_request_id,
                            rating: $scope.feedback.rating
                        };

                        $rootScope.$broadcast('ratingUpdateParams', params);
                        $rootScope.$broadcast('updateUserEvents',data.events);

                        modal.hide();
                    } else {
                        angular.extend($scope,data);
                        $scope.feedback.saving = false;
                    }
                },function(data) {
                    $scope.feedback.saving = false;
                });
    };


    this.counterSave=function() {
        $scope.feedback.saving = true;
        jsonPostDataPromise('/api-user-feedback/counter-save',{feedback:$scope.feedback})
            .then(function (data) {
                if (data.result) {

                    var params = {
                        search_request_offer_id: $scope.feedback.search_request_offer_id,
                        offer_request_id: $scope.feedback.offer_request_id,
                        rating: $scope.feedback.rating
                    };

                    $rootScope.$broadcast('counterRatingUpdateParams', params);
                    $rootScope.$broadcast('updateUserEvents',data.events);

                    modal.hide();
                } else {
                    angular.extend($scope,data);
                    $scope.feedback.saving = false;
                }
            },function(data) {
                $scope.feedback.saving = false;
            });
    };

});
