var adsenseService=angular.module('AdsenseService', []);

adsenseService.factory('adsense',function() {
    var factory={
        url: 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
        isAlreadyLoaded: false
    };
    return factory;
});
