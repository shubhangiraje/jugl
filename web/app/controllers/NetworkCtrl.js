app.controller('NetworkCtrl', function ($scope,networkData,jsonDataPromise,$state) {

    angular.extend($scope, networkData);

    try {
        $scope.urlState=angular.fromJson($state.params.urlState);
    } catch (e) {
        $scope.urlState={
            user_id: $scope.hierarchy.user.id
        };
    }

    $scope.$watch('urlState',function(newValue,oldValue){
        if (oldValue!=newValue) {
            $state.transitionTo($state.current.name, {urlState: angular.toJson(newValue)}, { location: 'replace', inherit: true, relative: $state.$current, notify: false });
        }
    },true);


    this.hierarchyShowUser=function(userId) {
        $scope.urlState.user_id=userId;
        jsonDataPromise('/api-network/hierarchy',{urlState:angular.toJson($scope.urlState)}).then(function (data) {
            angular.extend($scope, data);
        });
    };

});
