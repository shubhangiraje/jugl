app.controller('TrollboxCtrl', function (userStatus,$scope,$state,$rootScope,trollboxCtrlData,jsonDataPromise,jsonPostDataPromise,modal,$timeout,messengerService,Uploader,gettextCatalog) {

    angular.extend($scope, trollboxCtrlData);
    var self = this;

    $scope.state={
        pageNum:1
    };

    $scope.trollbox = {
        newMessage: {},
        voiting: false,
        sending: false,
        loading: false
    };

    if(!$scope.filter) {
        $scope.filter = {};
        $scope.filter.type = '';
        resetFilter();
    } else {
        $scope.filter.type = '';
    }


    function resetFilter() {
        $scope.filter.visibility = '';
        $scope.filter.category = '';
        $scope.filter.period = '';
        $scope.filter.sort = 'dt';
    }

    updateCategoryList();
    $scope.isUpdatedCategoryList = false;

    this.changeFilterVisibility = function(value) {
        if (value!=='MAIN') {
            $scope.filter.visibility = value;
        } else {
            $rootScope.gotoMainTrollboxMessages = true;
            $state.go('userProfile', {'id': userStatus.status.id});
        }
    };

    this.changeFilterType = function(value) {
        $scope.filter.category = '';
        $scope.filter.visibility = '';
        $scope.filter.type = value;
    };

    $scope.$watch('filter',function(newValue,oldValue) {
        if (newValue != oldValue) {
            $scope.state.pageNum = 1;
            self.getTrollboxMessage();
        }
    }, true);

    $scope.$watch('forumCountry',function(newValue,oldValue) {
		if (newValue != oldValue) {

            $scope.countryIds = [];
            if($scope.forumCountry.length > 0) {
                angular.forEach($scope.forumCountry,function(item,index){
                    $scope.countryIds.push(item.id);
                });
                $scope.countryIds = $scope.countryIds.join(',');
            }

		    $scope.state.pageNum=1;
            $scope.isUpdatedCategoryList = true;
		    self.getTrollboxMessage();
		}
    },true);

    $scope.log.loading=false;
    $scope.uploader=Uploader(['trollboxSmall']);

    $scope.fileUploadOptions={
        onSuccess: function(response,status,headers) {
            $scope.trollbox.newMessage.file_id = response.id;

            var img = new Image();
            img.onload = function() {
                if ($scope.trollbox.newMessage.file_id !== null) {
                    $scope.trollbox.newMessage.image = response.thumbs.trollboxSmall;
                }
            };
            img.src = response.thumbs.trollboxSmall;
        }
    };

    this.deleteTrollboxImage=function() {
        delete $scope.trollbox.newMessage.file_id;
        delete $scope.trollbox.newMessage.image;
    };

    this.enterGroupChat=function(id) {
        $timeout(function(){
            jsonPostDataPromise('/api-trollbox/enter-group-chat', {id: id})
                .then(function (res) {
                    if(res.result===true) {
                        messengerService.talkWithUser(res.groupChatId);
                    }
                });
        });
    };

    this.trollboxAcceptMessage=function(message) {
        jsonPostDataPromise('/api-moderator/accept-trollbox-message', {id: message.id})
            .then(function (res) {
                if(res.result===true) {
                    for(var idx in $scope.log.items) {
                        if ($scope.log.items[idx].id==message.id) {
                            $scope.log.items[idx]=res.trollboxMessage;
                        }
                    }
                } else {
                    modal.alert({message:res.result});
                }
            });
    };

    this.trollboxRejectMessage=function(message) {
        jsonPostDataPromise('/api-moderator/reject-trollbox-message', {id: message.id})
            .then(function (res) {
                if(res.result===true) {
                    for(var idx in $scope.log.items) {
                        if ($scope.log.items[idx].id==message.id) {
                            $scope.log.items[idx]=res.trollboxMessage;
                        }
                    }
                } else {
                    modal.alert({message:res.result});
                }
            });
    };

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getTrollboxMessage(function(data) {
            scrollLoadCallback(data.log.hasMore);
        });
    };

    this.getTrollboxMessage = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-trollbox/list', {
            country_ids: $scope.countryIds,
            pageNum: $scope.state.pageNum,
            filter: $scope.filter
        })
            .then(function (data) {
                $scope.state.loading=false;

                if ($scope.state.pageNum > 1) {
                    for(var msgIdx in $scope.log.items) {
                        for(var idx in data.log.items){
                            if($scope.log.items[msgIdx].id==data.log.items[idx].id) {
                                data.log.items.splice(idx, 1);
                                break;
                            }
                        }
                    }

                    if(data.log.items.length>0) {
                        data.log.items = $scope.log.items.concat(data.log.items);
                    } else {
                        $scope.state.pageNum++;
                        self.getTrollboxMessage(callback);
                        return;
                    }
                }

                angular.extend($scope, data);

                if($scope.isUpdatedCategoryList) {
                    updateCategoryList();
                    $scope.isUpdatedCategoryList = false;
                }

                if ($scope.state.modifiedWhileLoading) {
                    $scope.state.modifiedWhileLoading=false;
                    self.getTrollboxMessage(callback);
                }

                callback(data);
            });
    };

    function trollboxVote(id,vote) {
        $scope.trollbox.voiting = true;
        jsonPostDataPromise('/api-trollbox/vote-message', {id: id, vote: vote})
            .then(function (res) {
                $scope.trollbox.voiting = false;
                if(res.message) {
                    for(var msgIdx in $scope.log.items) {
                        if ($scope.log.items[msgIdx].id==res.message.id) {
                            $scope.log.items[msgIdx]=res.message;
                        }
                    }
                    if (userStatus.status.id==68) {
                        $scope.count_video_identification--;
                    }
                }
                modal.alert({message:res.result});
            },function(){
                $scope.trollbox.voiting = false;
            });
    }

    this.trollboxVoteUp=function(id) {
        if ($scope.trollbox.voiting) {
            return;
        }
        trollboxVote(id,1);
    };

    this.trollboxVoteDown=function(id) {
        if ($scope.trollbox.voiting) {
            return;
        }
        trollboxVote(id,-1);
    };

    this.updateTrollbox = function() {
		$scope.state.pageNum=1;
        self.getTrollboxMessage();
    };

    this.trollboxSendMessage=function() {
        if (userStatus.status.is_blocked_in_trollbox) {
            modal.alert({message: gettextCatalog.getString("Du wurdest für alle Foren von einem Moderator gesperrt")});
        } else {
            $scope.trollbox.$allErrors = [];
            modal.show({
                template: '/app-view/trollbox-message-visibility-popup',
                classes: {'modal-offer': true},
				country_ids: $scope.countryIds,
                trollboxMessage: $scope.trollbox.newMessage,
                trollboxCategoryList: $scope.trollboxCategoryList,
                setTrollbox: function (data) {
                    if (data.trollboxMessage.$allErrors.length === 0) {
                        self.updateTrollbox();
                        $scope.trollbox.newMessage = {};
                        // $scope.log.items=data.trollboxMessages;
                        if (data.message) {
                            modal.alert({message: data.message});
                        }
                    } else {
                        if (angular.isArray(data.trollboxMessage.$allErrors) && data.trollboxMessage.$allErrors.length > 0) {
                            $scope.trollbox.newMessage = data.trollboxMessage;
                            $scope.trollbox.sending = false;
                            return;
                        }
                    }
                }
            });
        }

    };

    function updateCategoryList() {
        $scope.categoryList = [];
        angular.forEach($scope.trollboxCategoryList,function(item,index){
            $scope.categoryList[index] = {
                id: item.id,
                title: item.title + ' ('+item.count_message+')'
            };
        });
    }

	$scope.labels=$rootScope.status.labels;	

	this.setAdvertising = function(id, user_bonus, click_interval, popup_interval) {
		var advertisements = {};
		advertisements.id = id;
        advertisements.user_bonus = user_bonus;
		advertisements.click_interval = click_interval;
		advertisements.popup_interval = popup_interval;

       jsonDataPromise('/api-advertising/set-advertising-user',{advertising_id:id})
        .then(function (data) {
			if (user_bonus > 0 && data === true) {
				timeoutPromise=$timeout(function(){
					var config={
						template:'/app-view/advertising-view-bonus-popup',
						advertising: advertisements
					};
					modal.show(config);
				},1000);
			}
			
        });
    };

    function escapeRegExp(str) {
        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    }

    this.emoticonsList = $rootScope.emoticonsList;

    this.emoticonsListInversion={};
    this.emoticonsRegExp=new RegExp(
        res=this.emoticonsList.map(function(emoticon) {
            return emoticon.codes.map(function(code) {
                self.emoticonsListInversion[code]=emoticon.num;
                return escapeRegExp(code);
            }).join('|');
        }).join('|'),'g'
    );


    this.votesView=function(id) {
        jsonDataPromise('api-trollbox/votes',{id:id})
            .then(function(data){
                var config={
                    template:'/app-view/view-votes-trollbox-popup',
                    classes: {'modal-info':true}
                };
                angular.extend(config,data);
                modal.show(config);
            });
    };

    this.votesViewVideo=function(id, type) {
        jsonDataPromise('api-trollbox/votes',{id:id, type: type})
            .then(function(data){
                var config={
                    template:'/app-view/view-votes-trollbox-popup',
                    classes: {'modal-info':true}
                };
                angular.extend(config,data);
                modal.show(config);
            });
    };


    this.trollboxBlockUser=function(message) {
        modal.confirmation({message:gettextCatalog.getString('Willst Du wirklich den Benutzer für alle Foren sperren?')},function(result){
            if (!result) return;

            jsonPostDataPromise('/api-moderator/block-user-in-trollbox-with-message',{groupChatId:message.id,userId:message.user.id}).then(function(data){
                if (data.result===true) {
                    for(var idx in $scope.log.items) {
                        if ($scope.log.items[idx].user.id==message.user.id) {
                            $scope.log.items[idx].user.is_blocked_in_trollbox=1;
                        }
                        if ($scope.log.items[idx].id==message.id) {
                            $scope.log.items[idx]=data.trollboxMessage;
                        }
                    }
                } else {
                    modal.alert({message:data.result});
                }
            });
        });
    };

    this.trollboxUnblockUser=function(message) {
        modal.confirmation({message:gettextCatalog.getString('Willst Du wirklich den Benutzer für alle Foren entsperren?')},function(result){
            if (!result) return;

            jsonPostDataPromise('/api-moderator/unblock-user-in-trollbox',{groupChatId:message.id,userId:message.user.id}).then(function(data){
                if (data.result===true) {
                    for(var idx in $scope.log.items) {
                        if ($scope.log.items[idx].user.id==message.user.id) {
                            $scope.log.items[idx].user.is_blocked_in_trollbox=0;
                        }
                    }
                } else {
                    modal.alert({message:data.result});
                }
            });
        });
    };

    this.trollboxSetStickyTrollboxMessage=function(message) {
        jsonPostDataPromise('/api-moderator/set-sticky-trollbox-message', {id: message.id})
            .then(function (res) {
                if(res.result===true) {
                    for(var idx in $scope.log.items) {
                        if ($scope.log.items[idx].id==message.id) {
                            $scope.log.items[idx]=res.trollboxMessage;
                        }
                    }
                } else {
                    modal.alert({message:res.result});
                }
            });
    };

    this.trollboxUnsetStickyTrollboxMessage=function(message) {
        jsonPostDataPromise('/api-moderator/unset-sticky-trollbox-message', {id: message.id})
            .then(function (res) {
                if(res.result===true) {
                    for(var idx in $scope.log.items) {
                        if ($scope.log.items[idx].id==message.id) {
                            $scope.log.items[idx]=res.trollboxMessage;
                        }
                    }
                } else {
                    modal.alert({message:res.result});
                }
            });
    };

    this.resetFilter = function() {
        resetFilter();
        $scope.forumCountry = [];
    };

    if ($rootScope.paramTrollboxFilterView) {
        $scope.filter.visibility = $rootScope.paramTrollboxFilterView;
        $rootScope.paramTrollboxFilterView = null;
        $scope.state.pageNum = 1;
        self.getTrollboxMessage();
    }

    if ($rootScope.paramTrollboxFilterType) {
        $scope.filter.type = $rootScope.paramTrollboxFilterType;
        $rootScope.paramTrollboxFilterType = null;
        $scope.state.pageNum = 1;
        self.getTrollboxMessage();
    }


});