app.directive('selectClick', function($document, $timeout) {
    return {
        restrict: 'A',
        link: function(scope, element, $attrs) {

            var selects = element.find('select');
            selects.selectpicker({ noneSelectedText: ''});

            function dropdownOptionClick(event) {
                $('.bootstrap-select.open', element).removeClass('open');
                if(element.hasClass('filter-select')) {
                    $('.filter-select-refresh').selectpicker('refresh');
                }
            }

            function documentClick() {
            }

            $(element).on('click.selectClick', '.dropdown-menu li', dropdownOptionClick);
            $document.bind('click.selectClick', documentClick);

            scope.$on('$destroy',function() {
                $('.dropdown-menu li', element).unbind('click.selectClick');
                $document.unbind('click.selectClick');
            });

        }
    };
});
