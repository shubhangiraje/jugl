app.controller('ProfileFillupCtrl', function ($state,$scope,Uploader,profileFillupData,gettextCatalog,jsonPostDataPromise,userStatus,$rootScope) {

    angular.extend($scope, profileFillupData);

    $scope.uploader=Uploader(['avatarBig']);

    $scope.avatarUploadOptions={
        onSuccess: function(response,status,headers) {
            $scope.user.avatar_file_id=response.id;
            $scope.user.avatarFile=response;
        }
    };

    this.save = function () {
        $scope.user.saving = true;
        jsonPostDataPromise('/api-profile/save-profile-fillup', {user: $scope.user}).then(
            function (data, status, headers, config) {
                $scope.user.saving = false;
                if (!data.result) {
                    $scope.user = data.user;
                    $scope.user.saving = false;
                    return;
                }
                userStatus.update();
                $state.go('dashboard');
            },
            function (data, status, headers, config) {
                $scope.user.saving = false;
                userStatus.update();
            }
        );
    };
    this.later = function () {
        $scope.user.saving = true;
        jsonPostDataPromise('/api-profile/later-profile-fillup', {user: $scope.user}).then(
            function (data, status, headers, config) {  
                userStatus.update();
                $state.go('dashboard');
            }  
        );
    };



});
