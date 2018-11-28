<div class="searches offers">
    <div class="container">
        <div ng-click="showInfoPopup('view-offers-search')" ng-class="{'blink':isOneShowInfoPopup('view-offers-search')}" class="info-popup-btn"></div>

        <div class="welcome-text">
            <h2><?=Yii::t('app','Werbung lesen / Etwas kaufen')?></h2>
        </div>

        <div class="searches-content clearfix">
            <div class="searches-filter-content">
                <div class="searches-filter-box clearfix">
                    <ul class="searches-filter-list">
                        <li class="searches-filter-item">
                            <label><?=Yii::t('app','Interessenkategorie')?>:</label>
                            <div class="field-box-select filter-select" dropdown-toggle select-click>
                                <select ng-model="filter.level1_interest_id" selectpicker ng-options="interest.id as interest.title for interest in interests | filter:{parent_id:null}">
                                    <option value=""></option>
                                </select>
                            </div>
                        </li>

                        <li ng-if="filter.level1_interest_id && (interests | filter: {parent_id:filter.level1_interest_id}).length>0" class="searches-filter-item">
                            <label><?=Yii::t('app','Unterkategorie')?>:</label>
                            <div class="field-box-select filter-select" dropdown-toggle select-click>
                                <select id="level2_interest_id" class="filter-select-refresh" ng-model="filter.level2_interest_id" selectpicker ng-options="interest.id as interest.title for interest in interests | filter: {parent_id:filter.level1_interest_id} : offerSearchCtrl.filterInterestComparator">
                                    <option value=""></option>
                                </select>
                            </div>
                        </li>

                        <li ng-if="filter.level2_interest_id && (interests | filter: {parent_id:filter.level2_interest_id}).length > 0" class="searches-filter-item">
                            <label><?=Yii::t('app','Themenfilter')?>:</label>
                            <div class="field-box-select filter-select" dropdown-toggle select-click>
                                <select id="level3_interest_id" class="filter-select-refresh" ng-model="filter.level3_interest_id" selectpicker ng-options="interest.id as interest.title for interest in interests | filter: {parent_id:filter.level2_interest_id} : offerSearchCtrl.filterInterestComparator">
                                    <option value=""></option>
                                </select>
                            </div>
                        </li>

                        <li ng-repeat="param in params | filter:offerSearchCtrl.paramFilter" class="searches-filter-item">
                            <label>{{param.title}}</label>
                            <div class="field-box-select filter-select" dropdown-toggle select-click>
                                <select ng-model="filter.params[param.id]" selectpicker="{title:''}" ng-options="value.id as value.title for value in param.values">
                                </select>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="searches-result-content">
                <div class="searches-result-box" scroll-load="offerSearchCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="results.hasMore">
                    <div class="sort-content clearfix">
                        <div class="sort-box">
                            <label><?=Yii::t('app', 'Filtern nach');?>:</label>
                            <div class="field-box-select" dropdown-toggle select-click>
                                <select ng-model="filter.type" selectpicker>
                                    <option value=""><?= Yii::t('app', 'Alle'); ?></option>
                                    <option value="AUTOSELL"><?= Yii::t('app', 'Sofortkauf'); ?></option>
                                    <option value="AUCTION"><?= Yii::t('app', 'Bieterverfahren'); ?></option>
                                    <option value="AD"><?= Yii::t('app', 'Ohne Kaufmöglichkeit'); ?></option>
                                </select>
                            </div>
							
                        </div>
                        <div class="sort-box">
                            <label><?=Yii::t('app', 'Sortieren nach');?>:</label>
                            <div class="field-box-select" dropdown-toggle select-click>
                                <select ng-model="filter.sort" selectpicker>
                                    <option value="create_dt"><?= Yii::t('app', 'Datum'); ?></option>
                                    <option value="relevancy"><?= Yii::t('app', 'Relevance'); ?></option>
                                    <option value="view_bonus"><?= Yii::t('app', 'Werbebonus'); ?></option>
                                    <option value="buy_bonus"><?= Yii::t('app', 'Kaufbonus'); ?></option>
                                    <option value="rating"><?= Yii::t('app', 'Wertung'); ?></option>
                                </select>
                            </div>
                        </div>
						<div class="sort-box">
                            <label><?=Yii::t('app', 'Land auswählen');?>:</label>
                            <div class="field-box-select" dropdown-toggle select-click>
                                <select ng-model="filter.country" selectpicker ng-options="countrySelect.id as countrySelect.name for countrySelect in offersCountryArray">>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div ng-if="results.items.length == 0" class="result-empty-text">
                        <?= Yii::t('app', 'Leider gibt es momentan keine Werbung bzw. Produktangebote, die Deinen eingegebenen Interessen entsprechen.') ?>
                    </div>

                    <div ng-repeat="item in results.items" class="found-item-box item-offers">
                        <div class="found-image-box">
                            <a ui-sref="offers.details({id:item.id})">
                                <div class="found-image">
                                    <img ng-src="{{::item.image}}" alt=""/>
                                </div>
                            </a>
                        </div>

                        <div class="found-offers-box">

                            <div class="found-offer-type" ng-click="showInfoPopup('info-offer-type')">
                                <span ng-if="item.type == 'AUCTION'"><?= Yii::t('app', 'Bieterverfahren') ?></span>
                                <span ng-if="item.type == 'AD'"><?= Yii::t('app', 'Keine Kaufmöglichkeit') ?></span>
                                <span ng-if="item.type == 'AUTOSELL'"><?= Yii::t('app', 'Sofortkauf') ?></span>
                            </div>

                            <div class="found-title">
                                <h2><a ui-sref="offers.details({id:item.id})">{{::item.title}}</a></h2>
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
                                            <div class="found-user-name">{{::item.user|userName}} <div ng-click="updateCountry(item.user.id,results.items)" id="{{::item.user.flag}}" class="flag flag-32 flag-{{item.user.flag}}"></div></div>
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
                                    <div class="found-favorite favorite" ng-if="!item.favorite" ng-click="offerSearchCtrl.addFavorite(item.id)" ng-class="{'favorite-false': !item.favorite}"><?= Yii::t('app', 'Merken'); ?></div>
                                    <div class="found-favorite favorite" ng-if="item.favorite" ng-class="{'favorite-true': item.favorite}"><?= Yii::t('app', 'Gemerkt'); ?></div>

                                    <div class="found-count-offer-view">{{::item.count_offer_view}}</div>

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
