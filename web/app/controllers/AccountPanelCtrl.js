app.controller('AccountPanelCtrl', function ($scope,userStatus,modal,gettextCatalog,jsonPostDataPromise,$location) {
    $scope.status=userStatus.status;
    // this.deleteProfile = function() {
    //     modal.confirmation({message:gettextCatalog.getString('Möchtest Du Dein Profil tatsächlich löschen?'), buttons: [
    //             {
    //                 caption: gettextCatalog.getString('Profil löschen'),
    //                 class: 'ok btn-line',
    //                 onClick: function() {
    //                     modal.hide();
    //                     jsonPostDataPromise('/api-profile/delete').then(function(data) {
    //                         if(data.result) {
    //                             window.location.href ='/site/logout';
    //                         }
    //                     });
    //                 }
    //             },
    //             {
    //                 caption: gettextCatalog.getString('Cancel'),
    //                 class: 'cancel',
    //                 onClick: function() {
    //                     modal.hide();
    //                 }
    //             }
    //         ]},function(){
    //     });
    // };


    // this.deleteProfile = function() {
    //     modal.alert({message:gettextCatalog.getString('Um Dein Profil zu löschen, stelle bitte einen schriftlichen Antrag auf Löschung mit der eMail Adresse mit der Du Dich registriert hast. Bitte mit Vor- und Nachnamen, vollständiger Anschrift und deiner Unterschrift an juglapp@gmx.de. Wir bitten um Verständnis, dass wir dies fordern, um Missbrauch zu vermeiden.')});
    // };
});