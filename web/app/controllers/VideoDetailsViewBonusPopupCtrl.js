
/* NVII-MEDIA
 * START - Video Detail View Bonus Popup
 */
app.controller('VideoDetailsViewBonusPopupCtrl', function ($scope,jsonDataPromise,$state,modal,$interval,userStatus) {
    $scope.video=modal.data.video;
	$scope.status=userStatus.status;
	
	$scope.data={secondsLeft:10};

    var intervalPromise=$interval(function(){
        $scope.data.secondsLeft--;
        if ($scope.data.secondsLeft===0) {
            modal.hide();
        }
    },1000);
	
	 $scope.$on('$destroy',function() {
        $interval.cancel(intervalPromise);
    });
	
	
    this.accept=function() {
		
		jsonDataPromise('/api-video/set-video-balance',{id: $scope.video.video_id}).then(function (data) {
		});
		
				modal.hide();
				userStatus.update();
		
		/*$scope.status.balance = parseFloat($scope.status.balance)+parseFloat($scope.video.bonus);	*/

    };

});