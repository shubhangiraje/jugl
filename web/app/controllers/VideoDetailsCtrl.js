/* NVII-MEDIA
 * START - Video Detail controller
 * Erweitert Robert
 */
app.controller('VideoDetailsCtrl', function ($scope,$sce,$state,videoDetailsData,jsonDataPromise,modal,$timeout,$interval,userStatus,jsonPostDataPromise,messengerService,gettextCatalog) {
	angular.extend($scope, videoDetailsData);

    //$scope.video.description=$scope.video.description;

	function formatNumber(number,digits,thousands_sep,decimal_sep) {

        /*
        function toFixed(num, fixed) {
            if (fixed===null) return num+"";
            fixed = fixed || 0;
            var pow = Math.pow(10, fixed);
            return (Math.floor(num * pow) / pow).toFixed(fixed);
        }
        */


         // limit max number of digits
         function toFixed(number,digits) {
             if (digits===null) return number+"";
             return number.toFixed(digits);
         }


        if (typeof thousands_sep === 'undefined') thousands_sep=' ';
        if (typeof decimal_sep === 'undefined') decimal_sep=',';

        var integerPart = parseInt(toFixed(Math.abs(+number || 0),digits)) + "";
        var thousandsGroups = (integerPart.length) > 3 ? integerPart.length % 3 : 0;

        var fraction;
        var fractionPart=Math.abs(number - integerPart)+1e-6;

        if (digits===null) {
            // take not more than 6 fractional digits
            fraction=toFixed(fractionPart,6).slice(2).replace(/0+$/,'');
        } else {
            fraction=toFixed(fractionPart,digits).slice(2);
        }

        price= number < 0 ? "-" : "" +
        (thousandsGroups ? integerPart.substr(0, thousandsGroups) + thousands_sep : "") +
        integerPart.substr(thousandsGroups).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep) +
        (fraction ? decimal_sep + fraction : "");

        return price;
    }
	
	$scope.video_state.video_total_bonus = formatNumber($scope.video_state.video_total_bonus,2,'.',',');

	
    $scope.status=userStatus.status;

	$scope.video.complete = false;
	
	if(typeof($scope.video_user.id) == 'undefined'){
		$scope.$watch('video.complete', function($newVal, $oldVal){
			if($newVal === true){
				var timeoutPromise=null;
				if (angular.isString($scope.video_state.video_total_bonus)) {
					timeoutPromise=$timeout(function(){
						var config={
							template:'/app-view/videos-details-view-bonus-popup',
							video: $scope.video
						};

						modal.show(config);
					},0);
				}
			}
		});
	}
	
    var intervalPromise = null;
    intervalPromise=$interval(function(){
        jsonDataPromise('/api-video/get-count-video-view',{id: $scope.video.video_id}).then(function (data) {
            $scope.video_state.video_total_view = data.video_total_view;
			$scope.video_state.video_total_bonus = formatNumber(data.video_total_bonus,2,'.',',');
        },function(){});
    },10*1000);

    $scope.$on('$destroy',function() {
        $interval.cancel(intervalPromise);
    });
});
