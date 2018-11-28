app.controller('FriendsInvitationInvitationsCtrl', function ($scope, friendsInvitationInvitationsData, $http, modal, jsonDataPromise, jsonPostDataPromise,gettextCatalog) {

    angular.extend($scope, friendsInvitationInvitationsData);

    $scope.resendInvitation={};
    $scope.deleteInvitation={};

    $scope.invitationsState = {pageNum: 1};

    this.moreInvitationsLoad = function(scrollLoadCallback) {
        jsonDataPromise('/api-friends-invitation-invitations/invitations',{
            pageNum: ++$scope.invitationsState.pageNum,
            sort: '',
            statusFilter: ''
        }).then(function (data) {
            data.invitations.invitations = $scope.invitations.invitations.concat(data.invitations.invitations);
            angular.extend($scope, data);
            scrollLoadCallback(data.invitations.hasMore);
        });
    };

    this.resendInvitation = function (invitationId) {
        $scope.resendInvitation.saving = false;
        $http.post('/api-friends-invitation-invitations/resend-invitation', {invitationId: invitationId})
            .error(function (data, status, headers, config) {
                $scope.resendInvitation.saving = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.resendInvitation.saving = false;
                modal.alert({'message':data.resendInvitation.message});
                if (data.invitation) {
                    for(var i in $scope.invitations.invitations) {
                        var invitation=$scope.invitations.invitations[i];
                        if (invitation.id===invitationId) {
                            angular.extend(invitation,data.invitation);
                            $scope.invitations.invitations[i]=angular.copy(invitation);
                        }
                    }
                }
            });
    };


    this.deleteInvitation = function(invitationId) {

        modal.confirmation({message:gettextCatalog.getString('Möchtest du die Einladung löschen?')},function(result) {
            if (!result) {
                return;
            }

            jsonPostDataPromise('/api-friends-invitation-invitations/delete-invitation',{invitationId:invitationId})
                .then(function (data) {
                    if (data.result===true) {
                        for (var idx in $scope.invitations.invitations) {
                            if ($scope.invitations.invitations[idx].id == invitationId) {
                                $scope.invitations.invitations.splice(idx, 1);
                            }
                        }
                    }
                });
        });

    };


});
