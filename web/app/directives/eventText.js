app.directive('eventText', function($compile,gettextCatalog, userStatus) {

    return {
        restrict: 'A',
        link: function(scope, element, $attrs) {
            var text=scope.$eval($attrs.eventText);

            //text=text.replace(/\[([a-zA-Z]+(:\d+)+|\/[a-zA-Z]+)\]/g,'');

            text=text.replace(/\[([a-zA-Z]+(:[-0-9a-zA-Z]+)*|\/[a-zA-Z]+)\]/g,function(value) {
                if (value.match(/\[\/[a-zA-Z]+\]/)) {
                    return '</a>';
                }

                var parts=value.match(/\[([a-zA-Z]+)(:[-0-9a-zA-Z]+)?(:[-0-9a-zA-Z]+)?(:[-0-9a-zA-Z]+)?\]/);

                if (parts) {
                    switch (parts[1]) {
                        case 'groupChat':
                            return '<a href="" ng-click="activityLogCtrl.enterGroupChat('+parts[2].replace(':','')+')">';
                        case 'upgradePacket':
                            return '<a ng-if="status.packet==\'VIP\'" ui-sref="packetUpgrade" class="btn btn-submit">'+gettextCatalog.getString('Jetzt PremiumPlus Midglied werden')+'</a>' +
                                   '<a ng-if="status.packet==\'STANDART\'" ui-sref="packetUpgrade" class="btn btn-submit">'+gettextCatalog.getString('Jetzt Premium/PremiumPlus Midglied werden')+'</a>';
                        case 'spamReportDeactivate':
                            return '<a href="" class="btn btn-submit" ng-click="activityLogCtrl.spamReportDeactivate(event.id,'+parts[2].replace(':','')+')">'+gettextCatalog.getString('Spammeldung zurücknehmen')+'</a>';
                        case 'toggleBlockParentTeamRequests':
                            return '<a href="" class="btn btn-submit" ng-click="activityLogCtrl.toggleBlockParentTeamRequests()"><span ng-if="!status.block_parent_team_requests">'+gettextCatalog.getString('Teamanfragen stoppen')+'</span><span ng-if="status.block_parent_team_requests">'+gettextCatalog.getString('Teamanfragen erlauben')+'</span></a>';
                        case 'teamChangeUserSearch':
                            return '<a ui-sref="team-change-user-search">';
                        case 'vipProlongation':
                            return '<a ng-if="status.vipProlongActive" href="" class="btn btn-submit" ng-click="activityLogCtrl.vip()">'+gettextCatalog.getString('Mitgliedschaft jetzt verlängern')+'</a>';
                        case 'vipUpgrade':
                            return '<a ng-if="status.packet!=\'VIP\'" href="" class="btn btn-submit" ng-click="activityLogCtrl.vip()">'+gettextCatalog.getString('Erneut erwerben')+'</a>';
                        case 'userTeamRequestAccept':
                            return '<a href="" class="btn btn-submit" ng-click="activityLogCtrl.userTeamRequestAccept('+parts[2].replace(':','')+')">'+gettextCatalog.getString('Annehmen')+'</a>';
                        case 'userTeamRequestDecline':
                            return '<a href="" class="btn btn-submit" ng-click="activityLogCtrl.userTeamRequestDecline('+parts[2].replace(':','')+')">'+gettextCatalog.getString('Ablehnen')+'</a>';
                        case 'offer':
                            return '<a ui-sref="offers.details({id:'+parts[2].replace(':','')+'})">';
                        case 'teamleaderFeedback':
                            return '<a href="" class="btn btn-submit" ng-click="activityLogCtrl.teamleaderFeedback()">'+gettextCatalog.getString('Bewerten')+'</a>';
                        case 'searchRequestOfferFeedback':
                            return '<a href="" class="btn btn-submit" ng-click="activityLogCtrl.searchRequestOfferFeedback('+parts[2].replace(':','')+')">'+gettextCatalog.getString('Bewerten')+'</a>';
                        case 'searchRequestOfferCounterFeedback':
                            return '<a href="" class="btn btn-submit" ng-click="activityLogCtrl.searchRequestOfferCounterFeedback('+parts[2].replace(':','')+')">'+gettextCatalog.getString('Bewerten')+'</a>';
                        case 'offerRequestFeedback':
                            return '<a href="" class="btn btn-submit" ng-click="activityLogCtrl.offerRequestFeedback('+parts[2].replace(':','')+')">'+gettextCatalog.getString('Bewerten')+'</a>';
                        case 'offerRequestCounterFeedback':
                            return '<a href="" class="btn btn-submit" ng-click="activityLogCtrl.offerRequestCounterFeedback('+parts[2].replace(':','')+')">'+gettextCatalog.getString('Bewerten')+'</a>';
                        case 'teamFeedbacks':
                            return '<a href="" class="btn btn-submit" ng-click="activityLogCtrl.teamleaderFeedbacks()">'+gettextCatalog.getString('Bewerten')+'</a>';
                        case 'userProfile':
                            return '<a ui-sref="userProfile({id:'+parts[2].replace(':','')+'})">';
                        case 'searchRequestOffer':
                            return '<a ui-sref="searches.offerDetails({id:'+parts[3].replace(':','')+'})">';
                        case 'searchRequest':
                            return '<a ui-sref="searches.details({id:'+parts[2].replace(':','')+'})">';
                        case 'offerPay':
                            return '<a class="btn btn-submit" ui-sref="offers.pay({id:'+parts[3].replace(':','')+'})">'+gettextCatalog.getString('Jetzt Bezahlen')+'</a>';
                        case 'offerPayConfirm':
                            return '<a class="btn btn-submit" href="" ng-click="activityLogCtrl.offerRequestPayConfirm('+parts[3].replace(':','')+')">'+gettextCatalog.getString('Geldeingang bestätigen')+'</a>';
                        case 'offerPayNotifyBuyer':
                            return '<a class="btn btn-submit" href="" ng-click="activityLogCtrl.offerRequestPayNotifyBuyer('+parts[3].replace(':','')+')">'+gettextCatalog.getString('Abmahnen')+'</a>';
                        case 'myOfferRequest':
                            return '<a href="" ng-click="activityLogCtrl.goMyOfferRequest({id:'+parts[2].replace(':','')+'})">';
                        case 'myOffer':
                            return '<a href="" ng-click="activityLogCtrl.goMyOffer({id:'+parts[2].replace(':','')+'})">';
                        case 'br':
                            return '<br/>';
                        case 'nobrStart':
                            return '<nobr>';
                        case 'nobrEnd':
                            return '</nobr>';
                        case 'jugl':
                            return '<jugl-currency-light></jugl-currency-light>';
						case 'video':
                            return '<a ui-sref="videos.details({id:'+parts[2].replace(':','')+'})">';
                        case 'info':
                            return '<a href="" ng-click="activityLogCtrl.goInfoView('+"'"+parts[2].replace(":","")+"'"+')">';
                        case 'networkMoveAccept':
                            return '<a class="btn btn-submit" ng-click="activityLogCtrl.networkAcceptMoving('+parts[2].replace(':','')+','+parts[3].replace(':','')+','+parts[4].replace(':','')+')">'+gettextCatalog.getString('Ja')+'</a>';
                        case 'networkMoveReject':
                            return '<a class="btn btn-submit" ng-click="activityLogCtrl.networkRejectMoving('+parts[2].replace(':','')+','+parts[3].replace(':','')+','+parts[4].replace(':','')+')">'+gettextCatalog.getString('Nein')+'</a>';
                        case 'stickParentAccept':
                            return '<a class="btn btn-submit" ng-click="activityLogCtrl.stickParentAccept()">'+gettextCatalog.getString('Zustimmen')+'</a>';
                        case 'stickParentReject':
                            return '<a class="btn btn-submit" ng-click="activityLogCtrl.stickParentReject()">'+gettextCatalog.getString('Ablehnen')+'</a>';
                    }
                }
                return '';

            });

            text=text.replace(/(<a[^>]*class="btn)/,'<div class="clearfix" style="margin-bottom:15px;"></div>$1');

            var el = $compile('<div>'+text+'</div>')( scope );

            // stupid way of emptying the element
            element.html("");

            // add the template content
            element.append( el );
        }
    };
});