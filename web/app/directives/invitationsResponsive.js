app.directive('invitationsResponsive', function($document) {
    return {
        restrict: 'A',
        link: function($scope, element, $attrs) {
            $(element).bind('list-builded.ReStable', function() {
                var $list = $(element).siblings('.responsive-table');

                $('.invitations-resend', $list).unbind('click');
                $('.invitations-resend', $list).click(function() {
                    var index = $(this).closest('ul').find('>li').index($(this).closest('li'));
                    $('tbody tr:eq(' + index + ') button.invitations-resend', element).click();
                });

                $('.invitations-delete', $list).unbind('click');
                $('.invitations-delete', $list).click(function() {
                    var index = $(this).closest('ul').find('>li').index($(this).closest('li'));
                    $('tbody tr:eq(' + index + ') button.invitations-delete', element).click();
                });


            });
            $scope.$on('$destroy',function() {
                $(element).unbind('list-builded.ReStable');
                $('.responsive-table .invitations-resend').unbind('click');
                $('.responsive-table .invitations-delete').unbind('click');
            });
        }
    };
});
