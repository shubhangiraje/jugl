app.controller('DashboardNewCtrl', function ($scope,$filter, userStatus, $rootScope, dashboardData, jsonDataPromise, $state, modal, $cookies, $cookieStore, jsonPostDataPromise, Uploader, messengerService, $timeout, invite, gettextCatalog) {

    angular.extend($scope, dashboardData);

    $scope.buyTokenUri=config.buyTokenUri;
    $scope.buyTokenDepositUri=config.buyTokenDepositUri;

    if ($scope.registrationFromApp) {
        window.close();
        window.location.href = 'http://jugl.net/#back_app';
    }

	$scope.user_status={
		delay_invited_member:null
	};
	
	$scope.callDelayInvitedMemberTimeout = function() {
        timer=$timeout( function(){
		$scope.user_status.delay_invited_member = 0;
		},$rootScope.status.delay_invited_member*1000);
		
    };
	
	var timer;

	$scope.user_status.delay_invited_member=$rootScope.status.delay_invited_member;
    $timeout.cancel(timer);
    $scope.callDelayInvitedMemberTimeout();

    $scope.loadingInviteMe = false;
    $scope.loadPageInviteMe = true;

    $scope.loadPageOffers = true;
    $scope.loadingOffers = false;

    $scope.loadPageVideos = true;
    $scope.loadingVideos = false;

    $scope.pageNumInviteMe = 1;
    $scope.pageNumOffers = 1;
    $scope.pageNumVideos = 1;

    $scope.loadPageSearchRequest = true;
    $scope.loadingSearchRequest = false;

    $scope.pageNumSearchRequest = 1;

    $scope.trollbox = {
        newMessage: {},
        voiting: false,
        sending: false,
        loading: false
    };

    $scope.uploader = Uploader(['trollboxSmall']);

    $scope.fileUploadOptions = {
        onSuccess: function (response, status, headers) {
            $scope.trollbox.newMessage.file_id = response.id;

            var img = new Image();
            img.onload = function () {
                if ($scope.trollbox.newMessage.file_id !== null) {
                    $scope.trollbox.newMessage.image = response.thumbs.trollboxSmall;
                }
            };
            img.src = response.thumbs.trollboxSmall;
        }
    };

    this.deleteTrollboxImage = function () {
        delete $scope.trollbox.newMessage.file_id;
        delete $scope.trollbox.newMessage.image;
    };

    this.invite = function (data) {
        invite.invite(data);
    };

    $rootScope.$on('BecomeMemberInviteWinner', function (event, winner) {
        for (var idx in $scope.inviteMe) {
            var invite = $scope.inviteMe[idx];
            if (invite.id == winner.user_id) {
                invite.winner = winner;
            }
            $scope.inviteMe[idx] = angular.copy(invite);
        }
    });

    // try {
    //     $scope.urlState=angular.fromJson($state.params.urlState);
    // } catch (e) {
    //     $scope.urlState={
    //         hierarchy_user_id:$scope.hierarchy.user_id,
    //         friends_page_num:1
    //     };
    // }

    $scope.$watch('urlState', function (newValue, oldValue) {
        if (oldValue != newValue) {
            $state.transitionTo($state.current.name, { urlState: angular.toJson(newValue) }, { location: true, inherit: true, relative: $state.$current, notify: false });
        }
    }, true);

    this.friendsNavigationClick = function (pageOffset) {
        $scope.urlState.friends_page_num += pageOffset;
        jsonDataPromise('/api-dashboard/friends', { urlState: angular.toJson($scope.urlState) })
            .then(function (data) {
                angular.extend($scope, data);
            });
    };

    function trollboxVote(id, vote) {
        $scope.trollbox.voiting = true;
        jsonPostDataPromise('/api-trollbox/vote-message', { id: id, vote: vote })
            .then(function (res) {
                $scope.trollbox.voiting = false;
                if (res.message) {
                    for (var msgIdx in $scope.trollboxMessages) {
                        if ($scope.trollboxMessages[msgIdx].id == res.message.id) {
                            $scope.trollboxMessages[msgIdx] = res.message;
                        }
                    }
                    $scope.trollbox.newMessage = {};
                }
                modal.alert({ message: res.result });
            }, function () {
                $scope.trollbox.voiting = false;
            });
    }

    this.trollboxVoteUp = function (id) {
        if ($scope.trollbox.voiting) {
            return;
        }
        trollboxVote(id, 1);
    };

    this.trollboxVoteDown = function (id) {
        if ($scope.trollbox.voiting) {
            return;
        }
        trollboxVote(id, -1);
    };

    this.trollboxAcceptMessage = function (message) {
        jsonPostDataPromise('/api-moderator/accept-trollbox-message', { id: message.id })
            .then(function (res) {
                if (res.result === true) {
                    for (var idx in $scope.trollboxMessages) {
                        if ($scope.trollboxMessages[idx].id == message.id) {
                            $scope.trollboxMessages[idx] = res.trollboxMessage;
                        }
                    }
                } else {
                    modal.alert({ message: res.result });
                }
            });
    };

    this.trollboxRejectMessage = function (message) {
        jsonPostDataPromise('/api-moderator/reject-trollbox-message', { id: message.id })
            .then(function (res) {
                if (res.result === true) {
                    for (var idx in $scope.trollboxMessages) {
                        if ($scope.trollboxMessages[idx].id == message.id) {
                            $scope.trollboxMessages[idx] = res.trollboxMessage;
                        }
                    }
                } else {
                    modal.alert({ message: res.result });
                }
            });
    };

    this.trollboxBlockUser=function(message) {
        modal.confirmation({message:gettextCatalog.getString('Willst Du wirklich den Benutzer für alle Foren sperren?')},function(result){
            if (!result) return;

            jsonPostDataPromise('/api-moderator/block-user-in-trollbox-with-message',{groupChatId:message.id,userId:message.user.id}).then(function(data){
                if (data.result===true) {
                    for(var idx in $scope.trollboxMessages) {
                        if ($scope.trollboxMessages[idx].user.id==message.user.id) {
                            $scope.trollboxMessages[idx].user.is_blocked_in_trollbox=1;
                        }
                        if ($scope.trollboxMessages[idx].id==message.id) {
                            $scope.trollboxMessages[idx]=data.trollboxMessage;
                        }
                    }
                } else {
                    modal.alert({message:data.result});
                }
            });
        });
    };

    var self=this;

    this.trollboxSendMessage=function() {
        var forumCountryIds = [];
        if($scope.forumCountry.length > 0) {
            angular.forEach($scope.forumCountry,function(item,index){
                forumCountryIds.push(item.id);
            });
            forumCountryIds = forumCountryIds.join(',');
        }

        if (userStatus.status.is_blocked_in_trollbox) {
            modal.alert({message:"Du wurdest für alle Foren von einem Moderator gesperrt"});
        } else {
            $scope.trollbox.$allErrors = [];
            modal.show({
                template: '/app-view/trollbox-message-visibility-popup',
                classes: { 'modal-offer': true },
                trollboxMessage: $scope.trollbox.newMessage,
                trollboxCategoryList: $scope.trollboxCategoryList,
				country_ids: forumCountryIds,
                setTrollbox: function(data) {
                    if(data.trollboxMessage.$allErrors.length===0) {
                        self.updateTrollbox();
                        //$scope.trollboxMessages=data.trollboxMessages;
                        $scope.trollbox.newMessage={};
                        if (data.message) {
                            modal.alert({message:data.message});
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

        /*if ($scope.trollbox.sending) {

            return;
        }
        $scope.trollbox.sending = true;
        $scope.trollbox.$allErrors = [];
        jsonPostDataPromise('/api-trollbox/send-message', { trollboxMessage: $scope.trollbox.newMessage, country: $scope.forumCountry })
            .then(function (res) {
                $scope.trollbox.sending = false;
                if (res.trollboxMessage.$allErrors.length === 0) {
                    $scope.trollbox.newMessage = {};
                    $scope.trollboxMessages = res.trollboxMessages;
                    angular.extend($scope.countryArrayForum, res.countryArrayTrollbox);
                    if (res.message) {
                        modal.alert({ message: res.message });
                    }
                } else {
                    if (angular.isArray(res.trollboxMessage.$allErrors) && res.trollboxMessage.$allErrors.length > 0) {
                        $scope.trollbox = res.trollboxMessage;
                        $scope.trollbox.sending = false;
                        return;
                    }
                }
            }, function () {
                $scope.trollbox.sending = false;
            });*/
    };



    this.gotoForum = function (filter) {
        if(filter!=='MAIN') {
            $state.go('forum');
            $rootScope.paramTrollboxFilterView = filter;
        } else {
            $rootScope.gotoMainTrollboxMessages = true;
            $state.go('userProfile', {'id': userStatus.status.id});
        }
    };

    this.gotoVideoIdentificationList = function() {
        $state.go('forum');
        $rootScope.paramTrollboxFilterType = 'VIDEO_IDENTIFICATION';
    };


    this.inviteMeSlideCallback = function (startIndex, countVisible, direction, doneCallback) {
        if ($scope.loadPageInviteMe && !$scope.loadingInviteMe && direction < 0 && $scope.inviteMe.length >= startIndex + countVisible && $scope.inviteMe.length - startIndex - countVisible <= 2) {
            $scope.loadingInviteMe = true;
            $countries_im = $scope.networkCountry.map(function (a) { return a.id; });
            jsonDataPromise('/api-dashboard/get-invite-me', { pageNum: $scope.pageNumInviteMe + 1, country_ids: angular.toJson($countries_im) })
                .then(function (res) {
                    if (res.inviteMe.length > 0) {
                        $scope.pageNumInviteMe++;
                        $scope.inviteMe = $scope.inviteMe.concat(res.inviteMe);
                        doneCallback();
                    } else {
                        $scope.loadPageInviteMe = false;
                    }
                    $scope.loadingInviteMe = false;
                }, function () {
                    $scope.loadingInviteMe = false;
                });
        }
    };
	/* NVII-MEDIA
	 * START - Dashboard SlideCallback
	 */

    this.offersSlideCallback = function (startIndex, countVisible, direction, doneCallback) {
        if ($scope.loadPageOffers && !$scope.loadingOffers && direction < 0 && $scope.offers.length >= startIndex + countVisible && $scope.offers.length - startIndex - countVisible <= 2) {
            $scope.loadingOffers = true;
            $countries_offer = $scope.offerCountry.map(function (a) { return a.id; });
            jsonDataPromise('/api-dashboard/get-offers', { pageNum: $scope.pageNumOffers + 1, country_ids: angular.toJson($countries_offer) })
                .then(function (res) {
                    if (res.offers.length > 0) {
                        $scope.pageNumOffers++;
                        $scope.offers = $scope.offers.concat(res.offers);
                        doneCallback();
                    } else {
                        $scope.loadPageOffers = false;
                    }
                    $scope.loadingOffers = false;
                }, function () {
                    $scope.loadingOffers = false;
                });
        }
    };
	/* NVII-MEDIA
	 * START - Dashboard SlideCallback
	 */
    this.videosSlideCallback = function (startIndex, countVisible, direction, doneCallback) {
        if ($scope.loadPageVideos && !$scope.loadingVideos && direction < 0 && $scope.videos.length >= startIndex + countVisible && $scope.videos.length - startIndex - countVisible <= 2) {
            $scope.loadingVideos = true;

            jsonDataPromise('/api-dashboard/get-videos', { pageNum: $scope.pageNumVideos + 1 })
                .then(function (res) {
                    if (res.videos.length > 0) {
                        $scope.pageNumVideos++;
                        $scope.videos = $scope.videos.concat(res.videos);
                        doneCallback();
                    } else {
                        $scope.loadPageVideos = false;
                    }
                    $scope.loadingVideos = false;
                }, function () {
                    $scope.loadingVideos = false;
                });
        }
    };
	/* NVII-MEDIA
	 * END - Dashboard SlideCallback
	 */

    this.searchRequestsSlideCallback = function (startIndex, countVisible, direction, doneCallback) {
        if ($scope.loadPageSearchRequest && !$scope.loadingSearchRequest && direction < 0 && $scope.searchRequest.length >= startIndex + countVisible && $scope.searchRequest.length - startIndex - countVisible <= 2) {
            $scope.loadingSearchRequest = true;
            $countries_sr = $scope.searchesCountry.map(function (a) { return a.id; });
            jsonDataPromise('/api-dashboard/get-search-request', { pageNum: $scope.pageNumSearchRequest + 1, country_ids: angular.toJson($countries_sr) })
                .then(function (res) {
                    if (res.searchRequest.length > 0) {
                        $scope.pageNumSearchRequest++;
                        $scope.searchRequest = $scope.searchRequest.concat(res.searchRequest);
                        doneCallback();
                    } else {
                        $scope.loadPageSearchRequest = false;
                    }
                    $scope.loadingSearchRequest = false;
                }, function () {
                    $scope.loadingSearchRequest = false;
                });
        }
    };

    this.enterGroupChat = function (id) {
        $timeout(function () {
            jsonPostDataPromise('/api-trollbox/enter-group-chat', { id: id })
                .then(function (res) {
                    if (res.result === true) {
                        messengerService.talkWithUser(res.groupChatId);
                    }
                });
        });
    };


    this.updateTrollbox = function () {
        if (!$scope.trollbox.loading) {
            $countries_trollbox = $scope.forumCountry.map(function (a) { return a.id; });
            console.log($countries_trollbox);
            $scope.trollbox.loading = true;
            jsonDataPromise('/api-dashboard/get-trollbox', { country_ids: angular.toJson($countries_trollbox) })
                .then(function (res) {

                    $timeout(function () {
                        $scope.trollbox.loading = false;
                    }, 300);
                    angular.extend($scope.trollboxMessages, res.trollboxMessages);
                }, function () {
                    $scope.trollbox.loading = false;
                });
        }
    };
    /*nvii-media*/

    function updateInviteMe() {
        if (!$scope.loadingInviteMe) {
            $countries_im = $scope.networkCountry.map(function (a) { return a.id; });
            $scope.loadingInviteMe = true;
            jsonDataPromise('/api-dashboard/get-invite-me', { pageNum: 1, country_ids: angular.toJson($countries_im) })
                .then(function (res) {
                    $scope.inviteMe = res.inviteMe;
                    $scope.loadingInviteMe = false;
                    $scope.loadPageInviteMe = true;
                    $scope.pageNumInviteMe = 1;
                }, function () {
                    $scope.loadingInviteMe = false;
                });
        }
    }

    function updateOffers() {
        if (!$scope.loadingOffers) {
            $countries_offer = $scope.offerCountry.map(function (a) { return a.id; });
            $scope.loadingOffers = true;
            jsonDataPromise('/api-dashboard/get-offers', { pageNum: 1, country_ids: angular.toJson($countries_offer) })
                .then(function (res) {
                    $scope.offers = res.offers;
                    $scope.loadingOffers = false;
                    $scope.loadPageOffers = true;
                    $scope.pageNumOffers = 1;
                }, function () {
                    $scope.loadingOffers = false;
                });
        }
    }
    function updateSearchRequests() {
        if (!$scope.trollbox.loading) {
            $countries_sr = $scope.searchesCountry.map(function (a) { return a.id; });
            $scope.loadingSearchRequest = true;
            jsonDataPromise('/api-dashboard/get-search-request', { pageNum: 1, country_ids: angular.toJson($countries_sr) })
                .then(function (res) {
                    $scope.searchRequest = res.searchRequest;
                    $scope.loadingSearchRequest = false;
                    $scope.loadPageSearchRequest = true;
                    $scope.pageNumSearchRequest = 1;
                }, function () {
                    $scope.loadingSearchRequest = false;
                });
        }
    }
    function updateTrollboxByCountry() {
        if (!$scope.trollbox.loading) {
            $countries_trollbox = $scope.forumCountry.map(function (a) { return a.id; });
            $scope.trollbox.loading = true;
            jsonDataPromise('/api-dashboard/get-trollbox', { country_ids: angular.toJson($countries_trollbox) })
                .then(function (res) {
                    $scope.trollboxMessages = res.trollboxMessages;
                    $scope.dashboardForumText = res.dashboardForumText;
                    $scope.trollbox.loading = false;
                }, function () {
                    $scope.trollbox.loading = false;
                });
        }
    }


    $scope.labels = $rootScope.status.labels;

	
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

    this.trollboxUnblockUser=function(message) {
        modal.confirmation({message:gettextCatalog.getString('Willst Du wirklich den Benutzer für alle Foren entsperren?')},function(result){
            if (!result) return;

            jsonPostDataPromise('/api-moderator/unblock-user-in-trollbox',{groupChatId:message.id,userId:message.user.id}).then(function(data){
                if (data.result===true) {
                    for(var idx in $scope.trollboxMessages) {
                        if ($scope.trollboxMessages[idx].user.id==message.user.id) {
                            $scope.trollboxMessages[idx].user.is_blocked_in_trollbox=0;
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
                    for(var idx in $scope.trollboxMessages) {
                        if ($scope.trollboxMessages[idx].id==message.id) {
                            $scope.trollboxMessages[idx]=res.trollboxMessage;
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
                    for(var idx in $scope.trollboxMessages) {
                        if ($scope.trollboxMessages[idx].id==message.id) {
                            $scope.trollboxMessages[idx]=res.trollboxMessage;
                        }
                    }
                } else {
                    modal.alert({message:res.result});
                }
            });
    };

    $scope.changedNetworkCountry = function () {
        if ($rootScope.status.status == "ACTIVE") {
            updateInviteMe();
            $timeout.cancel(timer);
            $scope.callDelayInvitedMemberTimeout();
        }
    };

    $scope.changedOfferCountry = function () {
        if ($rootScope.status.status == "ACTIVE") {
            updateOffers();
        }
    };

    $scope.changedSearchRequestsCountry = function () {
        if ($rootScope.status.status == "ACTIVE") {
            updateSearchRequests();
        }
    };

    $scope.changedTrollboxCountry = function () {
        if ($rootScope.status.status == "ACTIVE") {
            updateTrollboxByCountry();
        }
    };



    var preferrer = '';
    try {
        preferrer = top.document.referrer;
    } catch (e) {
        preferrer = '';
    } finally {
        var rndVal = 100 * (Math.random());
    }

    this.setAdvertising = function (id, user_bonus, click_interval, popup_interval) {
        var advertisements = {};
        advertisements.id = id;
        advertisements.user_bonus = user_bonus;
		advertisements.click_interval = click_interval;
		advertisements.popup_interval = popup_interval;
        jsonDataPromise('/api-advertising/set-advertising-user', { advertising_id: id, advertising_click_interval: click_interval })
            .then(function (data) {
                if (user_bonus > 0 && data.result === true) {
                    timeoutPromise = $timeout(function () {
                        var config = {
                            template: '/app-view/advertising-view-bonus-popup',
                            advertising: advertisements
                        };

                        modal.show(config);
                    }, 1000);
                }

            });
    };

    $timeout(function () {
        if ($rootScope.status.currentCountry.country_id) {
            $scope.networkCountry = [{ id: $rootScope.status.currentCountry.country_id }];
            $scope.offerCountry = [{ id: $rootScope.status.currentCountry.country_id }];
            $scope.searchesCountry = [{ id: $rootScope.status.currentCountry.country_id }];
            $scope.forumCountry = [{ id: $rootScope.status.currentCountry.country_id }];
        }
        if((!$rootScope.status.birthday || $rootScope.status.birthday =='0000-00-00' || $rootScope.status.birthday ==='' || !$rootScope.status.city  || $rootScope.status.city==='') && ($rootScope.status.packet=='VIP' || $rootScope.status.packet=='STANDART') && ($rootScope.status.later_profile_fillup_date===1)) {
            $state.go('profile-fillup');
        }
    });
});

