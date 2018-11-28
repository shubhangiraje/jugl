app.controller('UserProfileCtrl', function ($scope,userStatus,$state,$timeout,userProfileData,$http,modal,$stateParams,jsonDataPromise,jsonPostDataPromise,gettextCatalog,$rootScope,$anchorScroll,messengerService) {

    angular.extend($scope, userProfileData);
    var self = this;

    if ($rootScope.scrollToTeamlederFeedbacks) {
        $scope.teamFeedback.feedbackUser=true;
    }

    $timeout(function(){
        if ($rootScope.scrollToTeamlederFeedback) {
            $rootScope.scrollToTeamlederFeedback=false;
            $anchorScroll('teamleader-feedback');
        }

        if ($rootScope.scrollToTeamlederFeedbacks) {
            $rootScope.scrollToTeamlederFeedbacks=false;
            $anchorScroll('teamleader-feedbacks');
        }
    });

    $scope.userInfo.requesting = false;

    $scope.stateFriends={pageNum:1};
    $scope.stateFollowers={pageNumFollowers:1};
    $scope.stateFeedback={pageNumFeedback:1};
    $scope.stateTeamFeedback={pageNumTeamFeedback:1};
    $scope.stateTrollboxMessage={pageNumTrollboxMessage:1};

    $scope.collapseFeedback = {
        feedbackUser: 0,
        feedbackDeals: 0
    };

    $scope.collapse = {
        friends: 0,
        followers: 0,
        trollboxMessages: 0
    };

    $scope.trollbox = {
        voiting: false
    };

    $rootScope.$on('userTeamRequestAdded2',function(event,user_id) {
        if ($scope.userInfo.id==user_id) {
            $scope.userInfo.request_sent2=true;
        }
    });

    $rootScope.$on('userTeamRequestAdded',function(event,user_id) {
        if ($scope.userInfo.id==user_id) {
            $scope.userInfo.request_sent=true;
        }
    });

    $rootScope.$on('UserTeamFeedbackResponse',function(event,data) {
        for(var idx in $scope.teamFeedback.items) {
            var feedback=$scope.teamFeedback.items[idx];
            if (feedback.id==data.id) {
                angular.extend(feedback,data);
            }
        }
    });

    $rootScope.$on('UserFeedbackResponse',function(event,data) {
        for(var idx in $scope.feedback.items) {
            var feedback=$scope.feedback.items[idx];
            if (feedback.id==data.id) {
                angular.extend(feedback,data);
            }
        }
    });

    this.loadMore=function(scrollLoadCallback) {
        jsonDataPromise('/api-user-profile/friends',{
            pageNum: ++$scope.stateFriends.pageNum,
            userId: $scope.userInfo.id
        }).then(function (data) {
            data.friends.users = $scope.friends.users.concat(data.friends.users);
            angular.extend($scope, data);
            scrollLoadCallback(data.friends.hasMore);
        });
    };

    this.loadMoreFeedback=function(scrollLoadCallback) {
        jsonDataPromise('/api-user-profile/feedback',{
            pageNumFeedback: ++$scope.stateFeedback.pageNumFeedback,
            userId: $scope.userInfo.id
        }).then(function (data) {
            data.feedback.items = $scope.feedback.items.concat(data.feedback.items);
            angular.extend($scope, data);
            scrollLoadCallback(data.feedback.hasMore);
        });
    };

    this.loadMoreTeamFeedback=function(scrollLoadCallback) {
        jsonDataPromise('/api-user-profile/team-feedback',{
            pageNumTeamFeedback: ++$scope.stateTeamFeedback.pageNumTeamFeedback,
            userId: $scope.userInfo.id
        }).then(function (data) {
            data.feedback.items = $scope.teamFeedback.items.concat(data.feedback.items);
            angular.extend($scope, data);
            scrollLoadCallback(data.feedback.hasMore);
        });
    };

    this.loadMoreFollowers=function(scrollLoadCallback) {
        jsonDataPromise('/api-user-profile/followers',{
            pageNum: ++$scope.stateFollowers.pageNumFollowers,
            userId: $scope.userInfo.id
        }).then(function (data) {
            data.followers.users = $scope.followers.users.concat(data.followers.users);
            angular.extend($scope, data);
            scrollLoadCallback(data.followers.hasMore);
        });
    };

    this.loadMoreTrollboxMessages=function(scrollLoadCallback) {
        jsonDataPromise('/api-user-profile/trollbox-messages',{
            pageNum: ++$scope.stateTrollboxMessage.pageNumTrollboxMessage,
            userId: $scope.userInfo.id
        }).then(function (data) {
            data.trollboxMessages.items = $scope.trollboxMessages.items.concat(data.trollboxMessages.items);
            angular.extend($scope, data);
            scrollLoadCallback(data.trollboxMessages.hasMore);
        });
    };


    this.requestTeamChange2=function() {
        if(userStatus.status.packet == 'VIP' || userStatus.status.packet == 'VIP_PLUS') {
            var config={
                template:'/app-view/team-change-request-popup2',
                classes: {'modal-offer':true},
                userTeamRequest: {
                    user: {name: ($scope.userInfo.first_name ? $scope.userInfo.first_name:'')+' '+($scope.userInfo.last_name ? $scope.userInfo.last_name:'')},
                    second_user_id: $scope.userInfo.id
                }
            };

            modal.show(config);
        } else {
            modal.confirmation({
                message: gettextCatalog.getString('Diese Funktion steht nur Premium Mitgliedern zur Verfügung.'),

                buttons: [
                    {
                        caption: gettextCatalog.getString('Jetzt Premium-Mitglied werden'),
                        class: 'btn-line',
                        onClick: function() {
                            modal.hide();
                            $state.go('packetUpgrade');
                        }
                    },
                    {
                        caption: gettextCatalog.getString('Ok'),
                        class: 'cancel',
                        onClick: function() {
                            modal.hide();
                        }
                    }
                ]
            });
        }
    };

    this.requestTeamChange=function(user) {
        if (userStatus.status.block_team_change) {
            modal.alert({message: gettextCatalog.getString('Lieber Jugler, ein Teamwechsel ist nur alle 24h möglich.')});
        } else {
            var config={
                template:'/app-view/team-change-request-popup',
                classes: {'modal-offer':true},
                userTeamRequest: {
                    user: {name: ($scope.userInfo.first_name ? $scope.userInfo.first_name:'')+' '+($scope.userInfo.last_name ? $scope.userInfo.last_name:'')},
                    second_user_id: $scope.userInfo.id
                }
            };
            modal.show(config);
        }
    };

    this.teamFeedback=function() {
        jsonDataPromise('api-user-team-feedback/update').then(function(data){
            if (data===false) return;
            var config={
                template:'/app-view/user-team-feedback-popup',
                classes: {'modal-offer':true}
            };

            angular.extend(config,data);

            modal.show(config);
        });
    };

    this.teamFeedbackResponse=function(feedback) {
        jsonDataPromise('api-user-team-feedback/response-update',{id:feedback.id}).then(function(data){
            if (data===false) return;
            var config={
                template:'/app-view/user-team-feedback-response-popup',
                classes: {'modal-offer':true}
            };

            angular.extend(config,data);

            modal.show(config);
        });
    };

    this.feedbackResponse=function(feedback) {
        jsonDataPromise('api-user-feedback/response-update',{id:feedback.id}).then(function(data){
            if (data===false) return;
            var config={
                template:'/app-view/user-feedback-response-popup',
                classes: {'modal-offer':true}
            };

            angular.extend(config,data);

            modal.show(config);
        });
    };

    this.deleteFromFriends = function(friendId) {
        modal.confirmation({message:gettextCatalog.getString('You really want to delete this user from friends?')},function(result){
            if (!result)
                return;

            $scope.userInfo.requesting = true;
            $http.post('/api-user-profile/delete-friend', {friendId: friendId,userId:$stateParams.id,pageNum:1})
            .error(function (data, status, headers, config) {
                $scope.userInfo.requesting = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.userInfo.requesting = false;
                angular.extend($scope,data);
            });
        });
    };

    this.addToFriends = function(friendId) {
        $scope.userInfo.requesting = true;
        $http.post('/api-user-profile/add-friend', {friendId: friendId})
            .error(function (data, status, headers, config) {
                $scope.userInfo.requesting = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.userInfo.requesting = false;
                modal.alert({message:data.result});
            });
        return false;
    };

    this.addToFriends = function(friendId) {
        $scope.userInfo.requesting = true;
        $http.post('/api-user-profile/add-friend', {friendId: friendId})
            .error(function (data, status, headers, config) {
                $scope.userInfo.requesting = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.userInfo.requesting = false;
                modal.alert({message:data.result});
            });
        return false;
    };

    this.addToIgnoreList = function(friendId) {
        $scope.userInfo.requesting = true;
        $http.post('/api-user-profile/add-to-ignore-list', {friendId: friendId})
            .error(function (data, status, headers, config) {
                $scope.userInfo.requesting = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.userInfo.requesting = false;
                angular.extend($scope,data);
            });
        return false;
    };

    this.delFromIgnoreList = function(friendId) {
        $scope.userInfo.requesting = true;
        $http.post('/api-user-profile/del-from-ignore-list', {friendId: friendId})
            .error(function (data, status, headers, config) {
                $scope.userInfo.requesting = false;
                modal.httpError(data, status, headers, config);
            })
            .success(function (data, status, headers, config) {
                $scope.userInfo.requesting = false;
                angular.extend($scope,data);
            });
        return false;
    };

    this.subscribe=function() {
        jsonPostDataPromise('/api-user-profile/change-subscribe',{subscribeUserId: $scope.userInfo.id})
            .then(function (data) {
                if (data.result===true) {
                    $scope.userInfo.isMyFollow = true;
                }
            });
    };

    this.unsubscribe=function() {
        jsonPostDataPromise('/api-user-profile/change-subscribe',{subscribeUserId: $scope.userInfo.id})
            .then(function (data) {
                if (data.result===true) {
                    $scope.userInfo.isMyFollow = false;
                }
            });
    };

    function changeSubscribeAction(id) {
        jsonPostDataPromise('/api-user-profile/change-subscribe',{subscribeUserId: id})
            .then(function (data) {
                if (data.result===true) {
                    for (var idx in $scope.friends.users) {
                        if ($scope.friends.users[idx].id == id) {
                            $scope.friends.users[idx].isFollow = data.isFollow;
                        }
                    }
                }
            });
    }

    function changeFriendAction(id) {
        jsonPostDataPromise('/api-user-profile/change-friend',{friendUserId: id})
            .then(function (data) {
                if (data.result===true) {
                    for (var idx in $scope.friends.users) {
                        if ($scope.friends.users[idx].id == id) {
                            $scope.friends.users[idx].isFriend = data.isFriend;
                        }
                    }
                }
            });
    }


    this.changeSubscribe = function(user) {
        if(user.isFriend===false) {
            var userName = user.first_name+' '+user.last_name;
            modal.confirmation({message:gettextCatalog.getString('Möchtest Du '+userName+' wirklich aus Deinen Kontakten entfernen?')},function(result) {
                if (result) {
                    changeSubscribeAction(user.id);
                    for (var idx in $scope.friends.users) {
                        if ($scope.friends.users[idx].id == user.id) {
                            $scope.friends.users.splice(idx, 1);
                        }
                    }
                    $scope.friends.count--;
                } else {
                    for (var idy in $scope.friends.users) {
                        if ($scope.friends.users[idy].id == user.id) {
                            $scope.friends.users[idy].isFollow = true;
                        }
                    }
                }
            });
        } else {
            changeSubscribeAction(user.id);
        }
    };

    this.changeFriend = function(user) {
        if(user.isFollow===false) {
            var userName = user.first_name+' '+user.last_name;

            modal.confirmation({message:gettextCatalog.getString('Möchtest Du '+userName+' wirklich aus Deinen Kontakten entfernen?')},function(result) {
                if (result) {
                    changeFriendAction(user.id);
                    for (var idx in $scope.friends.users) {
                        if ($scope.friends.users[idx].id == user.id) {
                            $scope.friends.users.splice(idx, 1);
                        }
                    }
                    $scope.friends.count--;
                } else {
                    for (var idy in $scope.friends.users) {
                        if ($scope.friends.users[idy].id == user.id) {
                            $scope.friends.users[idy].isFriend = true;
                        }
                    }
                }
            });

        } else {
            changeFriendAction(user.id);
        }
    };

    this.networkChangeBlock = function() {
        var config={
            template:'/app-view/network-change-block-popup',
            classes: {'modal-offer':true},
            userId: $scope.userInfo.id,
            hideButton: function() {
                $scope.userInfo.canCreateStickRequest=false;
            }
        };
        modal.show(config);
    };


    this.gotoValidationPhone = function() {
        $rootScope.gotoValidationPhone = true;
        $state.go('profile');
    };

    this.gotoValidationPassport = function() {
        $rootScope.gotoValidationPassport = true;
        $state.go('funds.payout');
    };

    function trollboxVote(message,vote) {
        if(message.status == 'DELETED') {
            return;
        }

        $scope.trollbox.voiting = true;
        jsonPostDataPromise('/api-trollbox/vote-message', {id: message.id, vote: vote})
            .then(function (res) {
                $scope.trollbox.voiting = false;
                if(res.message) {
                    for(var msgIdx in $scope.trollboxMessages.items) {
                        if ($scope.trollboxMessages.items[msgIdx].id==res.message.id) {
                            $scope.trollboxMessages.items[msgIdx]=res.message;
                        }
                    }
                }
                modal.alert({message:res.result});
            },function(){
                $scope.trollbox.voiting = false;
            });
    }

    this.trollboxVoteUp=function(message) {
        if ($scope.trollbox.voiting) {
            return;
        }
        trollboxVote(message,1);
    };

    this.trollboxVoteDown=function(message) {
        if ($scope.trollbox.voiting) {
            return;
        }
        trollboxVote(message,-1);
    };

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


    this.trollboxMessageDelete = function (id) {
        modal.confirmation({message:gettextCatalog.getString('Willst Du Deinen Beitrag wirklich löschen?')}, function(result) {
            if (!result) return;

            jsonPostDataPromise('/api-trollbox/delete',{id: id})
                .then(function (data) {
                    if (data.result===true) {
                        for (var idx in $scope.trollboxMessages.items) {
                            if ($scope.trollboxMessages.items[idx].id == id) {
                                $scope.trollboxMessages.items[idx].status = 'DELETED';
                            }
                        }
                    }
                });
        });
    };

    this.trollboxRejectMessage=function(message) {
        jsonPostDataPromise('/api-moderator/reject-trollbox-message', {id: message.id})
            .then(function (res) {
                if(res.result===true) {
                    for(var idx in $scope.trollboxMessages.items) {
                        if ($scope.trollboxMessages.items[idx].id==message.id) {
                            $scope.trollboxMessages.items[idx]=res.trollboxMessage;
                        }
                    }
                } else {
                    modal.alert({message:res.result});
                }
            });
    };

    this.trollboxAcceptMessage=function(message) {
        jsonPostDataPromise('/api-moderator/accept-trollbox-message', {id: message.id})
            .then(function (res) {
                if(res.result===true) {
                    for(var idx in $scope.trollboxMessages.items) {
                        if ($scope.trollboxMessages.items[idx].id==message.id) {
                            $scope.trollboxMessages.items[idx]=res.trollboxMessage;
                        }
                    }
                } else {
                    modal.alert({message:res.result});
                }
            });
    };

    this.trollboxBlockUser=function(message) {
        modal.confirmation({message:gettextCatalog.getString('Willst Du wirklich den Benutzer für alle Foren sperren?')},function(result){
            if (!result) return;

            jsonPostDataPromise('/api-moderator/block-user-in-trollbox-with-message',{groupChatId:message.id,userId:message.user.id}).then(function(data){
                if (data.result===true) {
                    for(var idx in $scope.trollboxMessages.items) {
                        if ($scope.trollboxMessages.items[idx].user.id==message.user.id) {
                            $scope.trollboxMessages.items[idx].user.is_blocked_in_trollbox=1;
                        }
                        if ($scope.trollboxMessages.items[idx].id==message.id) {
                            $scope.trollboxMessages.items[idx]=data.trollboxMessage;
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
                    for(var idx in $scope.trollboxMessages.items) {
                        if ($scope.trollboxMessages.items[idx].user.id==message.user.id) {
                            $scope.trollboxMessages.items[idx].user.is_blocked_in_trollbox=0;
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
                    for(var idx in $scope.trollboxMessages.items) {
                        if ($scope.trollboxMessages.items[idx].id==message.id) {
                            $scope.trollboxMessages.items[idx]=res.trollboxMessage;
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
                    for(var idx in $scope.trollboxMessages.items) {
                        if ($scope.trollboxMessages.items[idx].id==message.id) {
                            $scope.trollboxMessages.items[idx]=res.trollboxMessage;
                        }
                    }
                } else {
                    modal.alert({message:res.result});
                }
            });
    };


    if($rootScope.gotoMainTrollboxMessages) {
        $scope.collapse.trollboxMessages = true;
        $timeout(function() {
            $anchorScroll('mainTrollboxMessages');
            $rootScope.gotoMainTrollboxMessages = null;
        });
    }

    this.trollboxMessageUpdate = function(id) {
        jsonDataPromise('api-trollbox/get-message', {id: id})
            .then(function(data){
                modal.show({
                    template:'/app-view/trollbox-message-update-popup',
                    classes: {'modal-trollbox-update':true},
                    trollboxMessage: data.trollboxMessage,
                    trollboxCategoryList: data.trollboxCategoryList,
                    updateTrollboxMessage: function (data) {
                        for(var idx in $scope.trollboxMessages.items) {
                            if ($scope.trollboxMessages.items[idx].id==data.trollboxMessage.id) {
                                var tmp=angular.copy($scope.trollboxMessages.items[idx]);
                                tmp.text = data.trollboxMessage.text;
                                tmp.file = data.trollboxMessage.file;
                                tmp.trollbox_category_id = data.trollboxMessage.trollbox_category_id;
                                tmp.trollbox_category = data.trollboxMessage.trollbox_category;
                                $scope.trollboxMessages.items[idx]=tmp;
                            }
                        }
                    }
                });

            });
    };

    this.showAppDownloadPopup = function () {
        var config = {
            template:'/app-view/app-download-popup',
            classes: {'modal-app-download':true},
            isDownloadInfoPopup: true
        };
        modal.show(config);
    };

    this.showVideoIdentification = function () {
        jsonDataPromise('api-trollbox/get-video-identification', {user_id: $scope.userInfo.id})
            .then(function(data){
                var config={
                    template:'/app-view/view-video-identification-popup',
                    classes: {'modal-video-identification':true}
                };
                angular.extend(config,data);
                modal.showInfo(config);
            });
    };


});
