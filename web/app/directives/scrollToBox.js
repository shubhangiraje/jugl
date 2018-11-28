app.directive('scrollToBox', function($timeout) {

    return {
        restrict: 'A',
        link: function($scope, element, $attrs) {

            element.on('click', function() {
                console.log('sefsefsef');
            });

        }
    };

});