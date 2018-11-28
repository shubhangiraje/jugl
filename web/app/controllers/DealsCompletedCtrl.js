app.controller('DealsCompletedCtrl', function ($scope, dealsCompletedData, jsonDataPromise, modal, $rootScope, gettextCatalog, jsonPostDataPromise) {

    var self=this;

    angular.extend($scope, dealsCompletedData);

    $scope.filter={
        type:''
    };

    $scope.state={pageNum:1};

    if($rootScope.paramFilterView) {
        $scope.filter.type = $rootScope.paramFilterView;
        $rootScope.paramFilterView = '';
    }

    $scope.$watch('filter',function(newValue,oldValue) {
        if (newValue != oldValue) {
            $scope.state.pageNum = 1;
            self.getResults();
        }
    },true);

    this.loadMore=function(scrollLoadCallback) {
        $scope.state.pageNum++;
        self.getResults(function(data) {
            scrollLoadCallback(data.results.hasMore);
        });
    };

    this.updateUserFeedback=function(id,search_request_offer_id,offer_request_id) {

        jsonDataPromise('api-user-feedback/update',{id:id,search_request_offer_id:search_request_offer_id,offer_request_id:offer_request_id}).then(function(data){
            var config={
                template:'/app-view/deals-completed-update-user-feedback-popup',
                classes: {'modal-offer':true}
            };

            angular.extend(config,data);

            modal.show(config);
        });
    };

    this.updateCounterUserFeedback=function(id,search_request_offer_id,offer_request_id) {

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
    
    this.undelete=function(item) {
        if (item.type=='offer') {
                jsonPostDataPromise('api-offer/undelete',{id:item.deal.id}).then(function(data){
                    if (data.result===true) {
                        item.deal.status=data.status;
                        if (item.dealOffers.length===0) {
                            for(var idx in $scope.results.items) {
                                if ($scope.results.items[idx]==item) {
                                    $scope.results.items.splice(idx,1);
                                    break;
                                }
                            }
                        }
                    }
                });
        }

        if (item.type=='search_request') {
                jsonPostDataPromise('api-search-request/undelete',{id:item.deal.id}).then(function(data){
                    if (data.result===true) {
                        item.deal.status=data.status;
                        if (item.dealOffers.length===0) {
                            for(var idx in $scope.results.items) {
                                if ($scope.results.items[idx]==item) {
                                    $scope.results.items.splice(idx,1);
                                    break;
                                }
                            }
                        }
                    }
                });
        }
    };

    this.unlink=function(item) {
        if (item.type=='offer') {
            modal.confirmation({message:gettextCatalog.getString('Willst du Dein Angebot endgültig löschen?')},function(result) {
                if (!result) {
                    return;
                }

                jsonPostDataPromise('api-offer/unlink',{id:item.deal.id}).then(function(data){
                    if (data.result===true) {
                        for(var idx in $scope.results.items) {
                            if ($scope.results.items[idx]==item) {
                                $scope.results.items.splice(idx,1);
                                break;
                            }
                        }
                    }
                });
            });
        }

        if (item.type=='search_request') {
            modal.confirmation({message:gettextCatalog.getString('Möchtest Du Deine Suchanzeige endgültig löschen?')},function(result) {
                if (!result) {
                    return;
                }

                jsonPostDataPromise('api-search-request/unlink',{id:item.deal.id}).then(function(data){
                    if (data.result===true) {
                        for(var idx in $scope.results.items) {
                            if ($scope.results.items[idx]==item) {
                                $scope.results.items.splice(idx,1);
                                break;
                            }
                        }
                    }
                });
            });
        }

    };

    this.getResults = function(callback) {
        callback = callback || function() {};

        if ($scope.state.loading) {
            $scope.state.modifiedWhileLoading=true;
            return;
        }

        $scope.state.loading=true;

        jsonDataPromise('/api-deals-completed/search',{filter:$scope.filter,pageNum:$scope.state.pageNum})
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


    $rootScope.$on('ratingUpdateParams',function(event,data){

        var idx,idxs;
        var search_request_offer_id = data.search_request_offer_id;
        var offer_request_id = data.offer_request_id;
        var rating = data.rating;

        if(search_request_offer_id > 0) {
            for (idx in $scope.results.items) {
                for(idxs in $scope.results.items[idx].dealOffers) {
                    if($scope.results.items[idx].dealOffers[idxs].id == search_request_offer_id) {
                        $scope.results.items[idx].dealOffers[idxs].rating = rating;
                    }
                }
            }
        }

        if(offer_request_id > 0) {
            for (idx in $scope.results.items) {
                for(idxs in $scope.results.items[idx].dealOffers) {
                    if($scope.results.items[idx].dealOffers[idxs].id == offer_request_id) {
                        $scope.results.items[idx].dealOffers[idxs].rating = rating;
                    }
                }
            }
        }

    });

    $rootScope.$on('counterRatingUpdateParams',function(event,data){

        var idx;
        var search_request_offer_id = data.search_request_offer_id;
        var offer_request_id = data.offer_request_id;
        var rating = data.rating;

        if(search_request_offer_id > 0) {
            for (idx in $scope.results.items) {
                if($scope.results.items[idx].dealOffer) {
                    if($scope.results.items[idx].dealOffer.id==search_request_offer_id) {
                        $scope.results.items[idx].dealOffer.counter_rating = rating;
                    }
                }
            }
        }

        if(offer_request_id > 0) {
            for (idx in $scope.results.items) {
                if($scope.results.items[idx].dealOffer) {
                    if($scope.results.items[idx].dealOffer.id==offer_request_id) {
                        $scope.results.items[idx].dealOffer.counter_rating = rating;
                    }
                }
            }
        }

    });



});
