app.controller('FriendsInvitationRegcodesCtrl', function ($scope,friendsInvitationRegcodesData,$rootScope,$http,modal,userStatus,jsonDataPromise, $state, $stateParams) {

    angular.extend($scope, friendsInvitationRegcodesData);

    $scope.buyRegcodesPacket={};
    $scope.status=userStatus.status;
    $scope.regcodesState = {pageNum: 1};

    this.moreRegcodesLoad = function(scrollLoadCallback) {
        jsonDataPromise('/api-friends-invitation-regcodes/regcodes',{
            pageNum: ++$scope.regcodesState.pageNum,
            sort: '',
            statusFilter: ''
        }).then(function (data) {
            data.regcodes.regcodes = $scope.regcodes.regcodes.concat(data.regcodes.regcodes);
            angular.extend($scope, data);
            scrollLoadCallback(data.regcodes.hasMore);
        });
    };

    this.buyRegcodesPacket = function () {
        $scope.buyRegcodesPacket.saving = true;
        $http.post('/api-friends-invitation-regcodes/buy-regcodes-packet', {buyRegcodesPacket: $scope.buyRegcodesPacket})
            .error(function (data, status, headers, config) {
                $scope.buyRegcodesPacket.saving = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.buyRegcodesPacket = data.buyRegcodesPacket;
                $scope.buyRegcodesPacket.saving = false;
                userStatus.update();

                modal.alert({'message':$scope.buyRegcodesPacket.message},function(){
                    $state.transitionTo($state.current, $stateParams, {
                        reload: true,
                        inherit: false,
                        notify: true
                    });
                });

            });
    };



});
