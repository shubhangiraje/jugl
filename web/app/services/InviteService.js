var inviteService=angular.module('InviteService', []);

inviteService.factory('invite',function($state, gettextCatalog,modal,jsonPostDataPromise,userStatus,$timeout,$rootScope) {
    var factory={};

    factory.invite=function(data,refresh) {
        factory.invitePerson=data;

        if(userStatus.status.packet == 'VIP' || userStatus.status.packet == 'VIP_PLUS') {
            modal.confirmation({
                message: gettextCatalog.getString('<p>Wenn Du auf \'Einverstanden\' klickst, bist Du verpflichtet, dieses Mitglied zu betreuen, d.h. Kontakt zu ihm aufzunehmen und ihm Jugl.net zu erklären.</p>'+
                    '<p>Erkläre ihm Jugl.net daher genau, damit Dein Netzwerk ein aktives Mitglied gewinnt.</p>'),
                buttons: [
                    {
						caption: gettextCatalog.getString('Einverstanden'),
                        class: 'btn-line',
                        onClick: function() {
                            modal.loading = true;
                            jsonPostDataPromise('/api-invitation/become-member-invite', {id: data.id})
                                .then(function (data) {
                                    modal.loading = false;
                                    modal.hide();

                                    if (data.winner) {
                                        $rootScope.$broadcast('BecomeMemberInviteWinner',data.winner);
                                    }

                                    $timeout(function(){
                                        if (data.message) {
                                            modal.alert({message:data.message.replace(/\n/g,'<br/>')});
                                        }
                                        if (data.refresh && refresh) {
                                            refresh();
                                        }
                                    });
									userStatus.update();
                                });
                        }
                    },
                    {
                        caption: gettextCatalog.getString('Cancel'),
                        class: 'cancel',
                        onClick: function() {
                            modal.hide();
                        }
                    }
                ]
            });
        } else {
            modal.confirmation({
                message: gettextCatalog.getString('Diese Funktion ist nur für Premium-Mitglieder.'),

                buttons: [
                    {
                        caption: gettextCatalog.getString('Jetzt PremiumPlus-Mitglied werden'),
                        class: 'btn-line',
                        onClick: function() {
                            modal.hide();
                            $state.go('packetUpgrade');
                        }
                    },
                    {
                        caption: gettextCatalog.getString('Ok'),
                        class: 'cancel',
                        onClick: function() {
                            modal.hide();
                        }
                    }
                ]
            });
        }


    };

    return factory;
});
