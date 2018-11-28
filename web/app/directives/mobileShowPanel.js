app.directive('mobileShowPanel', function($document) {
    return {
        restrict: 'A',
        link: function(scope, element, $attrs) {
            function otherInfoClick(event) {
                $(element).closest('.mobile-panel').siblings('.container.main').slideToggle();
            }

            $(element).bind('click', otherInfoClick);

            scope.$on('$destroy',function() {
                $(element).unbind('click', otherInfoClick);
            });
        }
    };
});
