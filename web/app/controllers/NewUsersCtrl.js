app.controller('NewUsersCtrl', function ($scope,$rootScope,newUsersData,jsonDataPromise,$state,$timeout) {

    var self=this;
																			   
	$scope.user_status={};
	angular.extend($scope.user_status,$rootScope.status);
	
	$scope.callDelayInvitedMemberTimeout = function() {
        $scope.user_status.delay_invited_member = 0;
    };

    $timeout( function(){  $scope.callDelayInvitedMemberTimeout(); }, $rootScope.status.delay_invited_member*1000);
    angular.extend($scope, newUsersData);

    try {
        $scope.urlState=angular.fromJson($state.params.urlState);
    } catch (e) {
        $scope.urlState={
            users: {               
                pageNum: 1
            }
        };
    }

    $scope.$watch('urlState',function(newValue,oldValue){
        if (oldValue!=newValue) {
            $state.transitionTo($state.current.name, {urlState: angular.toJson(newValue)}, { location: 'replace', inherit: true, relative: $state.$current, notify: false });
        }
    },true);

    $scope.filter=$scope.urlState.users.filter;
    $scope.state={};

    this.loadMore=function(scrollLoadCallback) {
        $scope.urlState.users.pageNum++;
        self.load(function(data) {
            scrollLoadCallback(data.users.hasMore);
        });
    };

    this.load=function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }
		$tempNewUsersCountry=$scope.newUsersCountry.map(function(a){return a.id;});
        $scope.state.loading=true;
        jsonDataPromise('api-user-search/new-users-request',{urlState:angular.toJson($scope.urlState),country_ids:angular.toJson($tempNewUsersCountry)}).then(function (data) {
										 
		  
								   
            $scope.state.loading=false;

            if ($scope.urlState.users.pageNum > 1) {
                data.users.users = $scope.users.users.concat(data.users.users);
            }

            angular.extend($scope, data);

            if ($scope.state.modifiedWhileLoading) {
                $scope.state.modifiedWhileLoading=false;
                self.load(callback);
            }
            callback(data);
        });
    };
	
	$scope.countryArrayNewUser=[];	
	
				jsonDataPromise('/api-country/get-country-list-new-user')
				.then(function (res) {
						angular.extend($scope.countryArrayNewUser, res);
						$scope.newUsersCountry=[{id:$rootScope.status.currentCountry.country_id}];
					});
	
	$scope.labels=$rootScope.status.labels;
		
    $scope.$watch('newUsersCountry',function(new_value, old_value) {
        if (new_value != old_value) {
            $scope.urlState.users.pageNum = 1;
            self.load();
        }
    }, true);
	

});