app.controller('InviteMyListCtrl', function ($scope, userStatus,$state,InviteMyListCtrlData,jsonDataPromise,invite,$rootScope,$timeout) {

    angular.extend($scope, InviteMyListCtrlData);

    var self = this;
	var timer = null;
	
    $scope.state={pageNum:1};

    $scope.countryIds = $scope.currentCountry[0].id;
	$scope.user_status={
		delay_invited_member:null
	};

	$scope.callDelayInvitedMemberTimeout = function() {
        timer=$timeout( function(){
		    $scope.user_status.delay_invited_member = 0;
		},$rootScope.status.delay_invited_member*1000);
    };

	$scope.user_status.delay_invited_member=$rootScope.status.delay_invited_member;
    $timeout.cancel(timer);
	$scope.callDelayInvitedMemberTimeout();
	
    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getUsersLog(function(data) {
            scrollLoadCallback(data.log.hasMore);
        });
    };

    this.invite=function(data) {
        invite.invite(data);
    };
	
	$scope.refreshUserList=function(){
		$scope.state.pageNum=1;
        self.getUsersLog();
	};

    $rootScope.$on('BecomeMemberInviteWinner',function(event,winner) {
        for(var idx in $scope.log.items) {
            var invite=$scope.log.items[idx];
            if (invite.id==winner.user_id) {
                invite.winner=winner;
            }
            $scope.log.items[idx]=angular.copy(invite);
        }
    });

    this.getUsersLog = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-invite-my/list', {
            country_ids:$scope.countryIds,
            pageNum: $scope.state.pageNum
        })
            .then(function (data) {
                $scope.state.loading=false;

                if ($scope.state.pageNum > 1) {
                    data.log.items = $scope.log.items.concat(data.log.items);
                }

                angular.extend($scope, data);

                if ($scope.state.modifiedWhileLoading) {
                    $scope.state.modifiedWhileLoading=false;
                    self.getUsersLog(callback);
                }
				$scope.user_status.delay_invited_member=$rootScope.status.delay_invited_member;
				$timeout.cancel(timer);
				$scope.callDelayInvitedMemberTimeout();
                callback(data);
            });
    };

    $scope.$watch('currentCountry',function(newValue,oldValue) {
		if (newValue != oldValue) {
            $scope.countryIds = '';
            if($scope.currentCountry.length > 0) {
                $scope.countryIds = [];
                angular.forEach($scope.currentCountry,function(item,index){
                    $scope.countryIds.push(item.id);
                });
                $scope.countryIds = $scope.countryIds.join(',');
            }

		    $scope.log.items=[];
		    $scope.state.pageNum=1;
            self.getUsersLog();
        }
    },true);

	
	//$scope.countryArrayNetwork=[];
	
    /*jsonDataPromise('/api-country/get-country-list-network')
    .then(function (res) {
            angular.extend($scope.countryArrayNetwork, res);
            $scope.networkCountry = [{id:$scope.currentCountry.country_id}];
            $scope.state.pageNum=0;
        });*/
	
	$scope.labels=$rootScope.status.labels;	

	/*$scope.$watch('networkCountry',function(newValue,oldValue) {
		if (newValue != oldValue) {
		  $scope.log.items=[];
		  $scope.state.pageNum=0;
          self.getUsersLog();  
        }
    },true);	*/
	

});