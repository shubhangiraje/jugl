app.controller('SearchRequestDetailsCtrl', function ($scope,searchRequestDetailsData,userStatus,modal,jsonPostDataPromise,$rootScope,jsonDataPromise) {

    angular.extend($scope, searchRequestDetailsData);

    var self = this;
    $scope.status=userStatus.status;
    $scope.state={
        pageNum:1
    };

    $scope.$on('spamReported',function() {
        $scope.searchRequest.spamReported=true;
    });

    $scope.spamReport=function(data) {
        var config={
            template:'/app-view/spam-report-popup',
            classes: {'modal-offer':true},
            spamReport: data
        };

        angular.extend(config,data);

        modal.show(config);
    };

    this.addFavorite = function(id) {

        jsonPostDataPromise('/api-favorites/add',{id:id, type:'search_request'})
            .then(function (data) {
                if (data.result===true) {
                    $scope.searchRequest.favorite = true;
                }
            });

    };

    this.activeImageChanged = function(idx) {
        if($scope.searchRequest.bigImages) {
            $scope.searchRequest.fancyboxImages = [];
            for (var i = idx; i < $scope.searchRequest.bigImages.length; i++) {
                $scope.searchRequest.fancyboxImages.push($scope.searchRequest.bigImages[i]);
            }

            for (var y = 0; y < idx; y++) {
                $scope.searchRequest.fancyboxImages.push($scope.searchRequest.bigImages[y]);
            }
        }
    };


    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getComments(function(data) {
            scrollLoadCallback(data.comments.hasMore);
        });
    };

    this.getComments = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-search-request/list-comments',{
            searchRequestId: $scope.searchRequest.id,
            pageNum: $scope.state.pageNum
        })
            .then(function (data) {
                $scope.state.loading=false;

                if ($scope.state.pageNum > 1) {
                    data.comments.items = $scope.comments.items.concat(data.comments.items);
                }

                angular.extend($scope.comments, data.comments);

                if ($scope.state.modifiedWhileLoading) {
                    $scope.state.modifiedWhileLoading=false;
                    self.getComments(callback);
                }
                callback(data);
            });
    };


    this.commentResponse = function(comment) {
        jsonDataPromise('/api-search-request-comment/response-update',{id:comment.id}).then(function(data){
            if (data===false) return;
            var config={
                template:'/app-view/search-request-comment-response-popup',
                classes: {'modal-offer':true}
            };
            angular.extend(config,data);
            modal.show(config);
        });
    };

    this.addComment = function() {
        var config={
            template:'/app-view/search-request-comment-popup',
            classes: {'modal-offer':true},
            search_request_id: $scope.searchRequest.id
        };
        modal.show(config);
    };

    $rootScope.$on('searchRequestCommentResponse',function(event,data) {
        for(var idx in $scope.comments.items) {
            var comment=$scope.comments.items[idx];
            if (comment.id==data.id) {
                angular.extend(comment,data);
            }
        }
    });

    $rootScope.$on('addSearchRequestComment',function(event,data) {
        $scope.state.pageNum=1;
        angular.extend($scope.comments, data);
    });


});
