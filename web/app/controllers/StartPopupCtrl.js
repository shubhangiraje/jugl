app.controller('StartPopupCtrl', function ($rootScope,userStatus,$scope,jsonPostDataPromise,modal) {

    this.saveShowStartPopup = function() {
        jsonPostDataPromise('/api-user/save-show-start-popup').then(
            function (data, status, headers, config) {
                if(data.result) {
                    modal.hide();
					userStatus.update();
					if ($rootScope.status.show_friends_invite_popup === 0) {
						$rootScope.showFriendsInvitePopup();
					}
					
                }
            }
        );
    };

});