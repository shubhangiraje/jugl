app.directive('isFlash', function() {
    return {
        restrict: 'A',
        link: function(scope, element, $attrs) {

            var isFlashEnabled = false;
            // All browsers, except IE
            if (typeof(navigator.plugins)!="undefined" && typeof(navigator.plugins["Shockwave Flash"])=="object") isFlashEnabled = true;
            else if (typeof  window.ActiveXObject !="undefined") {
                //IE
                try {
                    if (new ActiveXObject("ShockwaveFlash.ShockwaveFlash")) isFlashEnabled = true;
                } catch(e) {}
            }

            if(!isFlashEnabled)
                element.hide();

        }
    };
});
