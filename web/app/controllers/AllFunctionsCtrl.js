app.controller('AllFunctionsCtrl', function ($scope, modal, gettextCatalog, $state, $rootScope, deviceDetector) {

    this.accountDeletePopup = function() {
        modal.alert({message:gettextCatalog.getString('Bitte sende uns eine Email mit der Begründung, warum du den Account löschen möchtest an juglapp@gmx.de')});
    };

    this.goViewParamFilter = function(view, filter) {
        $rootScope.paramFilterView = filter;
        $state.go(view);
    };

});