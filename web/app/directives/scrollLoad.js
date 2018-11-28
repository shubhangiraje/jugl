app.directive('scrollLoad', function($document, $window) {
    return {
        restrict: 'A',
        scope: {
            scrollLoad: '&',
            scrollLoadVisible: '@',
            scrollLoadHasMore: '='
        },
        link: function(scope, element, $attrs) {
            var windowElement = angular.element($window),
                loading = false,
                hasMore = true;

            scope.scrollLoadVisible = parseFloat(scope.scrollLoadVisible || 1);

            function scrollCallback(isHasMore) {
                loading = false;
                hasMore = isHasMore;
            }

            function windowScrollHander() {
                if(scope.scrollLoadHasMore) {
                    hasMore = true;
                }
                if (loading || !hasMore || !scope.scrollLoadHasMore)
                    return;
                var windowBottom = windowElement.scrollTop() + windowElement.height(),
                    elementOffset = $(element).offset();
                if (elementOffset.top + $(element).outerHeight() * scope.scrollLoadVisible <= windowBottom) {
                    loading = true;
                    scope.$apply(function() {
                        scope.scrollLoad()(scrollCallback);
                    });
                }
            }

            windowElement.on('scroll.scrollLoad', windowScrollHander);
            windowElement.on('resize.scrollLoad', windowScrollHander);

            scope.$on('$destroy',function() {
                windowElement.off('scroll.scrollLoad', windowScrollHander);
                windowElement.off('resize.scrollLoad', windowScrollHander);
            });
        }
    };
});
