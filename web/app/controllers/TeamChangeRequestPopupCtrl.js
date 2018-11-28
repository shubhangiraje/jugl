app.controller('TeamChangeRequestPopupCtrl', function ($scope,jsonPostDataPromise,$state,modal,$rootScope) {

    $scope.userTeamRequest=angular.copy($scope.modalService.data.userTeamRequest);

    this.save=function() {
        $scope.userTeamRequest.saving = true;
            jsonPostDataPromise('/api-user-team-request/save-referral-to-parent',{userTeamRequest:$scope.userTeamRequest})
                .then(function (data) {
                    if (data.result) {
                        $rootScope.$broadcast('userTeamRequestAdded',$scope.userTeamRequest.second_user_id);
                        modal.hide();
                    } else {
                        angular.extend($scope,data);
                        $scope.userTeamRequest.saving = false;
                    }
                },function(data) {
                    $scope.userTeamRequest.saving = false;
                });
    };

});
