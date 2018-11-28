app.directive('messengerPopup', function($document, $window, $timeout) {
    return {
        restrict: 'A',
        link: function($scope, element, $attrs) {
            $timeout(function() {
                $(element).css('right', -$(element).outerWidth()).show();
            });

            $scope.$on('messenger.conversation.user_changed', function(event, args) {
                $timeout(function() {
                    $(element).css('right', -$(element).outerWidth());
                });
            });
        }
    };
});
