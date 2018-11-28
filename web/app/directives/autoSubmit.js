app.directive('autoSubmit', function($timeout) {
    return {
        restrict: 'A',
        link: function(scope, element, $attrs) {
            $timeout(function() {
                element.submit();
            },0);
        }
    };
});
