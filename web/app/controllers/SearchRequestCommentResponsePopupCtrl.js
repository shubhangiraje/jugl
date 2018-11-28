app.controller('SearchRequestCommentResponsePopupCtrl', function ($scope,jsonPostDataPromise,$state,modal,$rootScope) {

    $scope.comment=angular.copy($scope.modalService.data.comment);

    this.save=function() {
        $scope.comment.saving = true;
        jsonPostDataPromise('/api-search-request-comment/response-save',{comment:$scope.comment})
            .then(function (data) {
                if (data.result) {
                    modal.hide();
                    $rootScope.$broadcast('searchRequestCommentResponse',data.comment);
                } else {
                    angular.extend($scope,data);
                    $scope.comment.saving = false;
                }
            },function(data) {
                $scope.comment.saving = false;
            });

    };

});
