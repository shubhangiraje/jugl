app.controller('FundsPayInDataCtrl', function ($state,$scope,$http,modal,$window,fundsPayInDataData,$sce) {
    angular.extend($scope, fundsPayInDataData);

    //$scope.iframeUrl=$sce.trustAsResourceUrl($scope.iframeUrl);
});
