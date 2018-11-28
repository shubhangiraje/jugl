app.controller('InfoPopupCtrl', function ($scope,$rootScope,Uploader,modal,jsonPostDataPromise,jsonDataPromise,$state,userStatus,gettextCatalog,$timeout) {

    var self = this;

    $scope.infoPopupData = modal.dataInfo.infoPopupData;
    $scope.infoComments = $scope.infoPopupData.infoComments;

    $scope.labels=$rootScope.status.labels;

    $scope.countryId = $scope.infoPopupData.currentCountry[0].id;

    $scope.infoComment = {
        newComment: {},
        voiting: false,
        sending: false
    };

    $scope.state={
        pageNum:1,
        sort:'votes_up'
    };

    $scope.infoComments.loading=false;

    $scope.uploader=Uploader(['infoCommentSmall']);

    $scope.fileUploadOptions={
        onSuccess: function(response,status,headers) {
            $scope.infoComment.newComment.file_id = response.id;

            var img = new Image();
            img.onload = function() {
                if ($scope.infoComment.newComment.file_id !== null) {
                    $scope.infoComment.newComment.image = response.thumbs.infoCommentSmall;
                }
            };
            img.src = response.thumbs.infoCommentSmall;
        }
    };

    this.deleteInfoCommentImage=function() {
        delete $scope.infoComment.newComment.file_id;
        delete $scope.infoComment.newComment.image;
    };


    this.add = function() {

        if (userStatus.status.packet=='VIP' || userStatus.status.packet=='VIP_PLUS') {
            if ($scope.infoComment.sending) {
                return;
            }
            $scope.infoComment.sending = true;
            $scope.infoComment.$allErrors = [];

            jsonPostDataPromise('/api-info/add-comment', {
                infoComment: $scope.infoComment.newComment,
                info_id: $scope.infoPopupData.info.id,
                country_id:$scope.countryId
            }).then(function (data) {
                    $scope.infoComment.sending = false;
                    if (data.result===true) {
                        $scope.infoComment.newComment={};
                        $scope.state.pageNum=1;
                        angular.extend($scope.infoComments, data.infoComments);
                        angular.extend($scope.infoPopupData.countryList, data.countryList);
                    }
                    
                    if (data.infoComment && angular.isArray(data.infoComment.$allErrors) && data.infoComment.$allErrors.length > 0) {
                        $scope.infoComment = data.infoComment;
                        $scope.infoComment.sending = false;
                        return;
                    }

                },function(){
                    $scope.infoComment.sending = false;
                });
        } else {
            modal.alert({message: gettextCatalog.getString('Leider hast Du keine Premium-Mitgliedschaft um diese Funktion nutzen zu können.')});
        }


    };

    this.sort=function (field) {
        $scope.state.sort=field;
    };

    $scope.$watch('state.sort',function(new_value, old_value) {
        if (new_value != old_value) {
            $scope.state.pageNum=1;
            self.getInfoComments();
        }
    }, true);


    $scope.$watch('infoPopupData.currentCountry[0].id',function(newValue,oldValue) {
		if (newValue != oldValue) {
            $scope.countryId = newValue;
		    $scope.state.pageNum=1;
            self.getInfoComments();
        }
    },true);


    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getInfoComments(function(data) {
            scrollLoadCallback(data.hasMore);
        });
    };

    this.getInfoComments = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-info/list-comments',{
            info_id: $scope.infoPopupData.info.id,
            pageNum: $scope.state.pageNum,
            sort: $scope.state.sort,
			country_id:$scope.countryId
        })
            .then(function (data) {
                $scope.state.loading=false;

                if ($scope.state.pageNum > 1) {
                    data.items = $scope.infoComments.items.concat(data.items);
                }

                angular.extend($scope.infoPopupData.infoComments, data);
				
				//updateCountryList();

                if ($scope.state.modifiedWhileLoading) {
                    $scope.state.modifiedWhileLoading=false;
                    self.getInfoComments(callback);
                }
                callback(data);
            });
    };


    function infoCommentVote(id,vote) {
        $scope.infoComment.voiting = true;
        jsonPostDataPromise('/api-info/vote-comment', {id: id, vote: vote})
            .then(function (res) {
                $scope.infoComment.voiting = false;
                if(res.comment) {
                    for(var idx in $scope.infoComments.items) {
                        if ($scope.infoComments.items[idx].id==res.comment.id) {
                            $scope.infoComments.items[idx]=res.comment;
                        }
                    }
                }
                modal.alert({message:res.result});
            },function(){
                $scope.infoComment.voiting = false;
            });
    }

    this.infoCommentVoteUp=function(id) {
        if ($scope.infoComment.voiting) {
            return;
        }
        infoCommentVote(id,1);
    };

    this.infoCommentVoteDown=function(id) {
        if ($scope.infoComment.voiting) {
            return;
        }
        infoCommentVote(id,-1);
    };

    this.votesView=function(id) {
        jsonDataPromise('api-info/votes-comment',{id:id})
            .then(function(data){
                var config={
                    template:'/app-view/view-votes-info-popup',
                    classes: {'modal-info':true}
                };
                angular.extend(config,data);
                modal.show(config);
            });
    };

    this.goProfile = function(id) {
        modal.hideInfo();
        $state.go('userProfile', {id: id});
    };

    this.acceptComment = function(comment) {
        jsonPostDataPromise('/api-info/accept-comment', {id: comment.id})
            .then(function (res) {
                if(res.result===true) {
                    for(var idx in $scope.infoComments.items) {
                        if ($scope.infoComments.items[idx].id==comment.id) {
                            $scope.infoComments.items[idx]=res.infoComment;
                        }
                    }
                } else {
                    modal.alert({message:res.result});
                }
            });
    };

    this.rejectComment = function(comment) {
        jsonPostDataPromise('/api-info/reject-comment', {id: comment.id})
            .then(function (res) {
                if(res.result===true) {
                    for(var idx in $scope.infoComments.items) {
                        if ($scope.infoComments.items[idx].id==comment.id) {
                            $scope.infoComments.items[idx]=res.infoComment;
                        }
                    }
                } else {
                    modal.alert({message:res.result});
                }
            });
    };
	
	

});