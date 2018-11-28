app.directive('messengerWindowResize', function($document, $window, $timeout) {
    return {
        restrict: 'A',
        link: function($scope, element, $attrs) {
            function windowResizeHandler() {
                $scope.messenger.setWindowSize({
                    width: $(window).width(),
                    height: $(window).height()
                });
                if (!$scope.$$phase) $scope.$apply();
            }

            $(window).bind('resize.messengerResize', windowResizeHandler);
            windowResizeHandler();

            $scope.$on('$destroy',function() {
                $(window).unbind('resize.messengerResize');
            });
        }
    };
});
