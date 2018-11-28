<div class="my-offers">
    <div class="container">
        <div ng-click="showInfoPopup('view-offers-my')" ng-class="{'blink':isOneShowInfoPopup('view-offers-my')}" class="info-popup-btn"></div>

        <div class="welcome-text">
            <h2><?=Yii::t('app','Werbung verwalten')?></h2>
        </div>

<!--    <div class="offers-container" scroll-load="offerMyListCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="results.hasMore">-->
        <div class="offers-container">
            <div class="offers-filter-container clearfix">
                <div class="offers-filter-box">
                    <div class="field-box-select" dropdown-toggle select-click>
                        <select ng-model="filter.status" selectpicker>
                            <option value=""><?= Yii::t('app', 'Status Angebote') ?></option>
                            <option value="ACTIVE"><?= Yii::t('app', 'L&auml;uft') ?></option>
                            <option value="EXPIRED"><?= Yii::t('app', 'Abgelaufen') ?></option>
                            <option value="PAUSED"><?= Yii::t('app', 'Gestoppt') ?></option>
                            <option value="REQUEST_NEW"><?= Yii::t('app', 'Neue Kaufinteresse') ?></option>
                            <option value="REQUEST_INVITED"><?= Yii::t('app', 'Gekauft') ?></option>
                            <option value="REQUEST_PAYED"><?= Yii::t('app', 'Bezahlt') ?></option>
                            <option value="REQUEST_PAYING_POD"><?= Yii::t('app', 'Barzahlung bei Abholung') ?></option>
                            <option value="REQUEST_CONFIRMED"><?= Yii::t('app', 'Geldeingang bestätigt') ?></option>
                            <option value="TYPE_AUCTION"><?= Yii::t('app', 'Bieterverfahren') ?></option>
                            <option value="TYPE_AUTOSELL"><?= Yii::t('app', 'Sofortkauf') ?></option>
                            <option value="TYPE_AD"><?= Yii::t('app', 'Keine Kaufmöglichkeit') ?></option>
                            <option value="SCHEDULED"><?= Yii::t('app', 'Zeitvesetzt') ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div ng-if="results.items.length == 0" class="result-empty-text">
                <?= Yii::t('app', 'Leider hast du noch keine Werbung bzw. Produktangebote eingestellt oder keine passenden Produkte für diesen Filter. Um neue Werbung zu erstellen, gehe auf „Verkaufen / Werbung schalten".') ?>
            </div>

            <div class="offer-box" ng-repeat="item in results.items" id="offer-{{item.id}}" ng-class="{'status-scheduled': item.status=='SCHEDULED'}">

                <div ng-if="item.status=='SCHEDULED'" class="status-scheduled-label-text"><?= Yii::t('app','Wartet auf Veröffentlichung') ?></div>

                <div class="offer clearfix">
                    <div class="offer-picture-box">
                        <a ui-sref="offers.details({id:item.id})">
                            <div class="offer-picture">
                                <img ng-src="{{::item.image}}" alt=""/>
                            </div>
                        </a>
                    </div>

                    <div class="offer-info-box">

                        <div class="found-offer-type" ng-click="showInfoPopup('info-offer-type')">
                            <span ng-if="item.type == 'AUCTION'"><?= Yii::t('app', 'Bieterverfahren') ?></span>
                            <span ng-if="item.type == 'AD'"><?= Yii::t('app', 'Keine Kaufmöglichkeit') ?></span>
                            <span ng-if="item.type == 'AUTOSELL'"><?= Yii::t('app', 'Sofortkauf') ?></span>
                        </div>

                        <div class="offer-info-title"><h2><a ui-sref="offers.details({id:item.id})">{{::item.title}}</a></h2></div>
                        <ul class="offer-info-category">
                            <li>{{::item.level1Interest}}</li>
                            <li ng-if="item.level2Interest">{{::item.level2Interest}}</li>
                            <li ng-if="item.level3Interests">{{::item.level3Interests}}</li>
                        </ul>

                        <div once-if="item.status=='ACTIVE' && (!item.pay_status || item.pay_status=='INVITED')" class="offer-status offer-status-active"><?= Yii::t('app', 'L&auml;uft') ?></div>
                        <div once-if="item.status=='CLOSED'" class="offer-status offer-status-closed"><?= Yii::t('app', 'Abgeschlossen') ?></div>
                        <div once-if="item.status=='EXPIRED'" class="offer-status offer-status-expired"><?= Yii::t('app', 'Abgelaufen') ?></div>
                        <div once-if="item.status=='PAUSED'" class="offer-status offer-status-expired"><?= Yii::t('app', 'Gestoppt') ?></div>
                        <div once-if="item.status=='AWAITING_VALIDATION'" class="offer-status offer-status-expired"><?= Yii::t('app', 'Wird geprüft') ?></div>
                        <div once-if="item.status=='REJECTED'" class="offer-status offer-status-expired"><?= Yii::t('app', 'Abgelehnt') ?></div>

                        <div>
                            <a once-if="item.status=='ACTIVE' && (!item.pay_status || item.pay_status=='INVITED')" href="" ng-click="offerMyListCtrl.pause(item.id)"><?=Yii::t('app','Stoppen')?></a>
                            <a once-if="item.status=='PAUSED' && (!item.pay_status || item.pay_status=='INVITED')" href="" ng-click="offerMyListCtrl.pause(item.id)"><?=Yii::t('app','Starten')?></a>
                        </div>
                    </div>

                    <div class="offer-info-others-box">
                        <div class="offer-info-status-box clearfix">
                            <div class="offer-date">{{::item.create_dt|date:"dd.MM.yyyy"}}</div>
                            <div class="offer-interested">
                                <span>{{::item.bettersCount}}</span><?= Yii::t('app', 'Interessenten'); ?>
                            </div>
                        </div>

                        <div ng-if="item.type=='AUTOSELL'" class="offer-price"><b><?= Yii::t('app', 'Preis') ?>: </b><span>{{::item.price|priceFormat}} &euro;</span></div>
                        <div ng-if="item.type=='AUCTION'" class="offer-price"><b><?= Yii::t('app', 'Preisvorstellung') ?>: </b><span>{{::item.price|priceFormat}} &euro;</span></div>

                        <div ng-if="item.view_bonus" class="offer-bonus promotion-bonus">
                            <?= Yii::t('app', 'Werbebonus') ?>:
                            <span class="bonus-value">{{::item.view_bonus|priceFormat}} <jugl-currency></jugl-currency></span>
                        </div>
                        <div ng-if="item.buy_bonus" class="offer-bonus buy-bonus">
                            <?= Yii::t('app', 'Kaufbonus') ?>:
                            <span class="bonus-value">{{::item.buy_bonus|priceFormat}} <jugl-currency></jugl-currency></span>
                        </div>

                        <div ng-if="item.view_bonus_total" class="offer-bonus">
                            <?= Yii::t('app', 'Budget') ?>:
                            <span class="bonus-value">{{::item.view_bonus_total|priceFormat}} <jugl-currency></jugl-currency></span>
                        </div>

                        <div ng-if="item.view_bonus_used" class="offer-bonus">
                            <?= Yii::t('app', 'Budget verbraucht') ?>:
                            <span class="bonus-value">{{::item.view_bonus_used|priceFormat}} <jugl-currency></jugl-currency></span>
                        </div>

                        <div ng-if="item.show_amount == 1" class="offer-bonus">
                            <?= Yii::t('app', 'St&uuml;ckzahl') ?>:
                            <b>{{::item.amount}}</b>
                        </div>
                        <div class="offer-bonus">
                            <?= Yii::t('app', 'Aktiv bis') ?>:
                            <b>{{::item.active_till|date:"dd.MM.yyyy"}}</b>
                        </div>
                        <div ng-if="item.delivery_cost>0" class="offer-bonus">
                            <?= Yii::t('app', 'Versandkosten') ?>:
                            <b>{{::item.delivery_cost|priceFormat}} &euro;</b>
                        </div>

                        <div ng-if="item.bestBet>0" class="offer-bonus">
                            <b><?= Yii::t('app', 'Aktuelles Höchstgebot') ?>: </b>
                            <span class="bonus-value">{{::item.bestBet|priceFormat}} &euro;</span>
                        </div>

                        <div ng-if="item.type == 'AUCTION' && item.offerRequests.length > 0" ng-click="item.show_auction_list=!item.show_auction_list" class="show-auction-list-btn">
                            {{::item.offerRequests.length}}<br/>
                            <span><?= Yii::t('app', 'Gebote') ?></span>
                        </div>

                        <div class="bonus-total-update-box">
                            <span ng-if="item.canUpdateViewBonusTotal" ng-click="offerMyListCtrl.update(item)" class="bonus-total-update"><?= Yii::t('app', 'bearbeiten') ?></span>
                        </div>


                    </div>
                    <div class="btn-del-offer">
                        <button ng-click="offerMyListCtrl.delete(item.id)"></button>
                    </div>
                </div>

                <div ng-if="item.type == 'AUCTION' && item.offerRequests.length > 0 && item.show_auction_list" class="bet-filter-box clearfix">
                    <div class="sort-content clearfix">
                        <div class="sort-box">
                            <label><?=Yii::t('app', 'Filtern nach');?>:</label>
                            <div class="field-box-select" dropdown-toggle select-click>
                                <select ng-model="item.bet.filter" selectpicker>
                                    <option value=""><?= Yii::t('app', 'Alles zeigen'); ?></option>
                                    <option value="ACTIVE"><?= Yii::t('app', 'Nur aktive') ?></option>
                                    <option value="EXPIRED"><?= Yii::t('app', 'Nur abgelaufene') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="sort-box">
                            <label><?=Yii::t('app', 'Sortieren nach');?>:</label>
                            <div class="field-box-select" dropdown-toggle select-click>
                                <select ng-model="item.bet.sort" selectpicker>
                                    <option value=""><?= Yii::t('app', 'Alles zeigen'); ?></option>
                                    <option value="-bet_price"><?= Yii::t('app', 'Gebotshöhe') ?></option>
                                    <option value="-bet_active_till"><?= Yii::t('app', 'Gültigkeit') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="offer-users-list">
                    <li ng-repeat="request in item.offerRequests" ng-if="item.type != 'AUCTION'" id="offerRequest-{{request.id}}">
                        <div class="offer-users-list-box clearfix">
                            <div class="offer-request-left">
                                <div class="offer-request-user-box clearfix">
                                    <div class="offer-user-box clearfix">
                                        <a ui-sref="userProfile({id: request.user.id})">
                                            <div class="offer-user-avatar"><img ng-src="{{::request.user.avatarSmall}}" alt=""/></div>
                                        </a>
                                        <div class="offer-user-name">{{::request.user|userName}}</div>
                                        <div class="offer-user-rating">
                                            <div class="star-rating">
                                                <span once-style="{width:(+request.user.rating)+'%'}"></span>
                                            </div>
                                            <div class="user-feedback-count">({{::request.user.feedback_count}})</div>
                                            <div ng-if="request.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                            <div ng-if="request.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                        </div>
                                    </div>
                                </div>

                                <div once-if="!request.pay_status" class="offer-pay-status"><?= Yii::t('app', 'Neues Kaufinteresse') ?></div>
                                <div once-if="request.pay_status=='INVITED'" class="offer-pay-status"><?= Yii::t('app', 'Gekauft') ?></div>
                                <div once-if="request.pay_status=='PAYED' && request.pay_method!='POD'" class="offer-pay-status"><?= Yii::t('app', 'Als bezahlt markiert') ?></div>
                                <div once-if="request.pay_status=='PAYED' && request.pay_method=='POD'" class="offer-pay-status"><?= Yii::t('app', 'Barzahlung bei Abholung') ?></div>
                                <div once-if="request.pay_status=='CONFIRMED'" class="offer-pay-status"><?= Yii::t('app', 'Geldeingang bestätigt') ?></div>

                            </div>

                            <div class="offer-request-right">
                                <div class="offer-request-comments-box offer-comments">
                                    <p once-if="request.description" ng-bind-html="request.description|linky"></p>
                                    <p once-if="!request.description" class="no-comments"><?= Yii::t('app', 'Kein Kommentar') ?></p>
                                </div>

                                <div class="offer-request-link-box" ng-if="item.status=='ACTIVE' && !request.pay_status">
                                    <a class="offer-link" href="" ng-click="offerMyListCtrl.accept(request,item);"><?= Yii::t('app', 'Akzeptieren') ?></a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li ng-repeat="request in item.offerRequests | filter: offerRequestFilters[item.bet.filter] | orderBy:item.bet.sort " ng-if="item.type == 'AUCTION' && item.show_auction_list" id="offerRequest-{{request.id}}">
                        <div class="offer-users-list-box">
                            <div class="offer-item-auction-box">
                                <div class="offer-item-auction-user-box clearfix">
                                    <div class="offer-user-box clearfix">
                                        <a ui-sref="userProfile({id: request.user.id})">
                                            <div class="offer-user-avatar"><img ng-src="{{::request.user.avatarSmall}}" alt=""/></div>
                                        </a>
                                        <div class="offer-user-name">{{::request.user|userName}}</div>
                                        <div class="offer-user-rating">
                                            <div class="star-rating">
                                                <span once-style="{width:(+request.user.rating)+'%'}"></span>
                                            </div>
                                            <div class="user-feedback-count">({{::request.user.feedback_count}})</div>
                                            <div ng-if="request.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                            <div ng-if="request.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                        </div>
                                    </div>
                                    <div class="current-bid"><?= Yii::t('app', 'Aktuelles Gebot') ?>: <span>{{::request.bet_price|priceFormat}} &euro;</span></div>
                                </div>

                                <div class="offer-item-auction-description-box offer-auction-comments">
                                    <p once-if="request.description" ng-bind-html="request.description|linky"></p>
                                    <p once-if="!request.description"><?= Yii::t('app', 'Kein Kommentar') ?></p>
                                </div>

                                <div class="offer-item-auction-info-box">
                                    <div class="accept-bid" ng-if="!request.isExpired && !request.pay_status" ng-click="offerMyListCtrl.accept(request,item);"><?= Yii::t('app', 'Gebot annehmen') ?></div>
                                    <div class="accept-bid" ng-if="request.isExpired && !request.pay_status" ng-click="offerMyListCtrl.openChat(request, item);"><?= Yii::t('app', 'Nachricht senden') ?></div>

                                    <div ng-if="request.status!='ACCEPTED'" class="auction-valid-time">
                                        <?= Yii::t('app', 'Gültig') ?>: <span server-countdown="request.bet_active_till"></span>
                                    </div>
                                    <div ng-if="request.status=='ACCEPTED'" class="auction-valid-time">
                                        <?= Yii::t('app', 'Gestoppt') ?>: <span server-countdown="request.bet_active_till" server-countdown-closed-dt="request.closed_dt"></span>
                                    </div>

                                    <div class="auction-end-time"><?= Yii::t('app', 'Ende') ?>: <b>{{::request.bet_active_till|date:"dd.MM.yyyy HH:mm"}}</b></div>
                                    <div class="auction-status-accepted" ng-if="request.status=='ACCEPTED'">
                                        <?= Yii::t('app', 'Gebot angenommen') ?>
                                        <div class="request-closed_dt">{{request.closed_dt|date:"dd.MM.yyyy HH:mm"}}</div>
                                    </div>
                                    <div ng-if="request.modificationsCount>0" class="bids-changed" ng-click="request.show_bids_changed=!request.show_bids_changed"><?= Yii::t('app', 'Gebot geändert') ?>: <b>{{::request.modificationsCount}} <?= Yii::t('app', 'Mal') ?></b></div>

                                    <ul ng-if="request.show_bids_changed" class="bids-changed-list">
                                        <li ng-repeat="itemModification in request.modifications">
                                            {{::itemModification.dt|date:"dd.MM.yyyy HH:mm"}} <span>{{::itemModification.price|priceFormat}} &euro;</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>


                <div class="open-offer-view-users">
                    <a href="" ng-click="offerMyListCtrl.offerViewUsers(item.id)"><?= Yii::t('app', 'Werbung angeschaut') ?></a>
                </div>

                <div ng-if="item.offer_view_users_loader" class="loader-box">
                    <div class="spinner"></div>
                </div>

                <div ng-if="item.offer_view_users_load" class="offer-view-users">
                    <div ng-if="item.offer_view_users.count_users>0">
                        <div class="offer-view-users-count"><?= Yii::t('app', 'Anzahl der User') ?>: <b>{{::item.offer_view_users.count_users}}</b></div>
                        <div class="offer-view-users-box">
                            <div class="carousel-list-container carousel-list">
                                <ul class="offer-view-users-list carousel-list-box">
                                    <li ng-repeat="offerViewUser in item.offer_view_users.users" class="carousel-list-item">
                                        <div ng-click="offerMyListCtrl.offerHistoryView(offerViewUser.id, item.id)" class="offer-view-user-avatar">
                                            <img ng-src="{{::offerViewUser.avatarSmall}}" alt=""/>
                                        </div>
                                    </li>
                                </ul>
                                <div class="carousel-nav-prev"></div>
                                <div class="carousel-nav-next"></div>
                            </div>
                        </div>
                    </div>
                    <div ng-if="item.offer_view_users.count_users===0" class="offer-view-users-empty">
                        <?= Yii::t('app', 'Die Werbung wurde noch von keinem Benutzer angeschaut') ?>
                    </div>
                </div>

            </div>
            <div class="bottom-corner"></div>
        </div>
    </div>
</div>