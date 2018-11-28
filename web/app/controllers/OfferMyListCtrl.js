app.controller('OfferMyListCtrl', function ($scope, offerMyListData, $rootScope, $timeout, $state, $stateParams, jsonDataPromise, modal, jsonPostDataPromise, gettextCatalog, messengerService, $anchorScroll) {

    var self=this;

    angular.extend($scope, offerMyListData);

    $scope.filter={
        status:''
    };

    $scope.state={
        pageNum: 1
    };

    if($rootScope.paramFilterView) {
        $scope.filter.status = $rootScope.paramFilterView;
        $rootScope.paramFilterView = '';
    }

    $scope.$watch('filter',function(newValue,oldValue) {
        if (newValue != oldValue) {
            $scope.state.pageNum = 1;
            self.getResults();
        }
    },true);

    this.accept=function(offerRequest,offer) {
        jsonPostDataPromise('/api-offer-request/accept',{id:offerRequest.id})
            .then(function (data) {
                if (data.result===true) {
                    jsonDataPromise('/api-offer-my-list/list',{filter:{id:offer.id},pageNum:1}).then(function(data){
                        for (var idx in $scope.results.items) {
                            if ($scope.results.items[idx].id == data.results.items[0].id) {
                                $scope.results.items[idx]=data.results.items[0];
                            }
                        }
                    });
                    modal.alert({message:gettextCatalog.getString('Du hast erfolgreich einen Kaufinteressenten akzeptiert. Er wurde benachrichtigt.')});
                    return;
                } else {
                    modal.alert({message:data.result});
                }
            });
    };

    this.openChat=function(offerRequest,offer) {
        //messengerService.addSystemMessage(offerRequest.user.id,'/api-offer/open-chat-expired',angular.copy({offerRequestId:offerRequest.id}));
        messengerService.talkWithUser(offerRequest.user.id,gettextCatalog.getString('Kannst Du Dein Gebot für das Angebot "{{title}}" bitte noch einmal wiederholen? Ich würde es gerne annehmen.',{title:offer.title}));
    };

    this.delete=function(id) {
        modal.confirmation({message:gettextCatalog.getString('Willst du Dein Angebot endgültig löschen?')},function(result) {
            if (!result) {
                return;
            }

            jsonPostDataPromise('/api-offer/delete',{id:id})
                .then(function (data) {
                    if (data.result===true) {
                        for (var idx in $scope.results.items) {
                            if ($scope.results.items[idx].id == id) {
                                $scope.results.items.splice(idx, 1);
                            }
                        }
                    }
                });
        });
    };

    $scope.offerRequestFilters={
        'ACTIVE':{isExpired:false},
        'EXPIRED':{isExpired:true}
    };

    this.pause=function(id) {
            jsonPostDataPromise('/api-offer/pause',{id:id})
                .then(function (data) {
                    if (data.result===true) {
                        for (var idx in $scope.results.items) {
                            if ($scope.results.items[idx].id == data.offer.id) {
                                var t=angular.copy($scope.results.items[idx]);
                                angular.extend(t,data.offer);
                                $scope.results.items[idx]=t;
                            }
                        }
                    } else {
                        modal.alert({message:data.result});
                    }
                });
    };

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getResults(function(data) {
            scrollLoadCallback(data.results.hasMore);
        });
    };

    this.getResults = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-offer-my-list/list',{filter:$scope.filter,pageNum:$scope.state.pageNum})
            .then(function (data) {
                $scope.state.loading=false;

                if ($scope.state.pageNum > 1) {
                    data.results.items = $scope.results.items.concat(data.results.items);
                }

                angular.extend($scope, data);

                if ($scope.state.modifiedWhileLoading) {
                    $scope.state.modifiedWhileLoading=false;
                    self.getResults(callback);
                }
                callback(data);
            });
    };

    $scope.$on('offerUpdate',function(event,data) {
        for (var idx in $scope.results.items) {
            if ($scope.results.items[idx].id == data.id) {
                var t=angular.copy($scope.results.items[idx]);
                angular.extend(t,data);
                $scope.results.items[idx]=t;
            }
        }
    });

    this.update=function(offer) {
        jsonDataPromise('/api-offer/update',{id:offer.id}).then(function(data){
            var config={
                template:'/app-view/offers-update-popup',
                classes: {'modal-offer':true}
            };

            angular.extend(config,data);
            modal.show(config);
        });
    };


    if($rootScope.goMyOfferRequestID) {
        for (var idx in $scope.results.items) {
            for (var idy in $scope.results.items[idx].offerRequests) {
                if($scope.results.items[idx].offerRequests[idy].id == $rootScope.goMyOfferRequestID) {
                    $scope.results.items[idx].show_auction_list = true;
                }
            }
        }

        $timeout(function() {
            $anchorScroll('offerRequest-'+$rootScope.goMyOfferRequestID);
            $rootScope.goMyOfferRequestID = null;
        });

    }

    if($rootScope.goMyOfferID) {
        $timeout(function() {
            $anchorScroll('offer-'+$rootScope.goMyOfferID);
            $rootScope.goMyOfferID = null;
        });
    }

    /*this.offerViewUsers = function(id) {
        for (var idx in $scope.results.items) {
            if($scope.results.items[idx].id == id) {
                $scope.results.items[idx].offer_view_users_loader = true;
                $scope.results.items[idx].offer_view_users_load = false;
                jsonDataPromise('/api-offer-my-list/get-offer-view-users',{offer_id:id})
                    .then(function (data) {
                        $scope.results.items[idx].offer_view_users = data;
                        $scope.results.items[idx].offer_view_users_loader = false;
                        $scope.results.items[idx].offer_view_users_load = true;
                    });
                break;
            }
        }
    };*/

    this.offerViewUsers = function(id) {
        for (var idx in $scope.results.items) {
            if($scope.results.items[idx].id == id) {
                $scope.results.items[idx].offer_view_users_loader = true;
                $scope.results.items[idx].offer_view_users_load = false;
                jsonDataPromise('/api-offer-view-log/get-users',{offer_id:id})
                    .then(function (data) {
                        $scope.results.items[idx].offer_view_users = data;
                        $scope.results.items[idx].offer_view_users_loader = false;
                        $scope.results.items[idx].offer_view_users_load = true;
                    });
                break;
            }
        }
    };


    this.offerHistoryView=function(user_id, offer_id) {
        jsonDataPromise('/api-offer-view-log/history',{user_id:user_id, offer_id: offer_id})
            .then(function(data){
                var config={
                    template:'/app-view/offer-history-view-popup',
                    classes: {'modal-offer-history-view':true}
                };
                angular.extend(config,data);
                modal.show(config);
            });
    };




});