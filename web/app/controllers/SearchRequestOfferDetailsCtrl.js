app.controller('SearchRequestOfferDetailsCtrl', function ($scope,$stateParams,searchRequestOfferDetailsData,modal,jsonDataPromise,jsonPostDataPromise,$state,messengerService,gettextCatalog) {

    angular.extend($scope, searchRequestOfferDetailsData);

    this.accept=function() {
        $scope.searchRequestOffer.saving = true;
        jsonPostDataPromise('/api-search-request-offer/accept',{id:$scope.searchRequestOffer.id}).then(function(data) {
            $scope.searchRequestOffer.saving = false;
            if (data===false) return;
            if (data.result!==true) {
                modal.confirmation({
                    message: data.result,
                    buttons: [
                        {
                            caption: gettextCatalog.getString('Jugls aufladen'),
                            class: 'ok btn-line',
                            onClick: function() {
                                modal.hide();
                                $state.go('funds.payin');
                            }
                        },
                        {
                            caption: gettextCatalog.getString('Später aufladen'),
                            class: 'cancel btn-line',
                            onClick: function() {
                                modal.hide();
                            }
                        }

                    ]
                },function(result) {});
            } else {
                $state.transitionTo($state.current, $stateParams, {
                    reload: true,
                    inherit: false,
                    notify: true
                });
            }
        });
    };

    this.feedback=function() {
        jsonDataPromise('api-user-feedback/update',{search_request_offer_id:$scope.searchRequestOffer.id}).then(function(data){
            if (data===false) return;
            var config={
                template:'/app-view/deals-completed-update-user-feedback-popup',
                classes: {'modal-offer':true}
            };

            angular.extend(config,data);

            modal.show(config);
        });
    };

    this.contact=function() {
        messengerService.addSystemMessage($scope.searchRequestOffer.user.id,'/api-search-request-offer/mark-as-contacted',angular.copy({id:$scope.searchRequestOffer.id}));
        messengerService.talkWithUser($scope.searchRequestOffer.user.id);
    };

    this.reject=function() {
        var config={
            template:'/app-view/searches-offer-details-reject-popup',
            classes: {'modal-reject':true},
            searchRequestOffer: $scope.searchRequestOffer
        };

        modal.show(config);


        //modal.confirmation({message:gettext('Möchten Sie dieses Angebot wirklich ablehnen?')},function(result){
        //    if (!result) {
        //        return;
        //    }
        //
        //    jsonPostDataPromise('/api-search-request-offer/reject',{id:$scope.searchRequestOffer.id})
        //        .then(function (data) {
        //            $state.go('searches.myList');
        //        });
        //});
    };





});
