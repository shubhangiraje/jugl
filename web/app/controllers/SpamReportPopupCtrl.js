app.controller('SpamReportPopupCtrl', function ($scope,jsonPostDataPromise,$state,modal,$rootScope) {

    $scope.okCallback=modal.data.spamReport.okCallback;
    delete modal.data.spamReport.okCallback;
    $scope.spamReport=modal.data.spamReport;

    this.save=function() {
        $scope.spamReport.saving = true;
            jsonPostDataPromise('/api-spam-report/save',{spamReport:$scope.spamReport})
                .then(function (data) {
                    if (data.result) {
                        modal.hide();
                        $rootScope.$broadcast('spamReported');
                        if ($scope.okCallback) {
                            $scope.okCallback();
                        }

                    } else {
                        angular.extend($scope,data);
                        $scope.spamReport.saving = false;
                    }
                },function(data) {
                    $scope.spamReport.saving = false;
                });

    };

});
