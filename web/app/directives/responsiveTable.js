app.directive('responsiveTable', function($document) {
    return {
        restrict: 'A',
        link: function($scope, element, $attrs) {
            $(element).ReStable({
                maxWidth: 768,
                rowHeaders: true
            });
            $scope.$on('$destroy',function() {
                $(element).ReStable('destroy');
            });
        }
    };
});
