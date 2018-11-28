app.directive('cloudspongeCall', function($timeout, $document) {
    return {
        restrict: 'A',
        link: function($scope, element, $attrs, ngModel) {
            function cloudspongeCall() {
                return cloudsponge.launch($attrs.cloudspongeCall);
            }

            $(element).bind('click', cloudspongeCall);

            $scope.$on('$destroy',function() {
                $(element).unbind('click', cloudspongeCall);
            });
        }
    };
});
