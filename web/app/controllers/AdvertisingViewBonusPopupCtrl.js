app.controller('AdvertisingViewBonusPopupCtrl', function ($scope,jsonDataPromise,modal,$interval,userStatus) {
	$scope.data = {
		advertising: modal.data.advertising,
		secondsLeft: modal.data.advertising.popup_interval
	};
    var intervalPromise=$interval(function(){
        $scope.data.secondsLeft--;
        if ($scope.data.secondsLeft===0) {
			$interval.cancel(intervalPromise);
			jsonDataPromise('/api-advertising/accept-view-bonus',{id: $scope.data.advertising.id}).then(function (data){
			});
            modal.hide();
			userStatus.update();
        }
    },1000);

    $scope.$on('$destroy',function() {
        $interval.cancel(intervalPromise);
    });
});
