app.controller('OfferDetailsCtrl', function ($scope,$state,offerDetailsData,jsonDataPromise,modal,$timeout,$interval,userStatus,jsonPostDataPromise,messengerService,gettextCatalog,$rootScope) {

    angular.extend($scope, offerDetailsData);
    $rootScope.offerViewLogId = $scope.offer.offer_view_log_id;
    $scope.status=userStatus.status;

    function doRequest() {
        if ($scope.offer.type=='AUCTION') {
            $state.go('offers.bet',{offer_id:$scope.offer.id});
            return;
        }

        if ($scope.offer.type=='AUTOSELL') {
            modal.confirmation({message:gettextCatalog.getString('„Mit OK bestätigst Du den verbindlichen Kauf des Angebots.“')},function(result) {
                if (result) {
                    jsonPostDataPromise('/api-offer-request/save',{offerRequest:{offer_id:$scope.offer.id}})
                        .then(function (data) {
                            if (data.result!==true) {
                                modal.alert({message:data.result});
                            } else {
                                $state.go('offers.pay',{id:data.offerRequest.id});
                            }
                        });
                }
            });
        }
    }

    this.request=function() {
        if ($scope.offer.userAccepted) {
            modal.confirmation({message:gettextCatalog.getString('Du hast bereits auf diesen Artikel geboten, bist du dir sicher, dass du ein weiteres Gebot abgeben möchtest?')},function(result) {
                if (result) {
                    doRequest();
                }
            });
        } else {
            doRequest();
        }
    };

    var timeoutPromise=null;

    if (angular.isString($scope.offer.viewBonusCode)) {
        timeoutPromise=$timeout(function(){
            var config={
                template:'/app-view/offers-details-view-bonus-popup',
                offer: $scope.offer
            };

            modal.show(config);
        },$scope.offer.viewBonusDelay*1000);
    }

    $scope.$on('spamReported',function() {
        $scope.offer.spamReported=true;
    });

    this.openChat=function() {
        messengerService.addSystemMessage($scope.offer.user.id,'/api-offer/open-chat',angular.copy({offer:$scope.offer}));
        messengerService.talkWithUser($scope.offer.user.id);
    };

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

        jsonPostDataPromise('/api-favorites/add',{id:id, type:'offer'})
            .then(function (data) {
                if (data.result===true) {
                    $scope.offer.favorite = true;
                }
            });

    };

    this.activeImageChanged = function(idx) {
        if($scope.offer.bigImages) {
            $scope.offer.fancyboxImages = [];
            for (var i = idx; i < $scope.offer.bigImages.length; i++) {
                $scope.offer.fancyboxImages.push($scope.offer.bigImages[i]);
            }

            for (var y = 0; y < idx; y++) {
                $scope.offer.fancyboxImages.push($scope.offer.bigImages[y]);
            }
        }
    };


    this.openAgb = function() {
        var config={
            template:'/app-view/user-agb-popup',
            classes: {'modal-agb':true},
            offer: $scope.offer
        };
        modal.show(config);
    };


    var intervalPromise = null;
    
    intervalPromise=$interval(function(){
        jsonDataPromise('/api-offer-view-log/update',{id: $scope.offer.offer_view_log_id}).then(function (data) {
            $scope.offer.count_offer_view = data.count_offer_view;
        },function(){});
    },5*1000);


    $scope.$on('$destroy',function() {
        $timeout.cancel(timeoutPromise);
        $interval.cancel(intervalPromise);
    });




});
