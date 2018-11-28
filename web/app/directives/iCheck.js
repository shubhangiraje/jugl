app.directive('iCheck', function($timeout) {
    return {
        require: 'ngModel',
        restrict: 'A',
        link: function($scope, element, $attrs, ngModel) {
            //$(element.addClass('hidden'));
            //return $timeout(function() {
            var value;
            value = $attrs.value;

            $scope.$watch($attrs.ngModel, function(newValue){
                $(element).iCheck('update');
            });

            return $(element).iCheck({
                checkboxClass: 'icheckbox_square-grey',
                radioClass: 'iradio_square-grey'
            }).on('ifChanged', function(event) {
                if ($(element).attr('type') === 'checkbox' && $attrs.ngModel) {
                    $scope.$apply(function() {
                        return ngModel.$setViewValue(event.target.checked);
                    });
                }
                if ($(element).attr('type') === 'radio' && $attrs.ngModel) {
                    return $scope.$apply(function() {
                        return ngModel.$setViewValue(value);
                    });
                }
            });
            //});
        }
    };
});
