app.directive('accountPanel', function($document) {
    return {
        restrict: 'A',
        link: function(scope, element, $attrs) {
            function account_panel_button_click(event) {
                $('.account-page').toggleClass('show-account-panel');
            }

            $(element).bind('click', account_panel_button_click);

            scope.$on('$destroy',function() {
                $(element).unbind('click', account_panel_button_click);
            });
        }
    };
});
