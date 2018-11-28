app.directive('adsense', function() {
    return {
        restrict: 'E',
        replace: true,
        scope : {
            adClient : '@',
            adSlot : '@',
            adFormat : '@',
            adLayout: '@',
            adLayoutKey: '@',
            inlineStyle : '@',
            viewportMinWidth: '@',
            viewportMaxWidth: '@'
        },
        template: '<div data-ng-show="adFitInViewport" class="ads">'+
        '<ins data-ng-class="{\'adsbygoogle\': adFitInViewport}" '+
        'data-ad-client="{{adClient}}" '+
        'data-ad-slot="{{adSlot}}" '+
        'ng-attr-data-ad-format="{{adFormat || undefined}}" '+
        'ng-attr-data-ad-layout="{{adLayout || undefined}}" '+
        'ng-attr-data-ad-layout-key="{{adLayoutKey || undefined}}" '+
        'style="{{inlineStyle}}" '+
        '"></ins></div>',
        controller: ['adsense', '$scope', '$window', '$timeout', function (adsense, $scope, $window, $timeout) {

            $scope.adFitInViewport = true;
            if(($scope.viewportMinWidth && $window.innerWidth < $scope.viewportMinWidth) ||
                ($scope.viewportMaxWidth && $window.innerWidth > $scope.viewportMaxWidth)) {
                $scope.adFitInViewport = false;
                return;
            }

            if (!adsense.isAlreadyLoaded) {
                var s = document.createElement('script');
                s.type = 'text/javascript';
                s.src = adsense.url;
                s.async = true;
                document.body.appendChild(s);

                adsense.isAlreadyLoaded = true;
            }

            $timeout(function(){
                (window.adsbygoogle = window.adsbygoogle || []).push({});
            });
        }]
    };
});