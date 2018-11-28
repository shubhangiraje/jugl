<div class="searches-my">
    <div class="container">
        <div ng-click="showInfoPopup('view-searches-my')" ng-class="{'blink':isOneShowInfoPopup('view-searches-my')}" class="info-popup-btn"></div>

        <div class="welcome-text">
            <h2><?=Yii::t('app','Auftrag annehmen')?></h2>
        </div>

        <div class="offers-container" scroll-load="searchRequestMyListCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="results.hasMore">
            <?php /*
            <div class="offered-filter clearfix">
                <ul class="offered-filter-list">
                    <li>
                        <div class="field-box-select" dropdown-toggle select-click>
                            <select ng-model="filter.sort1" selectpicker="{title:''}">
                                <option value="">Interessenkategorie</option>
                            </select>
                        </div>
                    </li>
                    <li>
                        <div class="field-box-select" dropdown-toggle select-click>
                            <select ng-model="filter.sort2" selectpicker="{title:''}">
                                <option value="">Unterkategorie</option>
                            </select>
                        </div>
                    </li>
                    <li>
                        <div class="field-box-select" dropdown-toggle select-click>
                            <select ng-model="filter.sort3" selectpicker="{title:''}">
                                <option value="">Themenfilter</option>
                            </select>
                        </div>
                    </li>
                    <li>
                        <div class="field-box-select" dropdown-toggle select-click>
                            <select ng-model="filter.sort4" selectpicker="{title:''}">
                                <option value="">Sortieren nach</option>
                            </select>
                        </div>
                    </li>
                </ul>
            </div>
            */ ?>

            <div ng-if="results.items.length == 0" class="result-empty-text">
                <?= Yii::t('app', 'Leider hast Du noch keine Suchaufträge erstellt, Du kannst dies mit der „Menschlichen Suchmaschine“ tun (Suchauftrag erstellen).') ?>
            </div>

            <div class="offer-box" ng-repeat="item in results.items" ng-class="{'status-scheduled': item.status=='SCHEDULED'}">

                <div ng-if="item.status=='SCHEDULED'" class="status-scheduled-label-text"><?= Yii::t('app','Wartet auf Veröffentlichung') ?></div>

                <div class="offer clearfix">
                    <div class="offer-picture-box">
                        <a ui-sref="searches.details({id:item.id})">
                            <div class="offer-picture">
                                <img ng-src="{{::item.image}}" alt=""/>
                            </div>
                        </a>
                    </div>

                    <div class="offer-info-box">
                        <div class="offer-info-title"><h2><a ui-sref="searches.details({id:item.id})">{{::item.title}}</a></h2></div>
                        <ul class="offer-info-category">
                            <li>{{::item.level1Interest}}</li>
                            <li ng-if="item.level2Interest">{{::item.level2Interest}}</li>
                            <li ng-if="item.level3Interests">{{::item.level3Interests}}</li>
                        </ul>
                        <a href="" ng-click="searchRequestMyListCtrl.close(item.id)"><?=Yii::t('app','Anzeige abschalten')?></a>
                    </div>

                    <div class="offer-info-others-box">
                        <div class="offer-info-status-box clearfix">
                            <div class="offer-date">{{::item.create_dt|date:"dd.MM.yyyy H:mm:ss"}}</div>
                            <div class="offer-count">
                                <span>{{::item.searchRequestOffers.length}}</span><?= Yii::t('app', 'Angebote'); ?>
                            </div>
                        </div>
                        <div ng-if="item.price_to" class="offer-price"><span>{{::item.price_from|priceFormat}} - {{::item.price_to|priceFormat}} &euro;</span></div>
                        <div ng-if="!item.price_to" class="offer-price"><span>{{::item.price_from|priceFormat}} &euro;</span></div>
                        <div once-if="item.bonus" class="offer-bonus">
                            <span class="bonus-value">{{::item.bonus|priceFormat}} <jugl-currency></jugl-currency></span>
                        </div>
                    </div>

                    <div class="btn-del-offer">
                        <button ng-click="searchRequestMyListCtrl.delete(item.id)"></button>
                    </div>



<?php /*
                    <div class="offer-picture-box">
                        <div class="offer-picture">
                            <img ng-src="{{::item.image}}" alt=""/>
                        </div>
                    </div>
                    <div class="offer-info-box">
                        <div class="offer-info-title"><h2>{{::item.title}}</h2></div>
                        <ul class="offer-info-category">
                            <li>{{::item.level1Interest}}</li>
                            <li>{{::item.level2Interest}}</li>
                            <li>{{::item.level3Interests}}</li>
                        </ul>
                        <button ng-click="searchRequestMyListCtrl.delete(item.id)">L&ouml;schen</button>
                    </div>
                    <div class="offer-info-others-box">
                        <div class="offered-date">{{::item.create_dt|date:"dd.MM.yyyy"}}</div>
                        <div class="offered-count-offers">
                            <span class="count-offers">{{::item.searchRequestOffers.length}}</span>
                            Angebote
                        </div>
                        <div class="offered-price">{{::item.price_from|priceFormat}} - {{::item.price_to|priceFormat}} &euro;</div>
                        <div once-if="item.bonus" class="offered-jugl">{{::item.bonus|priceFormat}} <jugl-currency></jugl-currency></div>
                    </div>
 */ ?>

                </div>


                <ul class="offer-users-list offer-request-list">
                    <li ng-repeat="offer in item.searchRequestOffers" class="clearfix">
                        <div class="offer-users-list-box">
                            <div class="offer-request-left">
                                <div class="offer-request-user-box clearfix">
                                    <div class="offer-user-box clearfix">
                                        <a ui-sref="userProfile({id: offer.user.id})">
                                            <div class="offer-user-avatar"><img ng-src="{{::offer.user.avatarSmall}}" alt=""/></div>
                                        </a>
                                        <div class="offer-user-name">{{::offer.user|userName}}</div>
                                        <div class="offer-user-rating">
                                            <div class="star-rating">
                                                <span once-style="{width:(+offer.user.rating)+'%'}"></span>
                                            </div>
                                            <div class="user-feedback-count">({{::offer.user.feedback_count}})</div>
                                            <div ng-if="offer.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                            <div ng-if="offer.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="offer-user-relevance-box">
                                    <div class="offer-user-relevance">{{::offer.relevancy}}%</div>
                                </div>
                            </div>

                            <div class="offer-request-right">
                                <div class="offer-request-comments-box offer-comments">
                                    <p ng-bind-html="offer.description|linky"></p>
                                </div>

                                <div class="offer-request-price-box">
                                    <div ng-if="offer.price_to" class="offer-price"><span>{{::offer.price_from|priceFormat}} - {{::offer.price_to|priceFormat}} &euro;</span></div>
                                    <div ng-if="!offer.price_to" class="offer-price"><span>{{::offer.price_from|priceFormat}} &euro;</span></div>
                                </div>

                                <div class="offer-request-link-box">
                                    <a ui-sref="searches.offerDetails({id:offer.id})" class="offer-link"><?= Yii::t('app', 'Details anzeigen') ?></a>
                                    <div ng-if="offer.status=='ACCEPTED'" class="offer-request-status contacted"><?=Yii::t('app','SEARCH_REQUEST_OFFER_STATUS_ACCEPTED')?></div>
                                    <div ng-if="offer.status=='CONTACTED'" class="offer-request-status contacted"><?=Yii::t('app','SEARCH_REQUEST_OFFER_STATUS_CONTACTED')?></div>
                                    <div ng-if="offer.status=='REJECTED'" class="offer-request-status rejected"><?=Yii::t('app','SEARCH_REQUEST_OFFER_STATUS_REJECTED')?></div>
                                </div>

                            </div>
                        </div>
                    </li>
                </ul>


<?php /*
                <ul class="offered-users-list">
                    <li ng-repeat="offer in item.searchRequestOffers" class="clearfix">
                        <div class="offered-user-left">
                            <div class="offered-user-box">
                                <div class="offered-user">
                                    <div class="offered-user-avatar"><img ng-src="{{::offer.user.avatarSmall}}" alt=""/></div>
                                    <div class="offered-user-name">{{::offer.user|userName}}</div>
                                    <div class="offered-user-rating">
                                        <div class="star-rating">
                                            <span once-style="{width:(+offer.user.rating)+'%'}"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="offered-user-relevance">
                                <div class="relevance">{{::offer.relevancy}}%</div>
                            </div>
                        </div>

                        <div class="offered-user-right">
                            <div class="offered-user-text">
                                <p>{{::offer.description}}</p>
                            </div>
                            <div class="offered-user-price">
                                <div class="offered-price">{{::offer.price_from|priceFormat}} - {{::offer.price_to|priceFormat}} &euro;</div>
                            </div>
                            <div class="offered-view-details">
                                <a ui-sref="searches.offerDetails({id:offer.id})"><?= Yii::t('app', 'Details anzeigen') ?></a>
                            </div>
                        </div>
                    </li>
                </ul>

 */ ?>

            </div>

            <div class="bottom-corner"></div>
        </div>




    </div>
</div>