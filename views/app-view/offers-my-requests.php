<div class="offers-my-requests">
    <div class="container">

        <div ng-click="showInfoPopup('view-offers-my-requests')" ng-class="{'blink':isOneShowInfoPopup('view-offers-my-requests')}" class="info-popup-btn"></div>

        <div class="welcome-text">
            <h2><?=Yii::t('app','Ich habe geboten / gekauft')?></h2>
        </div>

        <div class="searches-content clearfix">

            <div class="offers-container" scroll-load="offerMyRequestsCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="results.hasMore">

                    <div class="offers-filter-container clearfix">
                        <div class="offers-filter-box">
                            <div class="field-box-select" dropdown-toggle select-click>
                                <select ng-model="filter.status" selectpicker>
                                    <option value=""><?= Yii::t('app', 'Alles zeigen'); ?></option>
                                    <option value="ACTIVE"><?= Yii::t('app', 'Gebot ist aktiv'); ?></option>
                                    <option value="EXPIRED"><?= Yii::t('app', 'Gebot abgelaufen'); ?></option>
                                    <option value="ACCEPTED"><?= Yii::t('app', 'Gebot hat gewonnen'); ?></option>
                                    <option value="OFFER_EXPIRED"><?= Yii::t('app', 'Bieterverfahren abgelaufen'); ?></option>
                                    <option value="OFFER_BUY"><?= Yii::t('app', 'Gekauft'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div ng-if="results.items.length == 0" class="result-empty-text">
                        <?= Yii::t('app', 'Du hast noch keine Gebote abgegeben oder etwas gekauft.') ?>
                    </div>

                    <div ng-repeat="item in results.items" class="bet-item-wrap">

                        <div ng-if="item.betCanBeChanged" class="status-bet-active bet-item-box">
                            <div class="found-image-box">
                                <a ui-sref="offers.details({id:item.offer.id})">
                                    <div class="found-image">
                                        <img ng-src="{{::item.offer.image}}" alt=""/>
                                    </div>
                                </a>
                            </div>
                            <div class="found-bet-offer-box">
                                <div class="bet-times-box">
                                    <div class="auction-valid-time"><?= Yii::t('app', 'Gültig') ?>: <span server-countdown="item.bet_active_till"></span></div>
                                    <div class="auction-end-time"><?= Yii::t('app', 'Ende') ?>: <b>{{::item.bet_active_till|date:"dd.MM.yyyy HH:mm"}}</b></div>
                                    <div class="cobetters-count">{{::item.offer.cobettersCount}}<br/><?= Yii::t('app', 'Mitbieter') ?> </div>
                                </div>

                                <div class="found-offer-my-bids-title-text"><?= Yii::t('app', 'Du hast geboten auf') ?>:</div>

                                <div class="found-title">
                                    <h2><a ui-sref="offers.details({id:item.offer.id})">{{::item.offer.title}}</a></h2>
                                </div>
                                <ul class="found-category">
                                    <li>{{::item.offer.level1Interest}}</li>
                                    <li ng-if="item.offer.level2Interest">{{::item.offer.level2Interest}}</li>
                                    <li ng-if="item.offer.level3Interests">{{::item.offer.level3Interests}}</li>
                                </ul>
                                <div class="found-bet-params-box clearfix">
                                    <div class="found-bet-params-left">
                                        <div class="found-offer-param"><?= Yii::t('app', 'Aktuelles Höchstgebot') ?>: <span class="found-offer-value">{{::item.offer.bestBet|priceFormat}} &euro;</span></div>
                                        <div class="found-offer-param your-bid"><?= Yii::t('app', 'Dein Gebot') ?>:
                                            <span class="found-offer-value">{{::item.bet_price|priceFormat}} &euro;</span>
                                            <a class="bet-update" ng-if="item.betCanBeChanged" ui-sref="offers.bet({offer_request_id:item.id})"></a>
                                        </div>
                                        <div class="found-bet-status active"><?= Yii::t('app', 'L&auml;uft') ?></div>
                                    </div>
                                    <div class="found-bet-params-right">
                                        <div ng-if="item.offer.view_bonus" class="found-offer-param promotion-bonus"><?= Yii::t('app', 'Werbebonus') ?>: <span class="found-offer-value">{{::item.offer.view_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                        <div ng-if="item.offer.buy_bonus" class="found-offer-param buy-bonus"><?= Yii::t('app', 'Kaufbonus') ?>: <span class="found-offer-value">{{::item.offer.buy_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                        <div class="found-date"><?= Yii::t('app', 'Läuft bis') ?>: {{::item.offer.active_till|date:"dd.MM.yyyy"}}</div>
                                    </div>
                                </div>
                                <div class="bet-offer-user-box">
                                    <div class="from-user"><?= Yii::t('app', 'von') ?>:</div>
                                    <div class="found-user-box clearfix">
                                        <a ui-sref="userProfile({id: item.offer.user.id})">
                                            <div class="found-user-avatar"><img ng-src="{{::item.offer.user.avatarSmall}}" alt=""/></div>
                                        </a>
                                        <div class="found-user-name">{{::item.offer.user|userName}}</div>
                                        <div class="offer-user-rating">
                                            <div class="star-rating">
                                                <span once-style="{width:(+item.offer.user.rating)+'%'}"></span>
                                            </div>
                                            <div class="user-feedback-count">({{::item.offer.user.feedback_count}})</div>
                                            <div ng-if="item.offer.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                            <div ng-if="item.offer.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div ng-if="item.status=='ACCEPTED' && item.offer.type=='AUCTION'" class="head-bet-status head-bet-status-accepted"><?= Yii::t('app', 'Herzlichen Glückwunsch, der Verkäufer hat Dein Gebot angenommen!') ?> <span><?= Yii::t('app', 'Du bist der Käufer!') ?></span></div>
                        <div ng-if="item.status=='ACCEPTED' && item.offer.type=='AUCTION'" class="status-bet-accepted bet-item-box">

                            <div class="found-image-box">
                                <a ui-sref="offers.details({id:item.offer.id})">
                                    <div class="found-image">
                                        <img ng-src="{{::item.offer.image}}" alt=""/>
                                    </div>
                                </a>
                            </div>

                            <div class="found-bet-offer-box">

                                <div class="found-offer-my-bids-title-text"><?= Yii::t('app', 'Du hast geboten auf') ?>:</div>
                                <div class="found-title">
                                    <h2><a ui-sref="offers.details({id:item.offer.id})">{{::item.offer.title}}</a></h2>
                                </div>
                                <ul class="found-category">
                                    <li>{{::item.offer.level1Interest}}</li>
                                    <li ng-if="item.offer.level2Interest">{{::item.offer.level2Interest}}</li>
                                    <li ng-if="item.offer.level3Interests">{{::item.offer.level3Interests}}</li>
                                </ul>

                                <div class="found-bet-params-box clearfix">
                                    <div class="found-bet-params-left">
                                        <div class="found-offer-param your-bid"><?= Yii::t('app', 'Dein Gebot') ?>: <span class="found-offer-value">{{::item.bet_price|priceFormat}} &euro;</span></div>
                                        <div class="found-offer-param"><?= Yii::t('app', 'Verkauft für') ?>: <span class="found-offer-value">{{::item.bet_price|priceFormat}} &euro;</span></div>
                                    </div>
                                    <div class="found-bet-params-right">
                                        <div ng-if="item.offer.view_bonus" class="found-offer-param promotion-bonus"><?= Yii::t('app', 'Werbebonus') ?>: <span class="found-offer-value">{{::item.offer.view_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                        <div ng-if="item.offer.buy_bonus" class="found-offer-param buy-bonus"><?= Yii::t('app', 'Kaufbonus') ?>: <span class="found-offer-value">{{::item.offer.buy_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                        <div class="found-date"><?= Yii::t('app', 'Läuft bis') ?>: {{::item.offer.active_till|date:"dd.MM.yyyy"}}</div>
                                    </div>
                                </div>

                                <div class="found-bet-params-box two clearfix">
                                    <div class="found-bet-params-left">
                                        <div class="bet-offer-user-box">
                                            <div class="from-user"><?= Yii::t('app', 'von') ?>:</div>
                                            <div class="found-user-box clearfix">
                                                <a ui-sref="userProfile({id: item.offer.user.id})">
                                                    <div class="found-user-avatar"><img ng-src="{{::item.offer.user.avatarSmall}}" alt=""/></div>
                                                </a>
                                                <div class="found-user-name">{{::item.offer.user|userName}}</div>
                                                <div class="offer-user-rating">
                                                    <div class="star-rating">
                                                        <span once-style="{width:(+item.offer.user.rating)+'%'}"></span>
                                                    </div>
                                                    <div class="user-feedback-count">({{::item.offer.user.feedback_count}})</div>
                                                    <div ng-if="item.offer.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                                    <div ng-if="item.offer.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div ng-if="item.pay_status=='INVITED'" class="found-bet-params-right">
                                        <a ui-sref="offers.pay({id:item.id})"><div class="offer-again-btn"><?= Yii::t('app', 'Jetzt bezahlen') ?></div></a>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div ng-if="item.status!='ACCEPTED' && item.offer.status!='ACTIVE' && item.offer.status!='PAUSED'" class="head-bet-status head-bet-status-expired"><?= Yii::t('app', 'Leider hat der Verkäufer Dein Gebot nicht angenommen') ?></div>
                        <div ng-if="item.status!='ACCEPTED' && item.offer.status!='ACTIVE' && item.offer.status!='PAUSED'" class="status-bet-expired bet-item-box">
                            <div class="found-image-box">
                                <a ui-sref="offers.details({id:item.offer.id})">
                                    <div class="found-image">
                                        <img ng-src="{{::item.offer.image}}" alt=""/>
                                    </div>
                                </a>
                            </div>

                            <div class="found-bet-offer-box">
                                <div class="found-offer-my-bids-title-text"><?= Yii::t('app', 'Du hast geboten auf') ?>:</div>
                                <div class="found-title">
                                    <h2><a ui-sref="offers.details({id:item.offer.id})">{{::item.offer.title}}</a></h2>
                                </div>
                                <ul class="found-category">
                                    <li>{{::item.offer.level1Interest}}</li>
                                    <li ng-if="item.offer.level2Interest">{{::item.offer.level2Interest}}</li>
                                    <li ng-if="item.offer.level3Interests">{{::item.offer.level3Interests}}</li>
                                </ul>

                                <div class="found-bet-params-box clearfix">
                                    <div class="found-bet-params-left">
                                        <div class="found-offer-param your-bid"><?= Yii::t('app', 'Dein Gebot') ?>: <span class="found-offer-value">{{::item.bet_price|priceFormat}} &euro;</span></div>
                                        <div class="found-offer-param"><?= Yii::t('app', 'Verkauft für') ?>: <span class="found-offer-value">{{::item.offer.bestBet|priceFormat}} &euro;</span></div>
                                    </div>
                                    <div class="found-bet-params-right">
                                        <div ng-if="item.offer.view_bonus" class="found-offer-param promotion-bonus"><?= Yii::t('app', 'Werbebonus') ?>: <span class="found-offer-value">{{::item.offer.view_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                        <div ng-if="item.offer.buy_bonus" class="found-offer-param buy-bonus"><?= Yii::t('app', 'Kaufbonus') ?>: <span class="found-offer-value">{{::item.offer.buy_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                        <div class="found-date"><?= Yii::t('app', 'Läuft bis') ?>: {{::item.offer.active_till|date:"dd.MM.yyyy"}}</div>
                                    </div>
                                </div>

                                <div class="bet-offer-user-box">
                                    <div class="from-user"><?= Yii::t('app', 'von') ?>:</div>
                                    <div class="found-user-box clearfix">
                                        <a ui-sref="userProfile({id: item.offer.user.id})">
                                            <div class="found-user-avatar"><img ng-src="{{::item.offer.user.avatarSmall}}" alt=""/></div>
                                        </a>
                                        <div class="found-user-name">{{::item.offer.user|userName}}</div>
                                        <div class="offer-user-rating">
                                            <div class="star-rating">
                                                <span once-style="{width:(+item.offer.user.rating)+'%'}"></span>
                                            </div>
                                            <div class="user-feedback-count">({{::item.offer.user.feedback_count}})</div>
                                            <div ng-if="item.offer.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                            <div ng-if="item.offer.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div ng-if="(item.offer.status=='ACTIVE' || item.offer.status=='PAUSED') && item.status!='ACCEPTED' && item.isExpired " class="status-bet-offer-expired bet-item-box">
                            <div class="found-image-box">
                                <a ui-sref="offers.details({id:item.offer.id})">
                                    <div class="found-image">
                                        <img ng-src="{{::item.offer.image}}" alt=""/>
                                    </div>
                                </a>
                            </div>

                            <div class="found-bet-offer-box">

                                <div class="bet-times-box">
                                    <div class="auction-valid-time"><?= Yii::t('app', 'Gültig') ?>: <span server-countdown="item.bet_active_till"></span></div>
                                    <div class="auction-end-time"><?= Yii::t('app', 'Ende') ?>: <b>{{::item.bet_active_till|date:"dd.MM.yyyy HH:mm"}}</b></div>
                                    <div class="cobetters-count">{{::item.offer.cobettersCount}}<br/><?= Yii::t('app', 'Mitbieter') ?> </div>
                                </div>

                                <div class="found-offer-my-bids-title-text"><?= Yii::t('app', 'Du hast geboten auf') ?>:</div>

                                <div class="found-title">
                                    <h2><a ui-sref="offers.details({id:item.offer.id})">{{::item.offer.title}}</a></h2>
                                </div>
                                <ul class="found-category">
                                    <li>{{::item.offer.level1Interest}}</li>
                                    <li ng-if="item.offer.level2Interest">{{::item.offer.level2Interest}}</li>
                                    <li ng-if="item.offer.level3Interests">{{::item.offer.level3Interests}}</li>
                                </ul>

                                <div class="found-bet-params-box clearfix">
                                    <div class="found-bet-params-left">
                                        <div class="found-offer-param"><?= Yii::t('app', 'Aktuelles Höchstgebot') ?>: <span class="found-offer-value">{{::item.offer.bestBet|priceFormat}} &euro;</span></div>
                                        <div class="found-offer-param your-bid"><?= Yii::t('app', 'Dein Gebot') ?>: <span class="found-offer-value">{{::item.bet_price|priceFormat}} &euro;</span></div>
                                        <div class="found-bet-status inactive"><?= Yii::t('app', 'Abgelaufen') ?></div>
                                    </div>
                                    <div class="found-bet-params-right">
                                        <div ng-if="item.offer.view_bonus" class="found-offer-param promotion-bonus"><?= Yii::t('app', 'Werbebonus') ?>: <span class="found-offer-value">{{::item.offer.view_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                        <div ng-if="item.offer.buy_bonus" class="found-offer-param buy-bonus"><?= Yii::t('app', 'Kaufbonus') ?>: <span class="found-offer-value">{{::item.offer.buy_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                        <div class="found-date"><?= Yii::t('app', 'Läuft bis') ?>: {{::item.offer.active_till|date:"dd.MM.yyyy"}}</div>
                                    </div>
                                </div>

                                <div class="found-bet-params-box clearfix">
                                    <div class="found-bet-params-left">
                                        <div class="bet-offer-user-box">
                                            <div class="from-user"><?= Yii::t('app', 'von') ?>:</div>
                                            <div class="found-user-box clearfix">
                                                <a ui-sref="userProfile({id: item.offer.user.id})">
                                                    <div class="found-user-avatar"><img ng-src="{{::item.offer.user.avatarSmall}}" alt=""/></div>
                                                </a>
                                                <div class="found-user-name">{{::item.offer.user|userName}}</div>
                                                <div class="offer-user-rating">
                                                    <div class="star-rating">
                                                        <span once-style="{width:(+item.offer.user.rating)+'%'}"></span>
                                                    </div>
                                                    <div class="user-feedback-count">({{::item.offer.user.feedback_count}})</div>
                                                    <div ng-if="item.offer.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                                    <div ng-if="item.offer.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="found-bet-params-right">
                                        <a ui-sref="offers.bet({offer_id:item.offer.id})"><div class="offer-again-btn"><?= Yii::t('app', 'Erneut bieten') ?></div></a>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div ng-if="item.status=='ACCEPTED' && item.offer.type=='AUTOSELL'" class="head-bet-status head-bet-status-buy"><?= Yii::t('app', 'Gekauft') ?></div>
                        <div ng-if="item.status=='ACCEPTED' && item.offer.type=='AUTOSELL'" class="status-offer-buy bet-item-box">
                            <div class="found-image-box">
                                <a ui-sref="offers.details({id:item.offer.id})">
                                    <div class="found-image">
                                        <img ng-src="{{::item.offer.image}}" alt=""/>
                                    </div>
                                </a>
                            </div>
                            <div class="found-bet-offer-box">
                                <div class="found-title">
                                    <h2><a ui-sref="offers.details({id:item.offer.id})">{{::item.offer.title}}</a></h2>
                                </div>
                                <ul class="found-category">
                                    <li>{{::item.offer.level1Interest}}</li>
                                    <li ng-if="item.offer.level2Interest">{{::item.offer.level2Interest}}</li>
                                    <li ng-if="item.offer.level3Interests">{{::item.offer.level3Interests}}</li>
                                </ul>
                                <div class="found-bet-params-box clearfix">
                                    <div class="found-bet-params-left">
                                        <div class="found-offer-param"><?= Yii::t('app', 'Preis') ?>: <span class="found-offer-value">{{::item.offer.price|priceFormat}} &euro;</span></div>
                                    </div>
                                    <div class="found-bet-params-right">
                                        <div ng-if="item.offer.view_bonus" class="found-offer-param promotion-bonus"><?= Yii::t('app', 'Werbebonus') ?>: <span class="found-offer-value">{{::item.offer.view_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                        <div ng-if="item.offer.buy_bonus" class="found-offer-param buy-bonus"><?= Yii::t('app', 'Kaufbonus') ?>: <span class="found-offer-value">{{::item.offer.buy_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                        <div class="found-date"><?= Yii::t('app', 'Läuft bis') ?>: {{::item.offer.active_till|date:"dd.MM.yyyy"}}</div>
                                    </div>
                                </div>
                                <div class="found-bet-params-box two clearfix">
                                    <div class="found-bet-params-left">
                                        <div class="bet-offer-user-box">
                                            <div class="from-user"><?= Yii::t('app', 'von') ?>:</div>
                                            <div class="found-user-box clearfix">
                                                <a ui-sref="userProfile({id: item.offer.user.id})">
                                                    <div class="found-user-avatar"><img ng-src="{{::item.offer.user.avatarSmall}}" alt=""/></div>
                                                </a>
                                                <div class="found-user-name">{{::item.offer.user|userName}}</div>
                                                <div class="offer-user-rating">
                                                    <div class="star-rating">
                                                        <span once-style="{width:(+item.offer.user.rating)+'%'}"></span>
                                                    </div>
                                                    <div class="user-feedback-count">({{::item.offer.user.feedback_count}})</div>
                                                    <div ng-if="item.offer.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                                    <div ng-if="item.offer.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div ng-if="item.pay_status=='INVITED'" class="found-bet-params-right">
                                        <a ui-sref="offers.pay({id:item.id})"><div class="offer-again-btn"><?= Yii::t('app', 'Jetzt bezahlen') ?></div></a>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>


            </div>



            <div class="bottom-corner"></div>

        </div>
    </div>
</div>