app.controller('ActionResultCtrl', function ($scope,$state,actionResultData) {
    angular.extend($scope,actionResultData);
    if (angular.isObject(actionResultData.redirect)) {
        $state.go(actionResultData.redirect.route,actionResultData.redirect.params);
    }
});
