app.controller('ToolbarCtrl', function ($scope,userStatus,modal) {
    $scope.status=userStatus.status;

    this.langPopup = function() {
        modal.show({
            template: '/app-view/lang-popup',
            classes: {'modal-lang':true}
        });
    };


});
