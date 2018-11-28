var userStatusService=angular.module('UserStatusService', []);

userStatusService.factory('userStatus',function(jsonDataPromise,$interval,$rootScope,$q,serverTime,modal,$state,$timeout,userSettings,$pixel,gettextCatalog) {
    var factory={};

    factory.status={
        first_name: '',
        last_name: ''
    };

    var prevPacket;

    factory.update=function() {
        var defer=$q.defer();

        jsonDataPromise('/api-user/status').then(function(data){
            serverTime.setCurrentServerTime(data.serverTime);

            var oldStatus=angular.copy(factory.status);

            angular.extend(factory.status,data);

            if (data) {
                if (data.packet === '' && prevPacket !== '' && !data.not_force_packet_selection) {
                    $state.go('registrationPayment');
                }

                if (data.packet !== '' && prevPacket === '') {
                    $state.go('dashboard');
                }

                prevPacket = data.packet;
            }

            if (data && data.showTeamleaderFeedbackNotification && !modal.isShowing()) {

                jsonDataPromise('/api-user-team-feedback/update').then(function(data){
                    if (data===false) return;
                    data.feedback.isNotification=true;
                    var config={
                        template:'/app-view/user-team-feedback-popup',
                        classes: {'modal-offer':true},
                    };

                    angular.extend(config,data);

                    modal.show(config);
                });
            }

            $timeout(function() {
                if (data.status == 'REGISTERED') {
                    var config = {
                        template:'/app-view/app-download-popup',
                        classes: {'modal-app-download':true},
                        noTransparentBackground: true
                    };
                    modal.show(config);
                }
                /*if(data.status == 'LOGINED') {
                    $state.go('profile-fillup');
                }*/
                if (data.status == 'ACTIVE' && oldStatus.status == 'LOGINED') {
                    $state.go('dashboard');
                }
            },300);


            if (!$rootScope.isShowValidationPhoneNotification) {
                $rootScope.isShowValidationPhoneNotification = (new Date()).getTime();
            }

            if ($rootScope.isShowValidationPhoneNotification<((new Date()).getTime() - 2*60*1000)) {
                if (data.packet !== null && data.packet !=='' && data.status == 'ACTIVE' && data.validation_phone_status!='VALIDATED' && data.birthday && data.city) {
                    jsonDataPromise('/api-user-validation-phone-notification/get').then(function(data){
                        if (!data.result) return;
                        var config = {
                            template:'/app-view/validation-phone-popup'
                        };
                        modal.show(config);
                    });
                }
            }

            if (data.pixel_registration_notified===0) {
                if (!$rootScope.isPixelRegistrationNotified) {
                    $pixel.track('CompleteRegistration', {
                        content_name: data.first_name + ' ' + data.last_name,
                        status: data.status
                    });
                }
                $rootScope.isPixelRegistrationNotified = true;
                jsonDataPromise('/api-user/update-pixel-registration-notified').then(function(data){});
            }

            if (data.is_update_country_after_login===0) {
                jsonDataPromise('/api-user/auto-update-country').then(function(data){
                    modal.alert({message: gettextCatalog.getString('Ihre Sprach- und Landeinstellungen wurden automatisch angepasst')});
                    factory.update();
                });
            }

            defer.resolve(true);
        });

        return defer.promise;
    };

    factory.update();

    // update status explicitly one time per minute
    $interval(factory.update,5*60*1000);

    $rootScope.$on('statusUpdateRequested',function($event,options){
        factory.update().then(function() {
            if (options && options.sound && userSettings.get('sounds') !== 0) {
                var soundMessage = new Audio('/static/sound/'+options.sound);
                soundMessage.play();
            }
        });
    });

    return factory;
});
