app.directive('userInfoMenu', function($document) {
    return {
        restrict: 'A',
        link: function(scope, element, $attrs) {
            function user_name_click(event) {
                $(this).siblings('.user-info-menu').toggleClass('showed');
                var stopPropagation = event.stopPropagation ? event.stopPropagation() : (event.cancelBubble=true);
            }

            function document_click() {
                $('.user-info-menu').removeClass('showed');
            }

            $(element).bind('click', user_name_click);
            $document.bind('click', document_click);

            scope.$on('$destroy',function() {
                $(element).unbind('click', user_name_click);
                $document.unbind('click', document_click);
            });
        }
    };
});
