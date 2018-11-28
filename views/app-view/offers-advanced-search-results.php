<div class="searches">
    <div class="container">
        <div class="welcome-text">
            <h2><?=Yii::t('app','Inserate durchsuchen')?></h2>
        </div>

        <div class="searches-content clearfix">
            <div class="searches-result-content">
                <div class="searches-result-box" scroll-load="offerAdvancedSearchResults.loadMore" scroll-load-visible="0.7" scroll-load-has-more="results.hasMore">
                    <div ng-if="results.items.length == 0" class="result-empty-text">
                        <?= Yii::t('app', 'Leider gibt es momentan keine Werbung bzw. Produktangebote, die Deinen eingegebenen Interessen entsprechen.') ?>
                    </div>

                    <div ng-repeat="item in results.items" class="found-item-box item-offers">
					
						<div class="advertising_banner" ng-if="item.advertising_display_name && item.advertising_type == 'BANNER' && item.id && item.user_bonus && item.link && item.banner">
							<div id="advertising-{{item.id}}" a-data-id="{{item.id}}" a-data-user-bonus="{{item.user_bonus}}" ng-click="offerAdvancedSearchResults.setAdvertising({{item.id}}, {{item.user_bonus}}, {{item.click_interval}}, {{item.popup_interval}})">
								<a href="{{item.link}}" target="_blank" title="{{item.advertising_display_name}}"><img src ="{{item.banner}}" alt="{{item.advertising_display_name}}"></a>
							</div>
						</div>
					
                        <div class="found-image-box" ng-if="!item.advertising_type">
                            <a ui-sref="offers.details({id:item.id,noViewBonus:1})">
                                <div class="found-image">
                                    <img ng-src="{{::item.image}}" alt=""/>
                                </div>
                            </a>
                        </div>
                        <div class="found-offers-box" ng-if="!item.advertising_type">
                            <div class="found-offer-type" ng-click="showInfoPopup('info-offer-type')">
                                <span ng-if="item.type == 'AUCTION'"><?= Yii::t('app', 'Bieterverfahren') ?></span>
                                <span ng-if="item.type == 'AD'"><?= Yii::t('app', 'Keine Kaufmöglichkeit') ?></span>
                                <span ng-if="item.type == 'AUTOSELL'"><?= Yii::t('app', 'Sofortkauf') ?></span>
                            </div>

                            <div class="found-title">
                                <h2><a ui-sref="offers.details({id:item.id,noViewBonus:1})">{{::item.title}}</a></h2>
                            </div>
                            <ul class="found-category">
                                <li>{{::item.level1Interest}}</li>
                                <li ng-if="item.level2Interest">{{::item.level2Interest}}</li>
                                <li ng-if="item.level3Interests">{{::item.level3Interests}}</li>
                            </ul>
                            <div class="found-offers-info-wrap clearfix">
                                <div class="found-offers-info-wrap-box" ng-if="item.type != 'AD'">
                                    <div class="found-offers-info-box">
                                        <div class="found-offers-info-box-left">
                                            <div ng-if="item.type == 'AUTOSELL'" class="found-offer-param"><?= Yii::t('app', 'Preis') ?>: <span class="found-offer-value">{{::item.price|priceFormat}} &euro;</span></div>
                                            <div ng-if="item.type == 'AUCTION'" class="found-offer-param"><?= Yii::t('app', 'Preisvorstellung') ?>: <span class="found-offer-value">{{::item.price|priceFormat}} &euro;</span></div>
                                            <div class="found-offer-param">{{::item.zip}} {{::item.city}}</div>
                                        </div>
                                        <div class="found-offers-info-box-right">
                                            <div ng-if="item.view_bonus" class="found-offer-param promotion-bonus"><?= Yii::t('app', 'Werbebonus') ?>: <span class="found-offer-value">{{::item.view_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                            <div ng-if="item.buy_bonus" class="found-offer-param buy-bonus"><?= Yii::t('app', 'Kaufbonus') ?>: <span class="found-offer-value">{{::item.buy_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                        </div>
                                    </div>
                                    <div class="found-offers-info-box">
                                        <div class="found-offers-info-box-left">
                                            <div class="found-user-box">
                                                <a ui-sref="userProfile({id: item.user.id})">
                                                    <div class="found-user-avatar"><img ng-src="{{::item.user.avatarSmall}}" alt=""/></div>
                                                </a>
                                                <div class="found-user-name">{{::item.user|userName}}</div>
                                                <div class="offer-user-rating">
                                                    <div class="star-rating">
                                                        <span once-style="{width:(+item.user.rating)+'%'}"></span>
                                                    </div>
                                                    <div class="user-feedback-count">({{::item.user.feedback_count}})</div>
                                                    <div ng-if="item.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                                    <div ng-if="item.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="found-offers-info-box-right">
                                            <div class="found-offer-param"><?= Yii::t('app', ' Aktiv bis') ?>: <b>{{::item.active_till|date:"dd.MM.yyyy"}}</b></div>
                                            <div class="found-offer-param" ng-if="item.show_amount == 1"><?= Yii::t('app', 'St&uuml;ckzahl') ?>: <b>{{::item.amount}}</b></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="found-offers-info-wrap-box" ng-if="item.type == 'AD'">
                                    <div class="found-offers-info-box">
                                        <div class="found-offers-info-box-left">
                                            <div class="found-user-box">
                                                <a ui-sref="userProfile({id: item.user.id})">
                                                    <div class="found-user-avatar"><img ng-src="{{::item.user.avatarSmall}}" alt=""/></div>
                                                </a>
                                                <div class="found-user-name">{{::item.user|userName}}</div>
                                                <div class="offer-user-rating">
                                                    <div class="star-rating">
                                                        <span once-style="{width:(+item.user.rating)+'%'}"></span>
                                                    </div>
                                                    <div class="user-feedback-count">({{::item.user.feedback_count}})</div>
                                                    <div ng-if="item.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                                    <div ng-if="item.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="found-offers-info-box-right">
                                            <div ng-if="item.view_bonus" class="found-offer-param promotion-bonus"><?= Yii::t('app', 'Werbebonus') ?>: <span class="found-offer-value">{{::item.view_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                                            <div class="found-offer-param"><?= Yii::t('app', ' Aktiv bis') ?>: <b>{{::item.active_till|date:"dd.MM.yyyy"}}</b></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="found-status-box clearfix">
                                    <div class="found-date">{{::item.create_dt|date:"dd.MM.yyyy"}}</div>
                                    <div class="found-relevance relevance">
                                        <?= Yii::t('app', 'Relevance'); ?>
                                        <div class="relevance-percent">{{::item.relevancy}}%</div>
                                    </div>
                                    <div class="found-favorite favorite" ng-if="!item.favorite" ng-click="offerAdvancedSearchResults.addFavorite(item.id)" ng-class="{'favorite-false': !item.favorite}"><?= Yii::t('app', 'Merken'); ?></div>
                                    <div class="found-favorite favorite" ng-if="item.favorite" ng-class="{'favorite-true': item.favorite}"><?= Yii::t('app', 'Gemerkt'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
