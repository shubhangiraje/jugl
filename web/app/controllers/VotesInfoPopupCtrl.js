app.controller('VotesInfoPopupCtrl', function ($scope,$state,modal,jsonDataPromise) {

    $scope.log=modal.data.log;
    $scope.comment_id=modal.data.comment_id;

    var self = this;
    $scope.state={pageNum:1};

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getVotesComment(function(data) {
            scrollLoadCallback(data.log.hasMore);
        });
    };

    this.getVotesComment = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-info/list-votes-comment',{
            id: $scope.comment_id,
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
                    self.getVotesComment(callback);
                }
                callback(data);
            });
    };


    this.goProfile = function(id) {
        modal.hide();
        modal.hideInfo();
        $state.go('userProfile', {id: id});
    };

});