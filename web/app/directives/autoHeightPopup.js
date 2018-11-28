app.directive('autoHeightPopup', function($timeout) {

    return {
        restrict: 'A',
        link: function($scope, element, $attrs) {
            $timeout(function() {
                var container = element.closest('.modal-info');
                element.css('height', container.height()+'px');
            });
        }
    };

});