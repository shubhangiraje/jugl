app.directive('zeroClipboard', function($document) {
    return {
        restrict: 'A',
        link: function(scope, element, $attrs) {
            var clip = new ZeroClipboard.Client();
            clip.setHandCursor(true);
            clip.setText($('input', element).val());
            clip.glue($('span', element)[0], $('div', element)[0]);

            scope.$watch($attrs.zeroClipboard, function(newValue){
                clip.setText(newValue);
            });
        }
    };
});
