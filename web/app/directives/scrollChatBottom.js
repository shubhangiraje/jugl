app.directive('scrollChatBottom', function($timeout) {

    return {
        restrict: 'A',
        link: function($scope, element, $attrs) {

            element.bind('jsp-initialised', function(event, isScrollable) {
                scrollBottom();
            });

            function scrollBottom() {
                $timeout(function() {
                    var pane = element.data('jsp');
                    pane.scrollToBottom();
                });
            }

        }
    };

});