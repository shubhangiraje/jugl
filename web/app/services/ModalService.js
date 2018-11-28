var modalService=angular.module('ModalService', []);

modalService.factory('modal',function($sce, $http, $templateCache, $window, gettextCatalog) {
    var factory={
        isShow: false,
        isShowInfo: false,
        data: {},
        dataInfo: {},
        loading: false
    };

    factory.isShowing=function() {
        return factory.isShow || factory.isShowInfo;
    };

    factory.show=function(scope) {
        factory.data = angular.copy(scope);
        factory.isShow = true;
    };

    factory.hide=function() {
        factory.isShow = false;
        factory.data = {};
    };

    factory.showInfo=function(scope) {
        factory.dataInfo = angular.copy(scope);
        factory.isShowInfo = true;
    };

    factory.hideInfo=function() {
        factory.isShowInfo = false;
        factory.dataInfo = {};
    };


    factory.confirmation=function(config,callback) {
        var scope=angular.copy(config);
        scope.message=$sce.trustAsHtml(scope.message);
        if (!scope.title) scope.title=gettextCatalog.getString('Confirm the action');

        callback = callback || function() {};
        scope.buttons = scope.buttons || [];
        if (!scope.buttons.length) {
            scope.buttons = [
                {			
                    caption: scope.successBtn ? scope.successBtn : gettextCatalog.getString('Ok'),
                    class: 'ok',
                    onClick: function() {
                        factory.hide();
                        callback(true);
                    }
                },
                {
                    caption: scope.cancelBtn ? scope.cancelBtn : gettextCatalog.getString('Cancel'),
                    class: 'cancel',
                    onClick: function() {
                        factory.hide();
                        callback(false);
                    }
                }
            ];
        }

        factory.show(scope);
    };

    factory.alert=function(config,callback) {
        var scope=angular.copy(config);
        scope.message=$sce.trustAsHtml(scope.message);
        if (!scope.title) scope.title=null;

        callback = callback || function() {};
        scope.buttons = scope.buttons || [];
        if (!scope.buttons.length) {
            scope.buttons = [
                {
                    caption: 'Ok',
                    class: 'ok',
                    onClick: function() {
                        factory.hide();
                        callback(true);
                    }
                }
            ];
        }
        factory.show(scope);
    };

    factory.error=function(config,callback) {
        var scope=angular.copy(config);
        scope.message=$sce.trustAsHtml(scope.message);
        if (!scope.title) scope.title=gettextCatalog.getString('Problem');

        callback = callback || function() {};
        scope.buttons = scope.buttons || [];
        if (!scope.buttons.length) {
            scope.buttons = [
                {
                    caption: 'Ok',
                    class: 'ok',
                    onClick: function() {
                        factory.hide();
                        callback(true);
                    }
                }
            ];
        }
        factory.show(scope);
    };

    factory.httpError=function(data, status, headers, config) {
        if (status>0) {
            if (status==403) {
                $window.location.href='/';
                return;
            } else {
                factory.error({
                    title: gettextCatalog.getString('Error'),
                    message: gettextCatalog.getString('Error while performing server request (code ' + status + ')')
                });
            }
        } else {
            factory.error({
                title: gettextCatalog.getString('Problem'),
                message: gettextCatalog.getString('Can\'t connect to server. Check your internet connection')
            });
        }
    };

    return factory;
});
