app.controller('FundsPayInResultCtrl', function ($state,$scope,fundsPayInResultData,$interval,jsonPostDataPromise,$stateParams) {
    angular.extend($scope, fundsPayInResultData);

    var intervalPromise=$interval(function(){
        jsonPostDataPromise('/api-funds-pay-in-result/index',{
            requestId:$stateParams.requestId,
            returnStatus:$stateParams.result
        }).then(function(data){
            angular.extend($scope,data);
        },function(){

        });
    },5*1000);

    $scope.$watch('payInRequest.status',function(newValue) {
        if (newValue!='AWAITING') {
            $interval.cancel(intervalPromise);
        }
    });

});
