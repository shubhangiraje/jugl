app.controller('DashboardCtrl', function ($scope, dashboardData, jsonDataPromise, $cookies, $state, userStatus, $rootScope, jsonPostDataPromise) {

    if (dashboardData.registrationFromApp) {
        window.close();
        window.location.href = 'http://jugl.net/#back_app';
    }

    angular.extend($scope, dashboardData);

    try {
        $scope.urlState=angular.fromJson($state.params.urlState);
    } catch (e) {
        $scope.urlState={
            hierarchy_user_id:$scope.hierarchy.user_id,
            friends_page_num:1
        };
    }

    $scope.$watch('urlState',function(newValue,oldValue){
        if (oldValue!=newValue) {
            $state.transitionTo($state.current.name, {urlState: angular.toJson(newValue)}, { location: true, inherit: true, relative: $state.$current, notify: false });
        }
    },true);

    this.friendsNavigationClick = function(pageOffset) {
        $scope.urlState.friends_page_num += pageOffset;
        jsonDataPromise('/api-dashboard/friends',{urlState:angular.toJson($scope.urlState)})
        .then(function (data) {
            angular.extend($scope, data);
        });
    };

    this.hierarchyShowUser=function(userId) {
        $scope.urlState.hierarchy_user_id=userId;
        jsonDataPromise('/api-dashboard/hierarchy',{urlState:angular.toJson($scope.urlState)})
        .then(function (data) {
            angular.extend($scope, data);
        });
    };

    if(!userStatus.status.show_friends_invite_popup && userStatus.status.status=='ACTIVE') {
        var currentTime = (new Date()).getTime();
        if((currentTime > $cookies.get('showFriendsInvitePopup')) || !$cookies.get('showFriendsInvitePopup')) {
            $cookies.put('showFriendsInvitePopup', currentTime+3600*1000);
            jsonPostDataPromise('/api-user/invite-friends-count').then(
                function (data, status, headers, config) {
                    if(data.result) {
                        $rootScope.showFriendsInvitePopup();
                    }
                }
            );
        }
    }


});
