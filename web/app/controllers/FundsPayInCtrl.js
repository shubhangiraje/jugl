app.controller('FundsPayInCtrl', function ($state,$scope,$http,modal,$window,fundsPayInData) {

    angular.extend($scope, fundsPayInData);

    $scope.payInRequest={
    };

    this.savePayInRequest = function () {
        $scope.payInRequest.saving = true;
        $http.post('/api-funds-pay-in/save-pay-in-request', {payInRequest: $scope.payInRequest})
            .error(function (data, status, headers, config) {
                $scope.payInRequest.saving = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.payInRequest.saving = false;
                if (angular.isArray(data.payInRequest.$allErrors) && data.payInRequest.$allErrors.length>0) {
                    $scope.payInRequest = data.payInRequest;
                    $scope.payInRequest.saving = false;
                    return;
                } else {
                    $scope.payInRequest.saved = true;
                    $scope.payInRequest.saving = false;
                    $state.go('.data',{requestId:data.payInRequest.id});
                }
            });
    };

});
