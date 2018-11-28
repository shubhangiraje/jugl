app.controller('FundsPayOutCtrl', function ($scope,fundsPayOutData, Uploader, modal, $http, $timeout, $anchorScroll, $rootScope) {

    angular.extend($scope, fundsPayOutData);

    $scope.payOutRequest={};
    $scope.itemUploadPhoto2File = false;

    $scope.uploader=Uploader(['validationSmall']);

    $scope.validationPhoto1UploadOptions={
        onSuccess: function(response,status,headers) {
            $scope.user.validation_photo1_file_id=response.id;
            $scope.user.validationPhoto1File=response;
            $scope.itemUploadPhoto1File = false;
            $scope.user.saving = false;
        },
        onBeforeUpload: function() {
            $scope.itemUploadPhoto1File = true;
        }
    };

    $scope.validationPhoto2UploadOptions={
        onSuccess: function(response,status,headers) {
            $scope.user.validation_photo2_file_id=response.id;
            $scope.user.validationPhoto2File=response;
            $scope.itemUploadPhoto2File = false;
            $scope.user.saving = false;
        },
        onBeforeUpload: function() {
            $scope.itemUploadPhoto2File = true;
        }
    };

    $scope.validationPhoto3UploadOptions={
        onSuccess: function(response,status,headers) {
            $scope.user.validation_photo3_file_id=response.id;
            $scope.user.validationPhoto3File=response;
            $scope.itemUploadPhoto3File = false;
            $scope.user.saving = false;
        },
        onBeforeUpload: function() {
            $scope.itemUploadPhoto3File = true;
        }
    };

    this.savePayOutRequest = function () {
        $scope.payOutRequest.saving = true;
        $http.post('/api-funds-pay-out/save-pay-out-request', {payOutRequest: $scope.payOutRequest})
            .error(function (data, status, headers, config) {
                $scope.payOutRequest.saving = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.payOutRequest.saving = false;
                if (angular.isArray(data.payOutRequest.$allErrors) && data.payOutRequest.$allErrors.length>0) {
                    $scope.payOutRequest = data.payOutRequest;
                    $scope.payOutRequest.saving = false;
                    return;
                } else {
                    $scope.payOutRequest.saved = true;
                    $scope.payOutRequest.saving = false;
                }
            });
    };

    this.saveValidation = function () {

        $scope.user.saving = true;
        $http.post('/api-funds-pay-out/save-validation', {user: $scope.user})
            .error(function (data, status, headers, config) {
                $scope.user.saving = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.user.saving = false;
                if (angular.isObject(data.user)) {
                    $scope.user = data.user;
                    return;
                }

            });
    };

    if($rootScope.gotoValidationPassport) {
        $scope.user.validation_type = 'PHOTOS';
        $timeout(function() {
            $anchorScroll('validationPassport');
            $rootScope.gotoValidationPassport = null;
        });
    }



});
