app.controller('UserFeedbackResponsePopupCtrl', function ($scope,jsonPostDataPromise,$state,modal,$rootScope) {

    $scope.feedback=angular.copy($scope.modalService.data.feedback);

    this.save=function() {
        $scope.feedback.saving = true;
            jsonPostDataPromise('/api-user-feedback/response-save',{feedback:$scope.feedback})
                .then(function (data) {
                    if (data.result) {
                        modal.hide();
                        $rootScope.$broadcast('UserFeedbackResponse',data.feedback);
                    } else {
                        angular.extend($scope,data);
                        $scope.feedback.saving = false;
                    }
                },function(data) {
                    $scope.feedback.saving = false;
                });

    };

});
