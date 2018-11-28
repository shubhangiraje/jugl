app.controller('SearchRequestCommentPopupCtrl', function ($scope,jsonPostDataPromise,$state,modal,$rootScope) {

    $scope.comment = {};
    $scope.comment.search_request_id = $scope.modalService.data.search_request_id;

    this.save=function() {
        $scope.comment.saving = true;
        jsonPostDataPromise('/api-search-request-comment/save',{comment:$scope.comment})
            .then(function (data) {
                if (data.result) {
                    modal.hide();
                    $rootScope.$broadcast('addSearchRequestComment',data.comments);
                } else {
                    angular.extend($scope,data);
                    $scope.comment.saving = false;
                }
            },function(data) {
                $scope.comment.saving = false;
            });

    };

});
