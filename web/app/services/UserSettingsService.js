var userSettingsService=angular.module('UserSettingsService', []);

userSettingsService.factory('userSettings',function($localStorage, jsonDataPromise, jsonPostDataPromise, $q) {
    var factory={},
        localStorageKey = 'userProfileSettings';

    // Defaults
    factory.settings = {
        local: {
            sounds: 1
        },
        remote: {
            setting_off_send_email: 0
        }
    };

    factory.update = function() {
        var defer = $q.defer();
        jsonDataPromise('/api-user-profile/settings')
            .then(function(data) {
                delete data.serverTime;
                factory.updateLocalSettings();
                angular.extend(factory.settings.remote, data.settings);
                defer.resolve(factory.settings);
            });
        return defer.promise;
    };

    factory.updateLocalSettings = function() {
        angular.extend(factory.settings.local, $localStorage[localStorageKey]);
    };

    factory.get = function(name) {
        if (factory.settings.remote[name] !== undefined) {
            return factory.settings.remote[name];
        }
        factory.updateLocalSettings();
        if (factory.settings.local[name] !== undefined) {
            return factory.settings.local[name];
        }
        return '';
    };

    factory.getAll = function() {
        factory.updateLocalSettings();
        return factory.settings;
    };

    factory.save = function(data) {
        var defer = $q.defer();
        data = angular.copy(data);
        jsonPostDataPromise('/api-user-profile/update-settings', {data: data.remote})
            .then(function (response) {
                $localStorage[localStorageKey] = data.local;
                angular.extend(factory.settings.local, data.local);
                angular.extend(factory.settings.remote, data.remote);
                defer.resolve(factory.settings);
            });
        return defer.promise;
    };

    factory.update();

    return factory;
});
