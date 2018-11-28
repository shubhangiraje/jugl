app.controller('AppDownloadPopupCtrl', function ($rootScope,userStatus,$scope,deviceDetector,jsonPostDataPromise,modal) {

    $scope.device = {
        os: deviceDetector.os,
        isDesktop: deviceDetector.isDesktop()
    };

    $scope.appDownloadData = modal.data;

    this.saveDesktop = function() {
        jsonPostDataPromise('/api-profile/save-desktop', {value: deviceDetector.isDesktop()}).then(
            function (data, status, headers, config) {
                if(data.result) {
                    modal.hide();
					userStatus.update();
					if ($rootScope.status.show_start_popup === 0) {
						$rootScope.showStartPopup();
					}
                }
            }
        );
    };

});