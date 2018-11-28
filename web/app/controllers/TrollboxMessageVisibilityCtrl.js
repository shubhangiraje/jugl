app.controller('TrollboxMessageVisibilityCtrl', function ($scope,jsonPostDataPromise,$state,modal) {

    $scope.trollboxMessage = {};
    $scope.trollboxMessage = modal.data.trollboxMessage;
    $scope.trollboxCategoryList = modal.data.trollboxCategoryList;
    $scope.country_ids = modal.data.country_ids;
    $scope.trollboxMessage.saving = false;
    $scope.trollboxMessage.error = false;

    this.send = function() {
        $scope.trollboxMessage.saving = true;

        if($scope.trollboxMessage.visible_for_all!=1 && $scope.trollboxMessage.visible_for_followers!=1 && $scope.trollboxMessage.visible_for_contacts!=1) {
            $scope.trollboxMessage.error = true;
            $scope.trollboxMessage.saving = false;
        } else {
            jsonPostDataPromise('/api-trollbox/send-message', {trollboxMessage: $scope.trollboxMessage, country_ids: $scope.country_ids})
                .then(function (data) {
                    $scope.trollboxMessage.saving = false;
                    modal.data.setTrollbox(data);
                    modal.hide();
                }, function(data) {
                    $scope.trollboxMessage.saving = false;
                });
        }
    };

    this.showAppDownloadPopup = function () {
        var config = {
            template:'/app-view/app-download-popup',
            classes: {'modal-app-download':true},
            isDownloadInfoPopup: true
        };
        modal.show(config);
    };

});
