app.controller('ValidationPhonePopupCtrl', function ($rootScope,userStatus,$scope,jsonPostDataPromise,modal,$state) {

    this.close = function () {
        jsonPostDataPromise('/api-user-validation-phone-notification/add').then(
            function (data) {
                if(data.result) {
                    modal.hide();
                }
            }
        );
    };

    this.gotoValidationPhone = function() {
        modal.hide();
        $rootScope.gotoValidationPhone = true;
        $state.go('profile');
    };

});