<div class="favorites">
    <div class="container">
        <div ng-click="showInfoPopup('view-favorites')" ng-class="{'blink':isOneShowInfoPopup('view-favorites')}" class="info-popup-btn"></div>

        <div class="welcome-text">
            <h2><?=Yii::t('app','Mein Merkzettel')?></h2>
        </div>

        <div class="offers-container" scroll-load="favoritesCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="results.hasMore">
            <div class="offers-filter-container clearfix">
                <div class="offers-filter-box">
                    <div class="field-box-select" dropdown-toggle select-click>
                        <select ng-model="filter.type" selectpicker>
                            <option value=""><?= Yii::t('app', 'Alles zeigen'); ?></option>
                            <option value="search_request"><?= Yii::t('app', 'Suchaufträge'); ?></option>
                            <option value="offer"><?= Yii::t('app', 'Werbungen'); ?></option>
                        </select>
                    </div>
                </div>
            </div>


            <div ng-if="results.items.length == 0" class="result-empty-text">
                <?= Yii::t('app', 'Du hast noch nichts in Deinem Merkzettel abgespeichert.') ?>
            </div>

            <div  ng-repeat="item in results.items">
                <div ng-if="item.type=='search_request'" class="offer-box offer-type1">
                    <div class="offer clearfix">
                        <div class="offer-picture-box">
                            <a ui-sref="searches.details({id:item.id})">
                                <div class="offer-picture">
                                    <img ng-src="{{::item.favorite.image}}" alt=""/>
                                </div>
                            </a>
                        </div>

                        <div class="offer-info-box">
                            <div class="offer-info-type"><?= Yii::t('app', 'Suchanzeige') ?></div>
                            <div class="offer-info-title"><h2><a ui-sref="searches.details({id:item.id})">{{::item.favorite.title}}</a></h2></div>
                            <ul class="offer-info-category">
                                <li>{{::item.favorite.level1Interest}}</li>
                                <li ng-if="item.favorite.level2Interest">{{::item.favorite.level2Interest}}</li>
                                <li ng-if="item.favorite.level3Interests">{{::item.favorite.level3Interests}}</li>
                            </ul>
                            <div class="offer-user-box clearfix">
                                <a ui-sref="userProfile({id: item.favorite.user.id})">
                                    <div class="offer-user-avatar"><img ng-src="{{::item.favorite.user.avatarSmall}}" alt=""/></div>
                                </a>
                                <div class="offer-user-name">{{::item.favorite.user|userName}} <div ng-click="updateCountry(item.favorite.user.id,results.items)" id="{{::item.favorite.user.flag}}" class="flag flag-32 flag-{{::item.favorite.user.flag}}"></div></div>
                                
                                <div class="offer-user-rating">
                                    <div class="star-rating">
                                        <span once-style="{width:(+item.favorite.user.rating)+'%'}"></span>
                                    </div>
                                    <div class="user-feedback-count">({{::item.favorite.user.feedback_count}})</div>
                                    <div ng-if="item.favorite.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                    <div ng-if="item.favorite.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                </div>
                            </div>
                        </div>

                        <div class="offer-info-others-box">
                            <div class="offer-info-status-box clearfix">
                                <div class="offer-date">{{::item.favorite.create_dt|date:"dd.MM.yyyy"}}</div>
                            </div>

                            <div ng-if="item.favorite.price_to" class="offer-price"><span>{{::item.favorite.price_from|priceFormat}} - {{::item.favorite.price_to|priceFormat}} &euro;</span></div>
                            <div ng-if="!item.favorite.price_to" class="offer-price"><span>{{::item.favorite.price_from|priceFormat}} &euro;</span></div>
                        </div>

                        <div class="btn-del-offer">
                            <button ng-click="favoritesCtrl.delete(item.id, item.type)"></button>
                        </div>

                    </div>
                </div>

                <div ng-if="item.type=='offer'" class="offer-box offer-type3">
                    <div class="offer clearfix">
                        <div class="offer-picture-box">
                            <a ui-sref="offers.details({id:item.id})">
                                <div class="offer-picture">
                                    <img ng-src="{{::item.favorite.image}}" alt=""/>
                                </div>
                            </a>
                        </div>

                        <div class="offer-info-box">
                            <div class="offer-info-type"><?= Yii::t('app', 'Angebot') ?></div>
                            <div class="offer-info-title"><h2><a ui-sref="offers.details({id:item.id})">{{::item.favorite.title}}</a></h2></div>
                            <ul class="offer-info-category">
                                <li>{{::item.favorite.level1Interest}}</li>
                                <li ng-if="item.favorite.level2Interest">{{::item.favorite.level2Interest}}</li>
                                <li ng-if="item.favorite.level3Interests">{{::item.favorite.level3Interests}}</li>
                            </ul>
                            <div class="offer-user-box clearfix">
                                <a ui-sref="userProfile({id: item.favorite.user.id})">
                                    <div class="offer-user-avatar"><img ng-src="{{::item.favorite.user.avatarSmall}}" alt=""/></div>
                                </a>
                                <div class="offer-user-name">{{::item.favorite.user|userName}} <div id="{{::item.favorite.user.flag}}" class="flag flag-32 flag-{{::item.favorite.user.flag}}"></div></div>                            
                                <div class="offer-user-rating">
                                    <div class="star-rating">
                                        <span once-style="{width:(+item.favorite.user.rating)+'%'}"></span>
                                    </div>
                                    <div class="user-feedback-count">({{::item.favorite.user.feedback_count}})</div>
                                    <div ng-if="item.favorite.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                    <div ng-if="item.favorite.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                </div>
                            </div>
                        </div>

                        <div class="offer-info-others-box">
                            <div class="offer-info-status-box clearfix">
                                <div class="offer-date">{{::item.favorite.create_dt|date:"dd.MM.yyyy"}}</div>
                            </div>

                            <div class="offer-price"><span>{{::item.favorite.price|priceFormat}} &euro;</span></div>

                            <div ng-if="item.favorite.view_bonus>0" class="offer-bonus promotion-bonus">
                                <?= Yii::t('app', 'Werbebonus: ') ?>
                                <span class="bonus-value">{{::item.favorite.view_bonus}} <jugl-currency></jugl-currency></span>
                            </div>
                            <div ng-if="item.favorite.buy_bonus>0" class="offer-bonus buy-bonus">
                                <?= Yii::t('app', 'Kaufbonus: ') ?>
                                <span class="bonus-value">{{::item.favorite.buy_bonus}} <jugl-currency></jugl-currency></span>
                            </div>
                        </div>

                        <div class="btn-del-offer">
                            <button ng-click="favoritesCtrl.delete(item.id, item.type)"></button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="bottom-corner"></div>

        </div>
    </div>
</div>