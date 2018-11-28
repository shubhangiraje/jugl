app.directive('juglCurrency', function($document) {
    return {
        restrict: 'E',
        template: '<span class="symbol-jugl">&#xe623;</span>',
        replace: true
    };
});
