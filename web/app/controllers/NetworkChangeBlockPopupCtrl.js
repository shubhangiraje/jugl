app.controller('NetworkChangeBlockPopupCtrl', function ($scope,jsonPostDataPromise,$state,modal) {

    $scope.stickUserRequest = {
        user_id: $scope.modalService.data.userId,
        saving: false
    };

    this.send=function() {
        $scope.stickUserRequest.saving = true;

        jsonPostDataPromise('/api-user-profile/save-stick-request',{stickUserRequest:$scope.stickUserRequest}).then(function (data) {
            if (data.result) {
                $scope.modalService.data.hideButton();
                modal.alert({message:data.result});
            } else {
                angular.extend($scope,data);
                $scope.stickUserRequest.saving = true;
            }
        },function(){
            $scope.stickUserRequest.saving = false;
        });

    };

});
