app.controller('UserTeamFeedbackPopupCtrl', function ($scope,jsonPostDataPromise,$state,modal,userStatus) {

    $scope.feedback=angular.copy($scope.modalService.data.feedback);

    this.save=function() {
        $scope.feedback.saving = true;
            jsonPostDataPromise('/api-user-team-feedback/save',{feedback:$scope.feedback})
                .then(function (data) {
                    if (data.result) {
                        modal.hide();
                    } else {
                        angular.extend($scope,data);
                        console.log($scope.feedback);
                        $scope.feedback.saving = false;
                    }
                },function(data) {
                    $scope.feedback.saving = false;
                });

    };

    this.close=function() {
        if ($scope.feedback.isNotification) {
            jsonPostDataPromise('/api-user-team-feedback/notification-rejected')
                .then(function (data) {
                    if (data.result) {
                        modal.hide();
                    }
                });
        } else {
            modal.hide();
        }
    };

});
