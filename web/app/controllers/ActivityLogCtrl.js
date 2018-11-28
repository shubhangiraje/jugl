app.controller('ActivityLogCtrl', function ($timeout, $scope, $rootScope, activityLogData, jsonDataPromise, jsonPostDataPromise, modal,gettextCatalog, $state, userStatus, messengerService, infoPopup) {

    angular.extend($scope, activityLogData);

    var self = this;

    $scope.state={pageNum:1};
    $scope.dateTimeFormat=gettextCatalog.getString("dd MMMM 'um' HH:mm");

    $scope.mode = 'event';

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getActivityLog(function(data) {
            scrollLoadCallback(data.log.hasMore);
        });
    };

    this.resetFilter = function() {
        this.filters = {
            type: ''
        };
    };

    this.resetFilter();

    this.vip=function() {
        $state.go('packetUpgrade');
    };

    this.goProfile=function(id) {
        if (id) {
            $state.go('userProfile',{id:id});
        }
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

    this.toggleBlockParentTeamRequests=function() {
        jsonPostDataPromise('/api-user/toggle-block-parent-team-requests').then(function(data){
            if (data.result===true) {
                userStatus.update();
            } else {
                modal.alert({message:data.result});
            }
        });
    };

    this.updateDealFeedback=function(id,search_request_offer_id,offer_request_id) {

        jsonDataPromise('api-user-feedback/update',{id:id,search_request_offer_id:search_request_offer_id,offer_request_id:offer_request_id}).then(function(data){
            var config={
                template:'/app-view/deals-completed-update-user-feedback-popup',
                classes: {'modal-offer':true}
            };

            angular.extend(config,data);

            modal.show(config);
        });
    };

    this.offerRequestFeedback=function(id) {
        this.updateDealFeedback(null,null,id);
    };

    this.searchRequestOfferFeedback=function(id) {
        this.updateDealFeedback(null,id,null);
    };


    this.updateDealCounterFeedback=function(id,search_request_offer_id,offer_request_id) {

        jsonDataPromise('api-user-feedback/counter-update',{id:id,search_request_offer_id:search_request_offer_id,offer_request_id:offer_request_id}).then(function(data){
            var config={
                template:'/app-view/deals-completed-update-user-feedback-popup',
                classes: {'modal-offer':true},
                counter_feedback: true
            };

            angular.extend(config,data);

            modal.show(config);
        });
    };


    this.offerRequestCounterFeedback=function(id) {
        this.updateDealCounterFeedback(null,null,id);
    };

    this.searchRequestOfferCounterFeedback=function(id) {
        this.updateDealCounterFeedback(null,id,null);
    };


    this.teamleaderFeedback=function(userId) {
        $rootScope.scrollToTeamlederFeedback=true;
        $state.go('userProfile',{id:userStatus.status.id});
    };

    this.teamleaderFeedbacks=function(userId) {
        $rootScope.scrollToTeamlederFeedbacks=true;
        $state.go('userProfile',{id:userStatus.status.id});
    };

    $scope.$on('updateUserEvents',function(event,events) {
        for(var eventIdx in events) {
            for(var idx in $scope.log.items) {
                if ($scope.log.items[idx].id==events[eventIdx].id) {
                    var t=angular.copy($scope.log.items[idx]);
                    angular.extend(t,events[eventIdx]);
                    $scope.log.items[idx]=t;
                    break;
                }
            }
        }
    });

    this.userTeamRequestAccept=function(fromUserId) {
        function processAccept() {
            jsonPostDataPromise('/api-user-team-request/accept', {fromUserId: fromUserId}).then(function (data) {
                if (data.result === true) {
                    for (var eventIdx in data.events) {
                        for (var idx in $scope.log.items) {
                            if ($scope.log.items[idx].id == data.events[eventIdx].id) {
                                var t = angular.copy($scope.log.items[idx]);
                                angular.extend(t, data.events[eventIdx]);
                                $scope.log.items[idx] = t;
                                break;
                            }
                        }
                    }

                    //                modal.alert({message:gettextCatalog.getString('Geldeingang wurde erfolgreich bestätigt')});
                } else {
                    modal.alert({message: data.result});
                }
            });
        }

        jsonDataPromise('/api-user-team-request/get-type',{fromUserId:fromUserId}).then(function(data) {
            if (data.result === true) {
                if (data.type=='PARENT_TO_REFERRAL') {
                    modal.confirmation({
                        message: gettextCatalog.getString('Achtung! Durch die Annahme dieser Teamanfrage verlässt Du Dein aktuelles Team.- Tu dies nur, wenn Du mit Deinem aktuellen Teamleiter unzufrieden bist.'),
                        successBtn: gettextCatalog.getString('Ja, Teamleiter wechseln'),
                        cancelBtn: gettextCatalog.getString('Nein, beim aktuellen Teamleiter bleiben')
                    }, function (result) {
                        if (result) {
                            processAccept();
                        }
                    });
                } else {
                    processAccept();
                }
            } else {
                modal.alert({message: data.result});
            }
        });
    };

    this.userTeamRequestDecline=function(fromUserId) {
        //modal.confirmYesCancel({message:gettextCatalog.getString('Möchten sie wirklich Annehmen?')}).then(function(buttonIndex) {
        //if (buttonIndex==1) {
        jsonPostDataPromise('/api-user-team-request/decline',{fromUserId:fromUserId}).then(function(data){
            if (data.result===true) {
                for(var eventIdx in data.events) {
                    for(var idx in $scope.log.items) {
                        if ($scope.log.items[idx].id==data.events[eventIdx].id) {
                            var t=angular.copy($scope.log.items[idx]);
                            angular.extend(t,data.events[eventIdx]);
                            $scope.log.items[idx]=t;
                            break;
                        }
                    }
                }

                //                modal.alert({message:gettextCatalog.getString('Geldeingang wurde erfolgreich bestätigt')});
            } else {
                modal.alert({message:data.result});
            }
        });
        //}
        //});
    };

    this.offerRequestPayConfirm=function(id) {
        modal.confirmation({message:gettextCatalog.getString('Möchten sie wirklich Geldeingang bestätigen?')},function(result){
            if (result) {
                jsonPostDataPromise('/api-offer/pay-confirm',{id:id}).then(function(data){
                    if (data.result===true) {
                        for(var eventIdx in data.events) {
                            for(var idx in $scope.log.items) {
                                if ($scope.log.items[idx].id==data.events[eventIdx].id) {
                                    var t=angular.copy($scope.log.items[idx]);
                                    angular.extend(t,data.events[eventIdx]);
                                    $scope.log.items[idx]=t;
                                    break;
                                }
                            }
                        }
                        modal.alert({message:gettextCatalog.getString('Geldeingang wurde erfolgreich bestätigt')});
                    } else {
                        modal.alert({message:data.result});
                    }
                });
            }
        });
    };

    this.spamReportDeactivate=function(event_id,id) {
        modal.confirmation({message:gettextCatalog.getString('Möchten sie wirklich Spammeldung zurücknehmen?')},function(result){
            if (result) {
                jsonPostDataPromise('/api-spam-report/deactivate',{id:id,event_id:event_id}).then(function(data){
                    if (data.result===true) {
                        for(var eventIdx in data.events) {
                            for(var idx in $scope.log.items) {
                                if ($scope.log.items[idx].id==data.events[eventIdx].id) {
                                    var t=angular.copy($scope.log.items[idx]);
                                    angular.extend(t,data.events[eventIdx]);
                                    $scope.log.items[idx]=t;
                                    break;
                                }
                            }
                        }
                        //modal.alert({message:gettextCatalog.getString('Geldeingang wurde erfolgreich bestätigt')});
                    } else {
                        modal.alert({message:data.result});
                    }
                });
            }
        });
    };

    $rootScope.$on('activityListUpdate',function(event,data) {
        for(var eventIdx in data.events) {
            for(var idx in $scope.log.items) {
                if ($scope.log.items[idx].id==data.events[eventIdx].id) {
                    var t=angular.copy($scope.log.items[idx]);
                    angular.extend(t,data.events[eventIdx]);
                    $scope.log.items[idx]=t;
                    break;
                }
            }
        }
    });

    this.offerRequestPayNotifyBuyer=function(id) {
        jsonDataPromise('/api-offer-request/payment-complaint',{id:id}).then(function(data){
                var config={
                    template:'/app-view/offer-request-payment-complaint-popup',
                    classes: {'modal-offer':true},
                };

                angular.extend(config,data);

                modal.show(config);
            });
        };

        /*
        modal.confirmation({message:gettextCatalog.getString('Möchten sie wirklich Geldeingang bestätigen?')},function(result){
            if (result) {
                jsonPostDataPromise('api-offer/pay-confirm',{id:id}).then(function(data){
                    if (data.result===true) {
                        for(var eventIdx in data.events) {
                            for(var idx in $scope.log.items) {
                                if ($scope.log.items[idx].id==data.events[eventIdx].id) {
                                    var t=angular.copy($scope.log.items[idx]);
                                    angular.extend(t,data.events[eventIdx]);
                                    $scope.log.items[idx]=t;
                                    break;
                                }
                            }
                        }
                        modal.alert({message:gettextCatalog.getString('Geldeingang wurde erfolgreich bestätigt')});
                    } else {
                        modal.alert({message:data.result});
                    }
                });
            }
        });
        */

    $scope.$watch('activityLogCtrl.filters',function(new_value, old_value) {
        if (new_value != old_value) {
            $scope.state.pageNum = 1;
            self.getActivityLog();
        }
    }, true);

    this.getActivityLog = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise($scope.mode == 'event' ? '/api-event-log/log' : '/api-follower-event-log/log', {
            type: self.filters.type,
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
                self.getActivityLog(callback);
            }
            callback(data);
        });
    };

    this.goMyOfferRequest = function(param) {
        $rootScope.goMyOfferRequestID = param.id;
        $state.go('offers.myList');
    };

    this.goMyOffer = function(param) {
        $rootScope.goMyOfferID = param.id;
        $state.go('offers.myList');
    };

    this.changeMode = function(mode) {
        switch (mode) {
            case 'event':
                $scope.mode='event_follower';
                break;
            case 'event_follower':
                $scope.mode='event';
                break;
        }
        $scope.state.pageNum = 1;
        this.resetFilter();
        this.getActivityLog();
    };

    this.goInfoView = function(view) {
        if(view=='view-funds-app') view = 'view-funds-web';
        var route = infoPopup.getInfoViewNavigate(view);
        if(route!==undefined) {
            $state.go(route);
            infoPopup.show(view);
        }
    };

    this.networkAcceptMoving = function(fromId, toId, userId) {
        jsonPostDataPromise('/api-manage-network/accept-moving',{fromId:fromId,toId:toId,userId:userId}).then(function(data){
            if (data.result===true) {
                for(var eventIdx in data.events) {
                    for(var idx in $scope.log.items) {
                        if ($scope.log.items[idx].id==data.events[eventIdx].id) {
                            var t=angular.copy($scope.log.items[idx]);
                            angular.extend(t,data.events[eventIdx]);
                            $scope.log.items[idx]=t;
                            break;
                        }
                    }
                }
                userStatus.update();
            } else {
                modal.alert({message:data.result});
            }
        });
    };

    this.networkRejectMoving = function(fromId, toId, userId) {
        jsonPostDataPromise('/api-manage-network/reject-moving',{fromId:fromId,toId:toId,userId:userId}).then(function(data){
            if (data.result===true) {
                for(var eventIdx in data.events) {
                    for(var idx in $scope.log.items) {
                        if ($scope.log.items[idx].id==data.events[eventIdx].id) {
                            var t=angular.copy($scope.log.items[idx]);
                            angular.extend(t,data.events[eventIdx]);
                            $scope.log.items[idx]=t;
                            break;
                        }
                    }
                }
            } else {
                modal.alert({message:data.result});
            }
        });
    };

    this.stickParentAccept = function() {
        jsonPostDataPromise('/api-user-profile/stick-parent-accept',{}).then(function(data){
            if (data.result===true) {
                for(var eventIdx in data.events) {
                    for(var idx in $scope.log.items) {
                        if ($scope.log.items[idx].id==data.events[eventIdx].id) {
                            var t=angular.copy($scope.log.items[idx]);
                            angular.extend(t,data.events[eventIdx]);
                            $scope.log.items[idx]=t;
                            break;
                        }
                    }
                }
            } else {
                modal.alert({message:data.result});
            }
        });
    };

    this.stickParentReject = function() {
        jsonPostDataPromise('/api-user-profile/stick-parent-reject',{}).then(function(data){
            if (data.result===true) {
                for(var eventIdx in data.events) {
                    for(var idx in $scope.log.items) {
                        if ($scope.log.items[idx].id==data.events[eventIdx].id) {
                            var t=angular.copy($scope.log.items[idx]);
                            angular.extend(t,data.events[eventIdx]);
                            $scope.log.items[idx]=t;
                            break;
                        }
                    }
                }
            } else {
                modal.alert({message:data.result});
            }
        });
    };

});
