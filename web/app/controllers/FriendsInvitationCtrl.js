app.controller('FriendsInvitationCtrl', function ($scope,$state,friendsInvitationData) {

    angular.extend($scope,friendsInvitationData);

    if ($state.is('friendsInvitation')) {
        $state.go('friendsInvitation.invite');
    }

});
