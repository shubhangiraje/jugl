var infoPopupService=angular.module('InfoPopupService', []);

infoPopupService.factory('infoPopup',function($rootScope,$timeout,$q,gettextCatalog,modal,jsonDataPromise) {

    var factory={};

    factory.updateViews = function(view) {
        var defer=$q.defer();
        if(infoViewedViews.indexOf(view)<0) {
            jsonDataPromise('/api-user-info-view/update', {view: view})
                .then(function(data) {
                    infoViewedViews = data.result;
                    defer.resolve(true);
                });
        }
        return defer.promise;
    };

    factory.show = function(view) {
        factory.updateViews(view);
        jsonDataPromise('/api-info/get-view', {view: view})
            .then(function(data) {
                modal.showInfo({
                    template:'/app-view/info-popup',
                    classes: {'modal-info':true},
                    infoPopupData: data
                });
            });
    };


    factory.isOneShow = function(view) {
        return infoViewedViews.indexOf(view)<0;
    };


    factory.getInfoViewNavigate = function(view) {
        var views = {
            'info-offer-type':'offers.search',
            'view-activities':'activityList',
            'view-deals-completed':'dealsCompleted',
            'view-earn-money':'earn-money',
            'view-favorites':'favorites',
            'view-forum':'forum',
            'view-funds-web':'funds',
            'view-interests-offer':'interests.index',
            'view-interests-search-request':'interests-searches.index',
            'view-invite':'friendsInvitation.invite',
            'view-invite-log':'friendsInvitation.invitations',
            'view-invite-my-list':'invite-my-list',
            'view-network':'network',
            'view-news':'news',
            'view-offers-index':'offers.index',
            'view-offers-add':'offers.add',
            'view-offers-my':'offers.myList',
            'view-offers-my-requests':'offers.myRequests',
            'view-offers-search':'offers.search',
            'view-profile':'profile',
            'view-searches-add':'searches.add',
            'view-searches-index':'searches.index',
            'view-searches-my':'searches.myList',
            'view-searches-my-offers':'searches.myOffers',
            //'view-searches-offer-add':'searches.addOffer',
            'view-searches-search':'searches.search',
            'view-settings':'profileSettings',
            'view-user-search':'userSearch',
            'view-manage-network':'manageNetwork'
        };
        return views[view];
    };


    return factory;
});
