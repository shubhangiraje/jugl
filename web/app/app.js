var app = angular.module('Jugl', [
        'ui.router',
        'once',
        'angularFileUpload',
        'ngTouch',
        'UserStatusService',
        'InviteService',
        'ModalService',
        'MessengerService',
        'ui.bootstrap.accordion',
        'ui.bootstrap.tpls',
        'angular-bootstrap-select',
        'angular-bootstrap-select.extra',
		'btorfs.multiselect',
        'ngJScrollPane',
        'templates',
        'gettext',
        'ngSanitize',
        'ngCookies',
        'ngAnimate',
        'tmh.dynamicLocale',
        'InfoPopupService',
        'ServerTimeService',
        'ng.deviceDetector',
        'ngStorage',
        'UserSettingsService',
        'AdsenseService',
        'ps-facebook-pixel'
    ])
    .config(function($stateProvider, $urlRouterProvider, tmhDynamicLocaleProvider, $localStorageProvider, $pixelProvider) {
        $localStorageProvider.setKeyPrefix('ngStorage_');

        // Facebook Pixel config
        if (config.useFacebookPixel) {
            $pixelProvider.id = '381896798941261';
            $pixelProvider.disablePushState = true;
            $pixelProvider.delayPageView = true;
        }

        $stateProvider
            // .state('dashboard', {
            //     url: '/dashboard/:urlState',
            //     templateUrl: '/app-view/dashboard',
            //     controller: 'DashboardCtrl',
            //     controllerAs: 'dashboardCtrl',
            //     resolve: {
            //         dashboardData: function(jsonDataPromise,$state,$stateParams) {
            //             if(!$stateParams.realStateChange) return;
            //             return jsonDataPromise('/api-dashboard/index',{urlState:$stateParams.urlState});
            //         }
            //     }
            // })
            .state('dashboard', {
                url: '/dashboard/:urlState',
                templateUrl: '/app-view/dashboard-new',
                controller: 'DashboardNewCtrl',
                controllerAs: 'dashboardCtrl',
                resolve: {
                    dashboardData: function(jsonDataPromise,$state,$stateParams) {
                        if(!$stateParams.realStateChange) return;
                        return jsonDataPromise('/api-dashboard/index-new',{urlState:$stateParams.urlState});
                    }
                }
            })
            .state('network', {
                url: '/network/:urlState',
                templateUrl: '/app-view/network',
                controller: 'NetworkCtrl',
                controllerAs: 'networkCtrl',
                resolve: {
                    networkData: function(jsonDataPromise,$state,$stateParams) {
                        if(!$stateParams.realStateChange) return;
                        return jsonDataPromise('/api-network/index',{urlState:$stateParams.urlState});
                    }
                }
            })
            .state('user-network', {
                url: '/user-network/:id/:urlState',
                templateUrl: '/app-view/user-network',
                controller: 'UserNetworkCtrl',
                controllerAs: 'UserNetworkCtrl',
                resolve: {
                    userNetworkData: function(jsonDataPromise,$state,$stateParams) {
                        if(!$stateParams.realStateChange) return;
                        return jsonDataPromise('/api-network/user-network',{id:$stateParams.id, urlState:$stateParams.urlState});
                    }
                }
            })
            .state('friends', {
                url: '/friends/:urlState',
                templateUrl: '/app-view/friends',
                controller: 'FriendsCtrl',
                controllerAs: 'friendsCtrl',
                resolve: {
                    friendsData: function(jsonDataPromise,$state,$stateParams) {
                        if(!$stateParams.realStateChange) return;
                        return jsonDataPromise('/api-friends/index',{urlState:$stateParams.urlState});
                    }
                }
            })
            .state('networkMembers', {
                url: '/network-members/:urlState',
                templateUrl: '/app-view/network-members',
                controller: 'NetworkMembersCtrl',
                controllerAs: 'networkMembersCtrl',
                resolve: {
                    networkMembersData: function(jsonDataPromise,$state,$stateParams) {
                        if(!$stateParams.realStateChange) return;
                        return jsonDataPromise('/api-network-members/index',{urlState:$stateParams.urlState});
                    }
                }
            })
            .state('welcome', {
                url: '/welcome',
                templateUrl: '/app-view/profile-update',
                controller: 'ProfileUpdateCtrl',
                controllerAs: 'profileUpdateCtrl',
                resolve: {
                    profileData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-profile/index');
                    }
                }
            })
            .state('profile', {
                url: '/profile',
                templateUrl: '/app-view/profile-update',
                controller: 'ProfileUpdateCtrl',
                controllerAs: 'profileUpdateCtrl',
                resolve: {
                    profileData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-profile/index');
                    }
                }
            })
            .state('funds', {
                url: '/funds',
                templateUrl: '/app-view/funds',
                controller: function($state) {
                    if ($state.is('funds')) {
                        $state.go('funds.log');
                    }
                }
            })
            .state('funds.log', {
                url: '/log',
                templateUrl: '/app-view/funds-log',
                controller: 'FundsLogCtrl',
                controllerAs: 'fundsLogCtrl',
                resolve: {
                    fundsLogData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-funds-log/index');
                    }
                }
            })
            .state('funds.log-token', {
                url: '/log-token',
                templateUrl: '/app-view/funds-log-token',
                controller: 'FundsLogTokenCtrl',
                controllerAs: 'fundsLogTokenCtrl',
                resolve: {
                    fundsLogTokenData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-funds-log-token/index');
                    }
                }
            })
            .state('funds.token-deposit', {
                url: '/token-deposit',
                templateUrl: '/app-view/funds-token-deposit',
                controller: 'FundsTokenDepositCtrl',
                controllerAs: 'fundsTokenDepositCtrl',
                resolve: {
                    fundsTokenDepositData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-funds-token-deposit/index');
                    }
                }
            })
            .state('funds.payin', {
                url: '/payin',
                templateUrl: '/app-view/funds-pay-in',
                controller: 'FundsPayInCtrl',
                controllerAs: 'fundsPayInCtrl',
                resolve: {
                    fundsPayInData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-funds-pay-in/index');
                    }
                }
            })
            .state('funds.payin.data', {
                url: '/data/:requestId',
                templateUrl: '/app-view/funds-pay-in-data',
                controller: 'FundsPayInDataCtrl',
                controllerAs: 'fundsPayInDataCtrl',
                resolve: {
                    fundsPayInDataData: function($state,$stateParams,jsonDataPromise) {
                        return jsonDataPromise('/api-funds-pay-in-data/index',{
                            requestId:$stateParams.requestId,
                            retUrl:$state.href('funds.payinResult',{requestId:$stateParams.requestId,result:''},{absolute:true}).replace('/#','/my#')
                        });
                    }
                }
            })
            .state('funds.payinResult', {
                url: '/payin-result/:requestId/:result',
                templateUrl: '/app-view/funds-pay-in-result',
                controller: 'FundsPayInResultCtrl',
                controllerAs: 'fundsPayInResultCtrl',
                resolve: {
                    fundsPayInResultData: function($state,$stateParams,jsonPostDataPromise) {
                        return jsonPostDataPromise('/api-funds-pay-in-result/index',{
                            requestId:$stateParams.requestId,
                            returnStatus:$stateParams.result
                        });
                    }
                }
            })
            .state('funds.payout', {
                url: '/payout',
                templateUrl: '/app-view/funds-pay-out',
                controller: 'FundsPayOutCtrl',
                controllerAs: 'fundsPayOutCtrl',
                resolve: {
                    fundsPayOutData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-funds-pay-out/index');
                    }
                }
            })
            .state('registrationPayment', {
                url: '/registration-payment',
                templateUrl: '/app-view/registration-payment',
                controller: 'RegistrationPaymentCtrl',
                controllerAs: 'registrationPaymentCtrl',
                resolve: {
                    registrationPaymentData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-registration-payment/index');
                    },
                    isUpgrade: function() {
                        return false;
                    }
                }
            })
            .state('packetUpgrade', {
                url: '/packet-upgrade',
                templateUrl: '/app-view/registration-payment',
                controller: 'RegistrationPaymentCtrl',
                controllerAs: 'registrationPaymentCtrl',
                resolve: {
                    registrationPaymentData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-registration-payment/index');
                    },
                    isUpgrade: function() {
                        return true;
                    }
                }
            })
            .state('packetUpgrade.data', {
                url: '/data/:requestId',
                templateUrl: '/app-view/funds-pay-in-data',
                controller: 'FundsPayInDataCtrl',
                controllerAs: 'fundsPayInDataCtrl',
                resolve: {
                    fundsPayInDataData: function($state,$stateParams,jsonDataPromise) {
                        return jsonDataPromise('/api-funds-pay-in-data/index',{
                            requestId:$stateParams.requestId,
                            retUrl:$state.href('welcome',{requestId:$stateParams.requestId,result:''},{absolute:true}).replace('/#','/my#')+'?'
                        });
                    }
                }
            })
            .state('registrationPayment.data', {
                url: '/data/:requestId',
                templateUrl: '/app-view/funds-pay-in-data',
                controller: 'FundsPayInDataCtrl',
                controllerAs: 'fundsPayInDataCtrl',
                resolve: {
                    fundsPayInDataData: function($state,$stateParams,jsonDataPromise) {
                        return jsonDataPromise('/api-funds-pay-in-data/index',{
                            requestId:$stateParams.requestId,
                            retUrl:$state.href('welcome',{requestId:$stateParams.requestId,result:''},{absolute:true}).replace('/#','/my#')+'?'
                        });
                    }
                }
            })
            .state('friendsInvitation', {
                url: '/friends-invitation',
                templateUrl: '/app-view/friends-invitation',
                controller: 'FriendsInvitationCtrl',
                controllerAs: 'friendsInvitationCtrl',
                resolve: {
                    friendsInvitationData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-friends-invitation/index');
                    }
                }
            })
            .state('friendsInvitation.invite', {
                url: '/invite',
                templateUrl: '/app-view/friends-invitation-invite',
                controller: 'FriendsInvitationInviteCtrl',
                controllerAs: 'friendsInvitationInviteCtrl',
                resolve: {
                    friendsInvitationInviteData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-friends-invitation-invite/index');
                    }
                }
            })
            .state('friendsInvitation.invitations', {
                url: '/invitations',
                templateUrl: '/app-view/friends-invitation-invitations',
                controller: 'FriendsInvitationInvitationsCtrl',
                controllerAs: 'friendsInvitationInvitationsCtrl',
                resolve: {
                    friendsInvitationInvitationsData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-friends-invitation-invitations/index');
                    }
                }
            })
            .state('friendsInvitation.regcodes', {
                url: '/regcodes',
                templateUrl: '/app-view/friends-invitation-regcodes',
                controller: 'FriendsInvitationRegcodesCtrl',
                controllerAs: 'friendsInvitationRegcodesCtrl',
                resolve: {
                    friendsInvitationRegcodesData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-friends-invitation-regcodes/index');
                    }
                }
            })
            .state('help', {
                url: '/help/',
                templateUrl: '/app-view/help'
            })
            .state('userProfile', {
                url: '/user-profile/:id',
                templateUrl: '/app-view/user-profile',
                controller: 'UserProfileCtrl',
                controllerAs: 'userProfileCtrl',
                resolve: {
                    userProfileData: function(jsonDataPromise,$stateParams) {
                        return jsonDataPromise('/api-user-profile/index',{id:$stateParams.id});
                    }
                }
            })
            .state('profileSettings', {
                url: '/user-profile-settings',
                templateUrl: '/app-view/settings',
                controller: 'SettingsCtrl',
                controllerAs: 'settingsCtrl',
                resolve: {
                    userProfileSettingsData: function(jsonDataPromise,$stateParams,userSettings) {
                        return userSettings.update();
                    }
                }
            })
            .state('userSearch', {
                url: '/user-search/:urlState',
                templateUrl: '/app-view/user-search',
                controller: 'UserSearchCtrl',
                controllerAs: 'userSearchCtrl',
                resolve: {
                    userSearchData: function(jsonDataPromise,$state,$stateParams) {
                        if(!$stateParams.realStateChange) return;
                        return jsonDataPromise('/api-user-search/index',{urlState:$stateParams.urlState});
                    }
                }
            })
            .state('friendRequestAccept', {
                url: '/friend-request-accept/:id',
                templateUrl: '/app-view/action-result',
                controller: 'ActionResultCtrl',
                controllerAs: 'actionResultCtrl',
                resolve: {
                    actionResultData: function(jsonPostDataPromise,$stateParams) {
                        return jsonPostDataPromise('/api-friends/request-accept',{id:$stateParams.id});
                    }
                }
            })
            .state('friendRequestDecline', {
                url: '/friend-request-decline/:id',
                templateUrl: '/app-view/action-result',
                controller: 'ActionResultCtrl',
                controllerAs: 'actionResultCtrl',
                resolve: {
                    actionResultData: function(jsonPostDataPromise,$stateParams) {
                        return jsonPostDataPromise('/api-friends/request-decline',{id:$stateParams.id});
                    }
                }
            })
            .state('functions', {
                url: '/all-functions',
                templateUrl: '/app-view/all-functions',
                controller: 'AllFunctionsCtrl',
                controllerAs: 'allFunctionsCtrl'
            })
            .state('ueberUns', {
                url: '/ueber-uns',
                templateUrl: '/app-view/ueber-uns'
            })
            .state('impressum', {
                url: '/impressum',
                templateUrl: '/app-view/impressum'
            })
            .state('agbs', {
                url: '/agbs',
                templateUrl: '/app-view/agbs'
            })
            .state('nutzungsbedingungen', {
                url: '/nutzungsbedingungen',
                templateUrl: '/app-view/nutzungsbedingungen'
            })
            .state('datenschutz', {
                url: '/datenschutz',
                templateUrl: '/app-view/datenschutz'
            })
            .state('interests', {
                abstract: true,
                url: '/interests',
                template: "<ui-view/>"
            })
            .state('interests.index', {
                url: '/index',
                templateUrl: '/app-view/interests-index',
                controller: 'UserInterestsCtrl',
                controllerAs: 'userInterestsCtrl',
                resolve: {
                    userInterestsData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-user-interests/index',{type:'OFFER'});
                    }
                }
            })
            .state('interests.addStep1', {
                url: '/addstep1',
                templateUrl: '/app-view/interests-addstep1',
                controller: 'InterestsAddStep1Ctrl',
                controllerAs: 'interestsAddStep1Ctrl',
                resolve: {
                    interestsAddStep1Data: function(jsonDataPromise) {
                        return jsonDataPromise('/api-interests/add-step1',{type:'OFFER'});
                    },
                    type: function() {
                        return 'addInterest';
                    },
                    interestType: function() {
                        return 'OFFER';
                    }
               }
            })
            .state('interests.addStep2', {
                url: '/addstep2/:id',
                templateUrl: '/app-view/interests-addstep2',
                controller: 'InterestsAddStep2Ctrl',
                controllerAs: 'interestsAddStep2Ctrl',
                resolve: {
                    interestsAddStep2Data: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-interests/add-step2',{parent_id:$stateParams.id});
                    },
                    type: function() {
                        return 'addInterest';
                    },
                    interestType: function() {
                        return 'OFFER';
                    }
                }
            })
            .state('interests.addStep3', {
                url: '/addstep3/:id',
                templateUrl: '/app-view/interests-addstep3',
                controller: 'InterestsAddStep3Ctrl',
                controllerAs: 'interestsAddStep3Ctrl',
                resolve: {
                    interestsAddStep3Data: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-interests/add-step3',{parent_id:$stateParams.id});
                    },
                    type: function() {
                        return 'addInterest';
                    },
                    interestType: function() {
                        return 'OFFER';
                    }
                }
            })
            .state('interests.update', {
                url: '/update/:id',
                templateUrl: '/app-view/interests-update',
                controller: 'UserInterestsUpdateCtrl',
                controllerAs: 'userInterestsUpdateCtrl',
                resolve: {
                    userInterestsUpdateData: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-user-interests/update',{id:$stateParams.id});
                    }
                }
            })

            .state('interests-searches', {
                abstract: true,
                url: '/interests-searches',
                template: "<ui-view/>"
            })
            .state('interests-searches.index', {
                url: '/index',
                templateUrl: '/app-view/interests-index',
                controller: 'UserInterestsCtrl',
                controllerAs: 'userInterestsCtrl',
                resolve: {
                    userInterestsData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-user-interests/index',{type:'SEARCH_REQUEST'});
                    }
                }
            })
            .state('interests-searches.addStep1', {
                url: '/addstep1',
                templateUrl: '/app-view/interests-addstep1',
                controller: 'InterestsAddStep1Ctrl',
                controllerAs: 'interestsAddStep1Ctrl',
                resolve: {
                    interestsAddStep1Data: function(jsonDataPromise) {
                        return jsonDataPromise('/api-interests/add-step1',{type:'SEARCH_REQUEST'});
                    },
                    type: function() {
                        return 'addInterest';
                    },
                    interestType: function() {
                        return 'SEARCH_REQUEST';
                    }
                }
            })
            .state('interests-searches.addStep2', {
                url: '/addstep2/:id',
                templateUrl: '/app-view/interests-addstep2',
                controller: 'InterestsAddStep2Ctrl',
                controllerAs: 'interestsAddStep2Ctrl',
                resolve: {
                    interestsAddStep2Data: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-interests/add-step2',{parent_id:$stateParams.id});
                    },
                    type: function() {
                        return 'addInterest';
                    },
                    interestType: function() {
                        return 'SEARCH_REQUEST';
                    }
                }
            })
            .state('interests-searches.addStep3', {
                url: '/addstep3/:id',
                templateUrl: '/app-view/interests-addstep3',
                controller: 'InterestsAddStep3Ctrl',
                controllerAs: 'interestsAddStep3Ctrl',
                resolve: {
                    interestsAddStep3Data: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-interests/add-step3',{parent_id:$stateParams.id});
                    },
                    type: function() {
                        return 'addInterest';
                    },
                    interestType: function() {
                        return 'SEARCH_REQUEST';
                    }
                }
            })
            .state('interests-searches.update', {
                url: '/update/:id',
                templateUrl: '/app-view/interests-update',
                controller: 'UserInterestsUpdateCtrl',
                controllerAs: 'userInterestsUpdateCtrl',
                resolve: {
                    userInterestsUpdateData: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-user-interests/update',{id:$stateParams.id});
                    }
                }
            })
            .state('searches', {
                abstract: true,
                url: '/searches',
                template: "<ui-view/>"
            })
            .state('searches.index', {
                url: '/index',
                templateUrl: '/app-view/searches-index'
            })
            .state('searches.details', {
                url: '/details/:id',
                controller: 'SearchRequestDetailsCtrl',
                controllerAs: 'searchRequestDetailsCtrl',
                templateUrl: '/app-view/searches-details',
                resolve: {
                    searchRequestDetailsData: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-search-request/details',{id:$stateParams.id});
                    }
                }
            })
            .state('searches.addOffer', {
                url: '/add-offer/:id',
                templateUrl: '/app-view/searches-add-offer',
                controller: 'SearchRequestOfferAddCtrl',
                controllerAs: 'searchRequestOfferAddCtrl',
                resolve: {
                    searchRequestOfferAddData: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-search-request-offer/add',{searchRequestId:$stateParams.id});
                    }
                }
            })
            .state('searches.myList', {
                url: '/my-list',
                templateUrl: '/app-view/searches-my',
                controller: 'SearchRequestMyListCtrl',
                controllerAs: 'searchRequestMyListCtrl',
                resolve: {
                    searchRequestMyListData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-search-request-my-list/index');
                    }
                }
            })
            .state('searches.myOffers', {
                url: '/my-offers',
                templateUrl: '/app-view/searches-my-offers',
                controller: 'SearchRequestMyOffersCtrl',
                controllerAs: 'searchRequestMyOffersCtrl',
                resolve: {
                    searchRequestMyOffersData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-search-request-my-offer/index');
                    }
                }
            })
            .state('searches.draft', {
                url: '/draft',
                templateUrl: '/app-view/searches-draft',
                controller: 'SearchRequestDraftCtrl',
                controllerAs: 'searchRequestDraftCtrl',
                resolve: {
                    searchRequestDraftData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-search-request-draft/index');
                    }
                }
            })
            .state('searches.draft-update', {
                url: '/draft-update/:id/:ids',
                controller: 'SearchRequestAddCtrl',
                controllerAs: 'searchRequestAddCtrl',
                templateUrl: '/app-view/searches-add',
                resolve: {
                    searchRequestAddData: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-search-request-draft/get',{id:$stateParams.id, ids:$stateParams.ids});
                    }
                }
            })
            .state('searches.offerDetails', {
                url: '/offer-details/:id',
                templateUrl: '/app-view/searches-offer-details',
                controller: 'SearchRequestOfferDetailsCtrl',
                controllerAs: 'searchRequestOfferDetailsCtrl',
                resolve: {
                    searchRequestOfferDetailsData: function (jsonDataPromise,$stateParams) {
                        return jsonDataPromise('/api-search-request-offer/details',{id:$stateParams.id});
                    }
                }
            })
            .state('searches.offersList', {
                url: '/searches-offers-list/:id',
                templateUrl: '/app-view/searches-offers-list',
                controller: 'SearchRequestOffersListCtrl',
                controllerAs: 'searchRequestOffersListCtrl',
                resolve: {
                    searchRequestOffersListData: function (jsonDataPromise,$stateParams) {
                        return jsonDataPromise('/api-search-request-offer/offers-list',{id:$stateParams.id});
                    }
                }
            })
            .state('offers', {
                abstract: true,
                url: '/offers',
                template: "<ui-view/>"
            })
            .state('offers.index', {
                url: '/index',
                templateUrl: '/app-view/offers-index'
            })
            .state('offers.pay', {
                url: '/pay/:id',
                controller: 'OfferPayCtrl',
                controllerAs: 'offerPayCtrl',
                templateUrl: '/app-view/offers-pay',
                resolve: {
                    offerPayData: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-offer/pay',{id:$stateParams.id});
                    }
                }
            })
            .state('offers.details', {
                url: '/details/:id?noViewBonus',
                controller: 'OfferDetailsCtrl',
                controllerAs: 'offerDetailsCtrl',
                templateUrl: '/app-view/offers-details',
                resolve: {
                    offerDetailsData: function(jsonDataPromise, $stateParams) {
                        var data = {
                            id: $stateParams.id
                        };
                        if ($stateParams.noViewBonus !== undefined) {
                            data.noViewBonus = 1;
                        }
                        return jsonDataPromise('/api-offer/details',data);
                    }
                }
            })
            .state('offers.bet', {
                url: '/bet/:offer_id/:offer_request_id',
                controller: 'OfferBetCtrl',
                controllerAs: 'offerBetCtrl',
                templateUrl: '/app-view/offers-bet',
                resolve: {
                    betData: function (jsonDataPromise,$stateParams) {
                        return jsonDataPromise('/api-offer-request/bet-get',{
                            offer_id:$stateParams.offer_id,
                            offer_request_id:$stateParams.offer_request_id
                        });
                    }
                }
            })
            .state('offers.myList', {
                url: '/my-list',
                templateUrl: '/app-view/offers-my',
                controller: 'OfferMyListCtrl',
                controllerAs: 'offerMyListCtrl',
                resolve: {
                    offerMyListData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-offer-my-list/index');
                    }
                }
            })
            .state('offers.draft', {
                url: '/draft',
                templateUrl: '/app-view/offers-draft',
                controller: 'OfferDraftCtrl',
                controllerAs: 'offerDraftCtrl',
                resolve: {
                    offerDraftData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-offer-draft/index');
                    }
                }
            })
            .state('offers.draft-update', {
                url: '/draft-update/:id/:ids',
                controller: 'OfferAddCtrl',
                controllerAs: 'offerAddCtrl',
                templateUrl: '/app-view/offers-add',
                resolve: {
                    offerAddData: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-offer-draft/get',{id:$stateParams.id, ids:$stateParams.ids});
                    }
                }
            })
            .state('offers.myRequests', {
                url: '/my-requests',
                templateUrl: '/app-view/offers-my-requests',
                controller: 'OfferMyRequestsCtrl',
                controllerAs: 'offerMyRequestsCtrl',
                resolve: {
                    offerMyRequestsData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-offer-my-request/index');
                    }
                }
            })
            .state('dealsCompleted', {
                url: '/dealsCompleted',
                controller: 'DealsCompletedCtrl',
                controllerAs: 'dealsCompletedCtrl',
                templateUrl: '/app-view/deals-completed',
                resolve: {
                    dealsCompletedData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-deals-completed/index');
                    }
                }
            })
            .state('offers.add', {
                url: '/add/:ids',
                templateUrl: '/app-view/offers-add',
                controller: 'OfferAddCtrl',
                controllerAs: 'offerAddCtrl',
                resolve: {
                    offerAddData: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-offer/add',{ids:$stateParams.ids});
                    }
                }
            })
            .state('offers.juglSearch', {
                url: '/jugl-search',
                templateUrl: '/app-view/offers-jugl-search',
                controller: 'OfferJuglSearchCtrl',
                controllerAs: 'offerJuglSearchCtrl',
                resolve: {
                    offerJuglSearchData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-offer-jugl-search/index');
                    }
                }
            })
            .state('offers.preview', {
                url: '/preview',
                controller: 'OfferPreviewCtrl',
                controllerAs: 'offerPreviewCtrl',
                templateUrl: '/app-view/offers-details'
            })
            .state('offers.search', {
                url: '/search',
                templateUrl: '/app-view/offers-search',
                controller: 'OfferSearchCtrl',
                controllerAs: 'offerSearchCtrl',
                resolve: {
                    offerSearchData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-offer-search/index');
                    }
                }
            })
            .state('offers.advancedSearch', {
                url: '/advanced-search',
                templateUrl: '/app-view/offers-advanced-search',
                controller: 'OfferAdvancedSearch',
                controllerAs: 'offerAdvancedSearch',
                resolve: {
                    offerAdvancedSearchData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-offer-advanced-search/index');
                    }
                }
            })
            .state('offers.advancedSearchResults', {
                url: '/advanced-search-results',
                templateUrl: '/app-view/offers-advanced-search-results',
                controller: 'OfferAdvancedSearchResults',
                controllerAs: 'offerAdvancedSearchResults',
                resolve: {
                    offerAdvancedSearchResultData: function (jsonDataPromise, $localStorage) {
                        return jsonDataPromise('/api-offer-advanced-search/search', {
                            filter: $localStorage['offersAdvancedSearchFilter'],
                            pageNum: 1
                        });
                    }
                }
            })
            .state('offers.searchByUser', {
                url: '/search-by-user/:id',
                templateUrl: '/app-view/offers-search',
                controller: 'OfferSearchCtrl',
                controllerAs: 'offerSearchCtrl',
                resolve: {
                    offerSearchData: function (jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-offer-search/search-by', {user_id: $stateParams.id});
                    }
                }
            })
            .state('offers.addStep1', {
                url: '/addstep1',
                templateUrl: '/app-view/interests-addstep1',
                controller: 'InterestsAddStep1Ctrl',
                controllerAs: 'interestsAddStep1Ctrl',
                resolve: {
                    interestsAddStep1Data: function(jsonDataPromise) {
                        return jsonDataPromise('/api-interests/add-step1',{type:'OFFER'});
                    },
                    type: function() {
                        return 'addOffer';
                    },
                    interestType: function() {
                        return 'OFFER';
                    }
                }
            })
            .state('offers.addStep2', {
                url: '/addstep2/:id',
                templateUrl: '/app-view/interests-addstep2',
                controller: 'InterestsAddStep2Ctrl',
                controllerAs: 'interestsAddStep2Ctrl',
                resolve: {
                    interestsAddStep2Data: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-interests/add-step2',{parent_id:$stateParams.id});
                    },
                    type: function() {
                        return 'addOffer';
                    },
                    interestType: function() {
                        return 'OFFER';
                    }
                }
            })
            .state('offers.addStep3', {
                url: '/addstep3/:id',
                templateUrl: '/app-view/interests-addstep3',
                controller: 'InterestsAddStep3Ctrl',
                controllerAs: 'interestsAddStep3Ctrl',
                resolve: {
                    interestsAddStep3Data: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-interests/searches-add-step3',{parent_id:$stateParams.id});
                    },
                    type: function() {
                        return 'addOffer';
                    },
                    interestType: function() {
                        return 'OFFER';
                    }

                }
            })
            .state('searches.add', {
                url: '/add/:ids',
                templateUrl: '/app-view/searches-add',
                controller: 'SearchRequestAddCtrl',
                controllerAs: 'searchRequestAddCtrl',
                resolve: {
                    searchRequestAddData: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-search-request/add',{ids:$stateParams.ids});
                    }
                }
            })
            .state('searches.search', {
                url: '/search',
                templateUrl: '/app-view/searches-search',
                controller: 'SearchRequestSearchCtrl',
                controllerAs: 'searchRequestSearchCtrl',
                resolve: {
                    searchRequestSearchData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-search-request-search/index');
                    }
                }
            })
            .state('searches.searchByUser', {
                url: '/search-by-user/:id',
                templateUrl: '/app-view/searches-search',
                controller: 'SearchRequestSearchCtrl',
                controllerAs: 'searchRequestSearchCtrl',
                resolve: {
                    searchRequestSearchData: function (jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-search-request-search/search-by', {user_id:$stateParams.id});
                    }
                }
            })
            .state('searches.addStep1', {
                url: '/addstep1',
                templateUrl: '/app-view/interests-addstep1',
                controller: 'InterestsAddStep1Ctrl',
                controllerAs: 'interestsAddStep1Ctrl',
                resolve: {
                    interestsAddStep1Data: function(jsonDataPromise) {
                        return jsonDataPromise('/api-interests/add-step1',{type:'SEARCH_REQUEST'});
                    },
                    type: function() {
                        return 'addSearch';
                    },
                    interestType: function() {
                        return 'SEARCH_REQUEST';
                    }

                }
            })
            .state('searches.addStep2', {
                url: '/addstep2/:id',
                templateUrl: '/app-view/interests-addstep2',
                controller: 'InterestsAddStep2Ctrl',
                controllerAs: 'interestsAddStep2Ctrl',
                resolve: {
                    interestsAddStep2Data: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-interests/add-step2',{parent_id:$stateParams.id});
                    },
                    type: function() {
                        return 'addSearch';
                    },
                    interestType: function() {
                        return 'SEARCH_REQUEST';
                    }
                }
            })
            .state('searches.addStep3', {
                url: '/addstep3/:id',
                templateUrl: '/app-view/interests-addstep3',
                controller: 'InterestsAddStep3Ctrl',
                controllerAs: 'interestsAddStep3Ctrl',
                resolve: {
                    interestsAddStep3Data: function(jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-interests/searches-add-step3',{parent_id:$stateParams.id});
                    },
                    type: function() {
                        return 'addSearch';
                    },
                    interestType: function() {
                        return 'SEARCH_REQUEST';
                    }

                }
            })
            .state('activityList', {
                url: '/activity-list',
                templateUrl: '/app-view/activity-list',
                controller: 'ActivityLogCtrl',
                controllerAs: 'activityLogCtrl',
                resolve: {
                    activityLogData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-event-log/index');
                    }
                }
            })
            .state('earn-money', {
                url: '/earn-money',
                templateUrl: '/app-view/earn-money'
            })
            .state('favorites', {
                url: '/favorites',
                controller: 'FavoritesCtrl',
                controllerAs: 'favoritesCtrl',
                templateUrl: '/app-view/favorites',
                resolve: {
                    favoritesData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-favorites/index');
                    }
                }
            })
            .state('howItWorks', {
                url: '/how-it-works',
                templateUrl: '/app-view/how-it-works'
            })
            .state('wieFunktioniert', {
                url: '/wie-funktioniert',
                templateUrl: '/app-view/wie-funktioniert'
            })
            .state('error404', {
                url: '/error404',
                templateUrl: '/app-view/error404'
            })
            .state('profile-fillup', {
                url: '/profile-fillup',
                templateUrl: '/app-view/profile-fillup',
                controller: 'ProfileFillupCtrl',
                controllerAs: 'profileFillupCtrl',
                resolve: {
                    profileFillupData: function(jsonDataPromise) {
                        return jsonDataPromise('/api-profile/index');
                    }
                }
            })
			.state('new-users', {
                url: '/new-users/:urlState',
                templateUrl: '/app-view/new-users',
                controller: 'NewUsersCtrl',
                controllerAs: 'newUsersCtrl',
                resolve: {
                    newUsersData: function(jsonDataPromise,$state,$stateParams) {
                        if(!$stateParams.realStateChange) return;
                        return jsonDataPromise('/api-user-search/new-users',{urlState:$stateParams.urlState});
                    }
                }
            })
            .state('new-network-members', {
                url: '/new-network-members',
                templateUrl: '/app-view/new-network-members',
                controller: 'NewNetworkMembersCtrl',
                controllerAs: 'newNetworkMembersCtrl',
                resolve: {
                    NewNetworkMembersData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-network-members/new-users');
                    }
                }
            })
            .state('manageNetwork', {
                url: '/manage-network',
                templateUrl: '/app-view/manage-network',
                controller: 'ManageNetworkCtrl',
                controllerAs: 'manageNetworkCtrl',
                resolve: {
                    manageNetworkData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-manage-network/list');
                    }
                }
            })
            .state('selectDestination', {
                url: '/manage-select-destination/:move_id/:id',
                templateUrl: '/app-view/manage-select-destination',
                controller: 'ManageSelectDestinationCtrl',
                controllerAs: 'manageSelectDestinationCtrl',
                resolve: {
                    manageSelectDestinationData: function (jsonDataPromise,$stateParams) {
                        return jsonDataPromise('/api-manage-network/move-destination-list',{
                            move_id:$stateParams.move_id,
                            id: $stateParams.id
                        });
                    }
                }
            })
            .state('forum', {
                url: '/forum',
                templateUrl: '/app-view/trollbox-list',
                controller: 'TrollboxCtrl',
                controllerAs: 'trollboxCtrl',
                resolve: {
                    trollboxCtrlData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-trollbox/index');
                    }
                }
            })
            .state('team-change-user-search', {
                url: '/team-change-user-search/:urlState',
                templateUrl: '/app-view/team-change-user-search',
                controller: 'TeamChangeUserSearchCtrl',
                controllerAs: 'teamChangeUserSearchCtrl',
                resolve: {
                    TeamChangeUserSearchCtrlData: function(jsonDataPromise,$state,$stateParams) {
                        if(!$stateParams.realStateChange) return;
                        return jsonDataPromise('/api-team-change-user-search/index',{urlState:$stateParams.urlState});
                    }
                }
            })
            .state('invite-my-list', {
                url: '/invite-my-list',
                templateUrl: '/app-view/invite-my-list',
                controller: 'InviteMyListCtrl',
                controllerAs: 'inviteMyListCtrl',
                resolve: {
                    InviteMyListCtrlData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-invite-my/index');
                    }
                }
            })
            .state('user-become-member-invitations', {
                url: '/user-become-member-invitations/:id',
                templateUrl: '/app-view/user-become-member-invitations',
                controller: 'UserBecomeMemberInvitationsCtrl',
                controllerAs: 'userBecomeMemberInvitationsCtrl',
                resolve: {
                    UserBecomeMemberInvitationsCtrlData: function (jsonDataPromise, $stateParams) {
                        return jsonDataPromise('/api-user-become-member-invitations/list',{id:$stateParams.id});
                    }
                }
            })
            .state('news', {
                url: '/news',
                templateUrl: '/app-view/news',
                controller: 'NewsCtrl',
                controllerAs: 'newsCtrl',
                resolve: {
                    newsData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-news/list');
                    }
                }
            })
            .state('faqs', {
                url: '/faqs',
                templateUrl: '/app-view/faqs',
                controller: 'FaqsCtrl',
                controllerAs: 'faqsCtrl',
                resolve: {
                    faqsData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-faq/list');
                    }
                }
            })
			/* NVII-MEDIA
			 * START - State Video add - for view
			 * Robert erweitert
			 */
			.state('videos', {
                abstract: true,
                url: '/videos',
                template: "<ui-view/>"
            })
			.state('videos.details', {
                url: '/details/:id',
                controller: 'VideoDetailsCtrl',
                controllerAs: 'videoDetailsCtrl',
                templateUrl: '/app-view/video-details',
                resolve: {
                    videoDetailsData: function(jsonDataPromise, $stateParams) {
                        var data = {
                            id: $stateParams.id
                        };
                        return jsonDataPromise('/api-video/details',data);
                    }
                }
            })
			
			.state('videos.search', {
                url: '/search',
                templateUrl: '/app-view/videos-search',
                controller: 'VideoSearchCtrl',
                controllerAs: 'videoSearchCtrl',
                resolve: {
                    videoSearchData: function (jsonDataPromise) {
                        return jsonDataPromise('/api-video-search/index');
                    }
                }
            })
			.state('howDoesItWork', {
                url: '/wie-funktioniert',
                templateUrl: '/app-view/wie-funktioniert'
            })
			/* NVII-MEDIA
			 * END - State Video add - for view
			 */
        ;

        $urlRouterProvider.otherwise(config.redirectToRegistrationPayment? '/registration-payment':'/dashboard/');
        tmhDynamicLocaleProvider.localeLocationPattern('static/build/angular-locale_{{locale}}.js');

        // configure html5
        //$locationProvider.html5Mode(true);
    })
    .run(function($rootScope,$state,$http,messengerService,modal,adsense,$window,gettextCatalog,jsonDataPromise,tmhDynamicLocale,userStatus,infoPopup,$pixel,userSettings) {

        $rootScope.back=function() {
            $window.history.back();
        };

        $rootScope.status=userStatus.status;
        gettextCatalog.setCurrentLanguage(config.language);
        tmhDynamicLocale.set(config.language);

        $rootScope.$state=$state;

        $rootScope.messenger=messengerService;
        $rootScope.modalService=modal;

        $rootScope.$on('$stateChangeSuccess',function(){
            window.scrollTo(0,0);
        });

        $rootScope.$on('$stateChangeStart',function(event, toState, toParams, fromState, fromParams){
            if (userStatus.status.packet==='' && !userStatus.status.not_force_packet_selection && toState.name!='registrationPayment' && toState.name!='registrationPayment.data') {
                event.preventDefault();
                return;
            }

            if (userStatus.status.status=='LOGINED' && toState.name!='profile-fillup') {
                event.preventDefault();
                return;
            }

            if(fromState.name=='offers.details') {
                jsonDataPromise('/api-offer-view-log/end-view',{id: $rootScope.offerViewLogId}).then(function (data) {
                    console.log(data);
                    $rootScope.offerViewLogId=null;
                },function(){});
            }

            toParams.realStateChange=true;
        });

        $rootScope.showInfoPopup=function(view) {
            infoPopup.show(view);
        };

        $rootScope.isOneShowInfoPopup=function(view) {
            return infoPopup.isOneShow(view);
        };


        if(!navigator.cookieEnabled) {
            modal.alert({message: 'enable cookies'});
        }

        $http.defaults.headers.post['X-CSRF-Token']=$('meta[name=csrf-token]').attr('content');

        $rootScope.showAppDownloadPopup = function() {
            var config = {
                        template:'/app-view/app-download-popup',
                        classes: {'modal-app-download':true},
                        noTransparentBackground: true
            };
            modal.show(config);
        };
		
		
		$rootScope.showStartPopup = function() {
            var config = {
                template:'/app-view/start-popup'
            };
            modal.show(config);
        };

        $rootScope.showFriendsInvitePopup = function() {
            var config = {
                template:'/app-view/friends-invitation-popup'
            };
            modal.show(config);
        };

        $rootScope.updateCountry = function(userId,dataObj) {
            if(userStatus.status.is_moderator && userStatus.status.allow_moderator_country_change) {
                jsonDataPromise('/api-user/get-update-data-country',{id: userId}).then(function (data) {
                    var config={
                        classes: {'modal-country-update':true},
                        template:'/app-view/user-country-update-popup',
                        userData: {
                            user_id: data.user.id,
                            country_id: data.user.country_id
                        },
                        countries: data.countries
                    };
                    $rootScope.updateCountryDataObject=dataObj;
                    modal.show(config);
                },function(){});
            }
        };


        $rootScope.emoticonsList = [
            {
                num: 1,
                codes: [':-D',':D']
            },
            {
                num: 45,
                codes: [':)']
            },
            {
                num: 46,
                codes: [';=)']
            },
            {
                num: 2,
                codes: [';-)',';)']
            },
            {
                num: 3,
                codes: [':-)']
            },
            {
                num: 4,
                codes: ['o)','O)']
            },
            {
                num: 5,
                codes: ['<3','&amp;lt;3']
            },
            {
                num: 6,
                codes: ['3<', '3&amp;lt;']
            },
            {
                num: 7,
                codes: ['o_o','O_O']
            },
            {
                num: 8,
                codes: [':P',':p']
            },
            {
                num: 9,
                codes: [':/',':-/']
            },
            {
                num: 10,
                codes: [':#']
            },
            {
                num: 11,
                codes: [':*',':-*']
            },
            {
                num: 12,
                codes: ['><','&amp;gt;&amp;lt;']
            },
            {
                num: 13,
                codes: ['=D']
            },
            {
                num: 14,
                codes: ['x_x','X_X']
            },
            {
                num: 15,
                codes: [':|',':-|']
            },
            {
                num: 16,
                codes: ['>.<','&amp;gt;.&amp;lt;']
            },
            {
                num: 17,
                codes: ['B)','B-)']
            },
            {
                num: 38,
                codes: ['^_']
            },
            {
                num: 48,
                codes: [';(',';-(',';=(']
            },
            {
                num: 41,
                codes: ['(ok)']
            },
            {
                num: 42,
                codes: ['(y)']
            },
            {
                num: 43,
                codes: ['(n)']
            },
            {
                num: 44,
                codes: ['(highfive)']
            },
            {
                num: 47,
                codes: ['(clap)']
            },
            {
                num: 49,
                codes: ['(jugl)']
            },
            {
                num: 50,
                codes: ['(j)']
            }
        ];
        
        
    });

app.factory('jsonDataPromise',function ($q,$http,modal,$state) {
    return function(url,params) {
        var defer=$q.defer();

        $http.get(url,{params:params})
            .error(function(data, status, headers, config) {
                if(status == '404') {
                    $state.go('error404');
                } else {
                    modal.httpError(data, status, headers, config);
                }
                defer.resolve(false);
            })
            .success(function(data, status, headers, config) {
                defer.resolve(data);
            });

        return defer.promise;
    };
});

app.factory('jsonPostDataPromise',function ($q,$http,modal) {
    return function(url,params) {
        var defer=$q.defer();

        $http.post(url,params)
            .error(function(data, status, headers, config) {
                modal.httpError(data, status, headers, config);
                defer.reject(false);
            })
            .success(function(data, status, headers, config) {
                defer.resolve(data);
            });

        return defer.promise;
    };
});

app.factory('Uploader',function(FileUploader,modal,gettextCatalog) {
    return function(thumbs) {
        if (typeof thumbs === 'undefined') thumbs=[];

        var data={
            thumbs: thumbs
        };

        data[$('meta[name=csrf-param]').attr('content')]=$('meta[name=csrf-token]').attr('content');

        var config={
            url: '/api-file/upload',
            formData: [data],
            autoUpload: true,
            queueLimit: 30
        };

        var uploader=new FileUploader(config);

        uploader.filters.push({
            name: 'imageFilter',
            fn: function(item, options) {
                var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';

                if ('|jpg|png|jpeg|'.indexOf(type.toLowerCase()) === -1) {
                    modal.alert({
                        title: gettextCatalog.getString('Image upload'),
                        message: gettextCatalog.getString('You can upload only files with jpg, jpeg and png extensions.')
                    });
                    return false;
                }

                if (item.size>50*1024*1024) {
                    modal.alert({
                        title: gettextCatalog.getString('Image upload'),
                        message: gettextCatalog.getString('File is too big, max file size is 50MB')
                    });
                    return false;
                }

                return true;

            }
        });

        uploader.filters.push({
            name: 'imageVideoFilter',
            fn: function(item, options) {
                var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';

                if ('|jpg|png|jpeg|mp4|'.indexOf(type.toLowerCase()) === -1) {
                    modal.alert({
                        title: gettextCatalog.getString('Image and video upload'),
                        message: gettextCatalog.getString('You can upload only files with jpg, jpeg, png and mp4 extensions.')
                    });
                    return false;
                }

                if (item.size>50*1024*1024) {
                    modal.alert({
                        title: gettextCatalog.getString('Image and video upload'),
                        message: gettextCatalog.getString('File is too big, max file size is 50MB')
                    });
                    return false;
                }

                return true;
            }
        });

        return uploader;
    };
});

app.factory('ChatUploader',function(FileUploader,modal,gettextCatalog) {
    return function(thumbs) {
        if (typeof thumbs === 'undefined') thumbs=[];

        var data={
            thumbs: thumbs
        };

        data[$('meta[name=csrf-param]').attr('content')]=$('meta[name=csrf-token]').attr('content');

        var config={
            url: '/api-chat-file/upload',
            formData: [data],
            autoUpload: true
        };

        var uploader=new FileUploader(config);

        uploader.filters.push({
            name: 'imageFilter',
            fn: function(item, options) {
                var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';

                if ('|jpg|png|jpeg|mp4|'.indexOf(type.toLowerCase()) === -1) {
                    modal.alert({
                        title: gettextCatalog.getString('Image upload'),
                        message: gettextCatalog.getString('Sie können nur Bilder oder Videos mit folgenden Erweiterungen hochladen: .JPG, .JPEG, .PNG, .MP4')
                    });
                    return false;
                }

                if (item.size>50*1024*1024) {
                    modal.alert({
                        title: gettextCatalog.getString('Image upload'),
                        message: gettextCatalog.getString('File is too big, max file size is 50MB')
                    });
                    return false;
                }

                return true;
            }
        });

        return uploader;
    };
});

// prevent drag & drop on whole page
window.addEventListener("dragover",function(e){
    e = e || event;
    e.preventDefault();
},false);
window.addEventListener("drop",function(e){
    e = e || event;
    e.preventDefault();
},false);
