app.directive('messengerContactsResize', function($document, $window, $timeout) {
    return {
        restrict: 'A',
        link: function($scope, element, $attrs) {
            function windowResizeHandler() {
                $(element).css({
                    height: ''
                });

                var windowHeight = $(window).height(),
                    messengerHeight = $('.account-messenger').outerHeight();

                if (messengerHeight > windowHeight) {
                    $(element).css({
                        height: $(element).outerHeight() - (messengerHeight - windowHeight)
                    });
                }
            }

            setTimeout(windowResizeHandler, 100);

            $(window).on('resize', windowResizeHandler);

            $scope.$on('$destroy',function() {
                $(window).off('resize', windowResizeHandler);
            });
        }
    };
});
