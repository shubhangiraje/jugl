app.controller('ProfileUpdateCtrl', function ($state, $scope, profileData, modal, $http, Uploader, userStatus, jsonPostDataPromise, gettextCatalog, $rootScope, $interval, $anchorScroll, $timeout) {

    angular.extend($scope, profileData);

    var self = this;

    $scope.userProfile = {};
    $scope.uploader=Uploader(['avatarBig','photoSmall']);
	
	var timer;
	$scope.new_code_interval = 0;

    $scope.isWelcome=$state.includes('welcome');

    $scope.avatarUploadOptions={
        onSuccess: function(response,status,headers) {
            $scope.user.avatar_file_id=response.id;
            $scope.user.avatarFile=response;
        }
    };

    $scope.fileUploadOptions={
        onSuccess: function(response,status,headers) {
            $scope.user.photos.push(response);
        }
    };

    this.deleteFile=function(id) {
        for(var i in $scope.user.photos) {
            if ($scope.user.photos[i].id==id) {
                $scope.user.photos.splice(i,1);
                break;
            }
        }

        if($scope.uploader.queue.length !== 0) {
            $scope.uploader.queue.length = $scope.uploader.queue.length-1;
        }

    };

    this.needHelp = function() {
        jsonPostDataPromise('/api-profile/need-help', {}).then(
            function(data) {
                modal.alert({message:gettextCatalog.getString('Vielen Dank, deine Hilfe-Anfrage wurde an unser Team weitergeleitet. Wir werden uns mit dir in Kürze in Verbindung setzen.')});
            }
        );
    };

    this.save = function () {
        $scope.userProfile.saving = true;
        $http.post('/api-profile/save-profile', {user: $scope.user})
            .error(function (data, status, headers, config) {
                $scope.userProfile.saving = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.userProfile.saving = false;
                if (angular.isArray(data.user.$allErrors) && data.user.$allErrors.length > 0) {
                    $scope.user = data.user;
                    $scope.userProfile.saving = false;
                    return;
                }
                userStatus.update();
                $state.go('userProfile',{id:userStatus.status.id});
            });
    };

    this.deleteProfile = function() {
        if(userStatus.status.validation_phone_status!='VALIDATED' && userStatus.status.parent_registration_bonus===0) {
            modal.confirmation({message:gettextCatalog.getString('Willst du wirklich dein Profil löschen?')},function(result){
                if (!result) return;
                jsonPostDataPromise('/api-profile/delete').then(function(data) {
                    if(data.result) {
                        window.location.href ='/site/logout';
                    } else {
                        modal.alert({message:gettextCatalog.getString('Um Dein Profil zu löschen, stelle bitte einen schriftlichen Antrag auf Löschung mit der eMail Adresse mit der Du Dich registriert hast. Bitte mit Vor- und Nachnamen, vollständiger Anschrift und deiner Unterschrift an juglapp@gmx.de. Wir bitten um Verständnis, dass wir dies fordern, um Missbrauch zu vermeiden.')});
                    }
                });
            });
        } else {
            modal.alert({message:gettextCatalog.getString('Um Dein Profil zu löschen, stelle bitte einen schriftlichen Antrag auf Löschung mit der eMail Adresse mit der Du Dich registriert hast. Bitte mit Vor- und Nachnamen, vollständiger Anschrift und deiner Unterschrift an juglapp@gmx.de. Wir bitten um Verständnis, dass wir dies fordern, um Missbrauch zu vermeiden.')});
        }
    };

    this.autoSave = function () {
        $http.post('/api-profile/auto-save-profile', {user: $scope.user})
            .error(function (data, status, headers, config) {
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                userStatus.update();
            });
    };


    $scope.$watch('user',function(new_value, old_value) {
        if (new_value != old_value) {

            if(new_value.email == old_value.email &&
                new_value.oldPassword == old_value.oldPassword &&
                new_value.newPassword == old_value.newPassword &&
                new_value.newPasswordRepeat == old_value.newPasswordRepeat &&
                new_value.company_name == old_value.company_name &&
                new_value.company_manager == old_value.company_manager &&
                new_value.impressum == old_value.impressum &&
                new_value.agb == old_value.agb &&
                new_value.city == old_value.city &&
                new_value.validation_phone == old_value.validation_phone &&
                new_value.validation_code_form == old_value.validation_code_form &&
                new_value.validation_phone_status == old_value.validation_phone_status &&
                new_value.is_company_name == old_value.is_company_name ) {

                self.autoSave();
            }
        }
    }, true);

    this.sendValidationPhone = function() {
        $scope.sendingSms=true;
        jsonPostDataPromise('/api-profile/send-validation-phone', {validation_phone: $scope.user.validation_phone}).
            then(function(data) {
                $scope.sendingSms=false;
                $scope.userProfile.validationPhone = data.validation;
                if(data.result === true) {
                    $scope.userProfile.validation_phone_status = data.validation.validation_phone_status;
                    $scope.user.validation_phone_status = data.validation.validation_phone_status;
                }
				$scope.callTimeoutButtonValidation();
				$scope.code_clicked=true;
            },function(error) {
                $scope.sendingSms=false;
            }
        );
    };

    this.sendValidationCode = function() {	
        jsonPostDataPromise('/api-profile/send-validation-code', {validation_code_form: $scope.user.validation_code_form}).
            then(function(data) {
				$scope.userProfile.validationCode = data.validation;
            }
        );
    };
		
	$scope.callTimeoutButtonValidation=function(){
		$scope.code_clicked=true;
			$scope.new_code_interval=20;
			timer=$interval( function(){
				console.log($scope.new_code_interval);
			$scope.new_code_interval--;
				if($scope.new_code_interval===0){
				 $interval.cancel(timer);	
				 $scope.code_clicked=false;
				}
			},1000);
		};

    if($rootScope.gotoValidationPhone) {
        $timeout(function() {
            $anchorScroll('validationPhone');
            $rootScope.gotoValidationPhone = null;
        });
    }



});