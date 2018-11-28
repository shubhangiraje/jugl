app.controller('FriendsInvitationInviteCtrl', function ($scope,friendsInvitationInviteData,$http,modal,$window,gettextCatalog) {

    angular.extend($scope, friendsInvitationInviteData);

    $scope.inviteEmailsList = '';
    $scope.inviteByEmail = {
        text: gettextCatalog.getString('Hallo, schau Dir diese neue App an und gib mir bitte ein Feedback. Liebe Grüße\n{link}\n\n\nUnd hier noch das Video dazu:\nhttps://youtu.be/X5h0JSLQP-Y')
    };

    $scope.inviteBySMS = {
        text: gettextCatalog.getString('Hallo, schau Dir diese neue App an und gib mir bitte ein Feedback. Liebe Grüße\n{link}\n\n\nUnd hier noch das Video dazu:\nhttps://youtu.be/X5h0JSLQP-Y')
    };

    this.inviteByEmail = function () {
        $scope.inviteByEmail.saving = true;
        $http.post('/api-friends-invitation-invite/invite-by-email', {inviteByEmail: $scope.inviteByEmail})
            .error(function (data, status, headers, config) {
                $scope.inviteByEmail.saving = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.inviteByEmail = data.inviteByEmail;
                $scope.inviteByEmail.saving = false;
            });
    };

    this.inviteBySMS = function () {
        $scope.inviteBySMS.saving = true;
        $http.post('/api-friends-invitation-invite/invite-by-sms', {inviteBySMS: $scope.inviteBySMS})
            .error(function (data, status, headers, config) {
                $scope.inviteBySMS.saving = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.inviteBySMS = data.inviteBySMS;
                $scope.inviteBySMS.saving = false;
            });
    };

});
