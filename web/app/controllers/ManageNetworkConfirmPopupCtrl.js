app.controller('ManageNetworkConfirmPopupCtrl', function ($scope,jsonPostDataPromise,$state,modal,$rootScope) {

    $scope.users=$scope.modalService.data.users;
    $scope.users.saving = false;

    this.save=function() {
        $scope.users.saving = true;
        jsonPostDataPromise('/api-manage-network/save',{
            moveId:$scope.users.src_id,
            dstId:$scope.users.dst_id
        }).then(function (data) {
            $scope.users.saving = false;
            modal.hide();
            modal.alert({message: data.result},function(){
                $state.go('dashboard');
            });

        },function(){
            $scope.users.saving = false;
        });
    };

});
