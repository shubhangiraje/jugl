app.controller('SettingsCtrl', function ($scope, userProfileSettingsData, jsonPostDataPromise, userSettings) {
    $scope.data = {
        settings: {},
        saving: false
    };
    angular.extend($scope.data.settings, userProfileSettingsData);

    this.save = function() {
        $scope.data.saving = true;
        userSettings.save($scope.data.settings).then(
            function() {
                $scope.data.saving = false;
            },
            function() {
                $scope.data.saving = false;
            }
        );
    };
});
