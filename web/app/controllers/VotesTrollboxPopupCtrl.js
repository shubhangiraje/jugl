app.controller('VotesTrollboxPopupCtrl', function ($scope,$state,modal,jsonDataPromise) {

    $scope.log=modal.data.log;
    $scope.message_id=modal.data.message_id;
    $scope.type=modal.data.type;

    var self = this;
    $scope.state={pageNum:1};

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getVotes(function(data) {
            scrollLoadCallback(data.log.hasMore);
        });
    };

    this.getVotes = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        var params = {
            id: $scope.message_id,
            pageNum: $scope.state.pageNum
        };

        if ($scope.type!==null) {
            params.type = $scope.type;
        }

        jsonDataPromise('/api-trollbox/votes-list', params)
            .then(function (data) {
                $scope.state.loading=false;

                if ($scope.state.pageNum > 1) {
                    data.log.items = $scope.log.items.concat(data.log.items);
                }

                angular.extend($scope, data);

                if ($scope.state.modifiedWhileLoading) {
                    $scope.state.modifiedWhileLoading=false;
                    self.getVotes(callback);
                }
                callback(data);
            });
    };


    this.goProfile = function(id) {
        modal.hide();
        $state.go('userProfile', {id: id});
    };

});