app.controller('FriendsInvitationPopupCtrl', function ($scope, userStatus, jsonPostDataPromise, modal) {

    this.saveFriendsInvitationPopup = function () {
        jsonPostDataPromise('/api-user/save-friends-invitation-popup').then(
            function (data, status, headers, config) {
                if (data.result) {
                    modal.hide();
                    userStatus.update();
                }
            }
        );
    };

});