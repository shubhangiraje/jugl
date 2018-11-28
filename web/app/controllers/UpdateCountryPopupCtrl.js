app.controller('UserCountryUpdatePopupCtrl', function ($scope,modal,jsonPostDataPromise,$rootScope,userStatus) {

    $scope.userCountryUpdateForm = {};
    $scope.userCountryUpdateForm=angular.copy($scope.modalService.data.userData);
    $scope.countries=angular.copy($scope.modalService.data.countries);

    function replaceCountryId(dataObj,userId,countryId,flag) {
        var idx;

        if (dataObj instanceof Array) {
            for(idx in dataObj) {
                replaceCountryId(dataObj[idx],userId,countryId,flag);
            }
            return;
        }

        if (typeof dataObj == 'object' && dataObj !== null) {
            if (dataObj.id == userId && dataObj.flag !== '') {
                dataObj.country_id = countryId;
                dataObj.flag = flag;
            }

            for (idx in dataObj) {
                if (dataObj.hasOwnProperty(idx) && typeof dataObj[idx] == 'object') {
                    replaceCountryId(dataObj[idx], userId, countryId, flag);
                }
            }
        }
    }

    this.save = function() {
        $scope.userCountryUpdateForm.saving = true;
        jsonPostDataPromise('/api-user/update-country',{user_id:$scope.userCountryUpdateForm.user_id, country_id: $scope.userCountryUpdateForm.country_id})
            .then(function (data) {
                if (data.result) {
                    $scope.userCountryUpdateForm.saving = false;
                    replaceCountryId($rootScope.updateCountryDataObject,$scope.userCountryUpdateForm.user_id,$scope.userCountryUpdateForm.country_id,data.flag);
                    delete $rootScope.updateCountryDataObject;
                }
                modal.hide();
            },function(data) {
                $scope.userCountryUpdateForm.saving = false;
            });

    };


});
