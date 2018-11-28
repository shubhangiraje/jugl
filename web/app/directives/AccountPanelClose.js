app.directive('accountPanelClose', function($document) {
    return {
        restrict: 'A',
        link: function(scope, element, $attrs) {
            function account_panel_close_click(event) {
                $('.account-page').removeClass('show-account-panel');
            }

            $(element).on('click', account_panel_close_click);

            scope.$on('$destroy',function() {
                $(element).off('click', account_panel_close_click);
            });
        }
    };
});
