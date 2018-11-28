<div class="deals-completed">
    <div class="container">
        <div ng-click="showInfoPopup('view-deals-completed')" ng-class="{'blink':isOneShowInfoPopup('view-deals-completed')}" class="info-popup-btn"></div>
        <div class="welcome-text">
            <h2><?=Yii::t('app','Abgeschlossene Gesch&auml;fte & Bewertungen')?></h2>
        </div>
        <div class="offers-container" scroll-load="dealsCompletedCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="results.hasMore">
            <div class="offers-filter-container clearfix">
                <div class="offers-filter-box">
                    <div class="field-box-select" dropdown-toggle select-click>
                        <select ng-model="filter.type" selectpicker>
                            <option value=""><?= Yii::t('app', 'Typ des Handels'); ?></option>
                            <option value="search_request"><?= Yii::t('app', 'Deine Suchauftr&auml;ge'); ?></option>
                            <option value="search_request_offer"><?= Yii::t('app', 'Deine Angebote auf Suchauftr&auml;ge'); ?></option>
                            <option value="offer"><?= Yii::t('app', 'verkaufte Artikel'); ?></option>
                            <option value="offer_request"><?= Yii::t('app', 'gekaufte Artikel'); ?></option>
                            <option value="offer|1"><?= Yii::t('app', 'ich habe abgemahnt'); ?></option>
                            <option value="offer_request|1"><?= Yii::t('app', 'ich wurde abgemahnt'); ?></option>
                            <option value="||DELETED"><?=Yii::t('app','Gelöscht')?></option>
                            <option value="|||1"><?=Yii::t('app','Noch nicht bewertet')?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div ng-if="results.items.length == 0" class="result-empty-text">
                <?= Yii::t('app', 'Momentan gibt es noch keine von Dir abgeschlossenen oder dem Filter entsprechenden Geschäfte.') ?>
            </div>

            <div  ng-repeat="item in results.items">

                <div ng-if="item.type=='search_request'" class="offer-box offer-type1" ng-class="{'offer-type-delete': item.deal.status=='DELETED'}">
                    <div class="offer clearfix">

                        <div class="btn-offer-deletes" ng-if="item.deal.status=='DELETED'">
                            <button class="btn-offer-unlink" ng-click="dealsCompletedCtrl.unlink(item)"><?= Yii::t('app', 'Endgültig löschen') ?></button>
                            <button class="btn-offer-undelete" ng-click="dealsCompletedCtrl.undelete(item)"><?= Yii::t('app', 'Wiederherstellen') ?></button>
                        </div>

                        <div class="offer-picture-box">
                            <a ui-sref="searches.details({id:item.deal.id})">
                                <div class="offer-picture">
                                    <img ng-src="{{::item.deal.image}}" alt=""/>
                                </div>
                            </a>
                        </div>

                        <div class="offer-info-box">
                            <div class="offer-info-type"><?= Yii::t('app', 'Dein Suchauftrag') ?></div>
                            <div class="offer-info-title"><h2><a ui-sref="searches.details({id:item.deal.id})">{{::item.deal.title}}</a></h2></div>
                            <ul class="offer-info-category">
                                <li>{{::item.deal.level1Interest}}</li>
                                <li ng-if="item.deal.level2Interest">{{::item.deal.level2Interest}}</li>
                                <li ng-if="item.deal.level3Interests">{{::item.deal.level3Interests}}</li>
                            </ul>
                        </div>

                        <div class="offer-info-others-box">
                            <div class="offer-info-status-box clearfix">
                                <div class="offer-date">{{::item.deal.create_dt|date:"dd.MM.yyyy"}}</div>
                            </div>
                            <div ng-if="item.deal.price_to" class="offer-price"><span>{{::item.deal.price_from|priceFormat}} - {{::item.deal.price_to|priceFormat}} &euro;</span></div>
                            <div ng-if="!item.deal.price_to" class="offer-price"><span>{{::item.deal.price_from|priceFormat}} &euro;</span></div>
                        </div>
                    </div>

                    <ul class="offer-users-list offer-request-list">
                        <li ng-repeat="dealOffer in item.dealOffers">
                            <div class="offer-users-list-box">
                                <div class="offer-request-left">
                                    <div class="deal-offer-text-box clearfix">
                                        <div class="deal-offer-text">
                                            <?= Yii::t('app', 'Du hast folgendes Angebot erhalten') ?>
                                        </div>
                                    </div>
                                    <div class="offer-user-relevance-box">
                                        <div class="offer-user-relevance">{{::dealOffer.relevancy}}%</div>
                                    </div>
                                </div>

                                <div class="offer-request-right">
                                    <div class="offer-request-comments-box offer-comments">
                                        <p>{{::dealOffer.description}}</p>
                                        <div class="deal-offer-price">
                                            <div ng-if="dealOffer.price_to" class="offer-price"><span>{{::dealOffer.price_from|priceFormat}} - {{::dealOffer.price_to|priceFormat}} &euro;</span></div>
                                            <div ng-if="!dealOffer.price_to" class="offer-price"><span>{{::dealOffer.price_from|priceFormat}} &euro;</span></div>
                                        </div>
                                    </div>

                                    <div class="deal-offer-user-box clearfix">
                                        <div class="offer-from-user-text"><?= Yii::t('app', 'von') ?>:</div>
                                        <div class="offer-user-box clearfix">
                                            <a ui-sref="userProfile({id: dealOffer.user.id})">
                                                <div class="offer-user-avatar"><img ng-src="{{::dealOffer.user.avatarSmall}}" alt=""/></div>
                                            </a>
                                            <div class="offer-user-name">{{::dealOffer.user|userName}}</div>
                                            <div class="offer-user-rating">
                                                <div class="star-rating">
                                                    <span once-style="{width:(+dealOffer.user.rating)+'%'}"></span>
                                                </div>
                                                <div class="user-feedback-count">({{::dealOffer.user.feedback_count}})</div>
                                                <div ng-if="dealOffer.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                                <div ng-if="dealOffer.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="deal-accept-box clearfix">
                                <div class="deal-accept-text">
                                    <?= Yii::t('app', 'Du wurdest dafür bewertet mit') ?>:
                                </div>

                                <div class="deal-accept-rating-box clearfix">
                                    <div class="star-rating">
                                        <span ng-style="{width:(+dealOffer.counter_rating)+'%'}"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="deal-accept-box clearfix">
                                <div class="deal-accept-text">
                                    <span ng-if="dealOffer.rating"><?= Yii::t('app', 'Du hast') ?> <a ui-sref="userProfile({id: dealOffer.user.id})">{{::dealOffer.user|userName}}</a> <?= Yii::t('app', 'daf&uuml;r bewertet mit') ?>:</span>
                                    <span ng-if="!dealOffer.rating" class="red"><?= Yii::t('app','Noch nicht bewertet') ?></span>
                                </div>
                                <div class="deal-accept-rating-box clearfix">
                                    <div ng-if="dealOffer.rating" class="star-rating">
                                        <span ng-style="{width:(+dealOffer.rating)+'%'}"></span>
                                    </div>
                                    <a class="offer-link" href="" ng-click="dealsCompletedCtrl.updateUserFeedback(item.dealOffer.user_feedback_id,dealOffer.id);"><?= Yii::t('app', 'Handel bewerten') ?></a>
                                </div>
                            </div>

                        </li>
                    </ul>

                </div>


                <div ng-if="item.type=='search_request_offer'" class="offer-box offer-type2">
                    <div class="offer clearfix">
                        <div class="offer-picture-box">
                            <a ui-sref="searches.details({id:item.deal.id})">
                                <div class="offer-picture">
                                    <img ng-src="{{::item.deal.image}}" alt=""/>
                                </div>
                            </a>
                        </div>

                        <div class="offer-info-box">
                            <div class="offer-info-type"><?= Yii::t('app', 'Dein Angebot auf den Suchauftrag') ?></div>
                            <div class="offer-info-title"><h2><a ui-sref="searches.details({id:item.deal.id})">{{::item.deal.title}}</a></h2></div>
                            <ul class="offer-info-category">
                                <li>{{::item.deal.level1Interest}}</li>
                                <li ng-if="item.deal.level2Interest">{{::item.deal.level2Interest}}</li>
                                <li ng-if="item.deal.level3Interests">{{::item.deal.level3Interests}}</li>
                            </ul>

                            <div class="deal-user-box clearfix">
                                <div class="offer-from-user-text"><?= Yii::t('app', 'von') ?>:</div>
                                <div class="offer-user-box clearfix">
                                    <a ui-sref="userProfile({id: item.deal.user.id})">
                                        <div class="offer-user-avatar"><img ng-src="{{::item.deal.user.avatarSmall}}" alt=""/></div>
                                    </a>
                                    <div class="offer-user-name">{{::item.deal.user|userName}}</div>
                                    <div class="offer-user-rating">
                                        <div class="star-rating">
                                            <span once-style="{width:(+item.deal.user.rating)+'%'}"></span>
                                        </div>
                                        <div class="user-feedback-count">({{::item.deal.user.feedback_count}})</div>
                                        <div ng-if="item.deal.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                        <div ng-if="item.deal.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="offer-info-others-box">
                            <div class="offer-info-status-box clearfix">
                                <div class="offer-date">{{::item.deal.create_dt|date:"dd.MM.yyyy"}}</div>
                            </div>
                            <div ng-if="item.deal.price_to" class="offer-price"><span>{{::item.deal.price_from|priceFormat}} - {{::item.deal.price_to|priceFormat}} &euro;</span></div>
                            <div ng-if="!item.deal.price_to" class="offer-price"><span>{{::item.deal.price_from|priceFormat}} &euro;</span></div>
                        </div>
                    </div>

                    <ul class="offer-users-list offer-request-list">
                        <li>
                            <a ui-sref="searches.offerDetails({id:item.dealOffer.id})">
                                <div class="offer-users-list-box clearfix">
                                    <div class="deal-comments-title"><?= Yii::t('app', 'Dein Angebot') ?></div>
                                    <div class="deal-comments-box offer-comments">
                                        <p ng-if="item.dealOffer.description">{{::item.dealOffer.description}}</p>
                                        <p ng-if="!item.dealOffer.description" class="no-comments"><?= Yii::t('app', 'Kein Kommentar') ?></p>
                                    </div>
                                </div>
                            </a>

                            <div class="deal-accept-box clearfix">
                                <div class="deal-accept-text">
                                    <?= Yii::t('app', 'Du wurdest dafür bewertet mit') ?>:
                                </div>

                                <div class="deal-accept-rating-box clearfix">
                                    <div class="star-rating">
                                        <span ng-style="{width:(+item.dealOffer.rating)+'%'}"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="deal-accept-box clearfix">
                                <div class="deal-accept-text">
                                    <span ng-if="item.dealOffer.counter_rating"><?= Yii::t('app', 'Du hast') ?> <a ui-sref="userProfile({id: item.deal.user.id})">{{::item.deal.user|userName}}</a> <?= Yii::t('app', 'daf&uuml;r bewertet mit') ?>:</span>
                                    <span ng-if="!item.dealOffer.counter_rating" class="red"><?= Yii::t('app','Noch nicht bewertet') ?></span>
                                </div>
                                <div class="deal-accept-rating-box clearfix">
                                    <div ng-if="item.dealOffer.counter_rating" class="star-rating">
                                        <span ng-style="{width:(+item.dealOffer.counter_rating)+'%'}"></span>
                                    </div>
                                    <a class="offer-link" href="" ng-click="dealsCompletedCtrl.updateCounterUserFeedback(null,item.dealOffer.id);"><?= Yii::t('app', 'Handel bewerten') ?></a>
                                </div>
                            </div>

                        </li>
                    </ul>
                </div>



                <div ng-if="item.type=='offer'" class="offer-box offer-type3" ng-class="{'offer-type-delete': item.deal.status=='DELETED'}">

                    <div class="btn-offer-deletes" ng-if="item.deal.status=='DELETED'">
                        <button class="btn-offer-unlink" ng-click="dealsCompletedCtrl.unlink(item)"><?= Yii::t('app', 'Endgültig löschen') ?></button>
                        <button class="btn-offer-undelete" ng-click="dealsCompletedCtrl.undelete(item)"><?= Yii::t('app', 'Wiederherstellen') ?></button>
                    </div>

                    <div class="offer clearfix">
                        <div class="offer-picture-box">
                            <a ui-sref="offers.details({id:item.deal.id})">
                                <div class="offer-picture">
                                    <img ng-src="{{::item.deal.image}}" alt=""/>
                                </div>
                            </a>
                        </div>

                        <div class="offer-info-box">
                            <div class="offer-info-type"><?= Yii::t('app', 'Du hast folgenden Artikel verkauft') ?></div>
                            <div class="offer-info-title"><h2><a ui-sref="offers.details({id:item.deal.id})">{{::item.deal.title}}</a></h2></div>
                            <ul class="offer-info-category">
                                <li>{{::item.deal.level1Interest}}</li>
                                <li ng-if="item.deal.level2Interest">{{::item.deal.level2Interest}}</li>
                                <li ng-if="item.deal.level3Interests">{{::item.deal.level3Interests}}</li>
                            </ul>
                        </div>

                        <div class="offer-info-others-box">
                            <div class="offer-info-status-box clearfix">
                                <div class="offer-date">{{::item.deal.create_dt|date:"dd.MM.yyyy"}}</div>
                            </div>

                            <div class="offer-price"><span>{{::item.deal.price|priceFormat}} &euro;</span></div>

                            <div ng-if="item.deal.view_bonus>0" class="offer-bonus promotion-bonus">
                                <?= Yii::t('app', 'Werbebonus: ') ?>
                                <span class="bonus-value">{{::item.deal.view_bonus}} <jugl-currency></jugl-currency></span>
                            </div>
                            <div ng-if="item.deal.buy_bonus>0" class="offer-bonus buy-bonus">
                                <?= Yii::t('app', 'Kaufbonus: ') ?>
                                <span class="bonus-value">{{::item.deal.buy_bonus}} <jugl-currency></jugl-currency></span>
                            </div>

                        </div>
                    </div>

                    <ul class="offer-users-list offer-request-list">
                        <li ng-repeat="dealOffer in item.dealOffers">
                            <div class="offer-users-list-box">
                                <div class="offer-request-left">
                                    <div class="deal-offer-text-box clearfix">
                                        <div class="deal-offer-text">
                                            <?= Yii::t('app', 'Wurde angenommen am {{::dealOffer.closed_dt|date:"dd.MM.yyyy"}} von') ?>:
                                        </div>
                                    </div>

                                    <div class="offer-request-user-box clearfix">
                                        <div class="offer-user-box clearfix">
                                            <a ui-sref="userProfile({id: dealOffer.user.id})">
                                                <div class="offer-user-avatar"><img ng-src="{{::dealOffer.user.avatarSmall}}" alt=""/></div>
                                            </a>
                                            <div class="offer-user-name">{{::dealOffer.user|userName}}</div>
                                            <div class="offer-user-rating">
                                                <div class="star-rating">
                                                    <span once-style="{width:(+dealOffer.user.rating)+'%'}"></span>
                                                </div>
                                                <div class="user-feedback-count">({{::dealOffer.user.feedback_count}})</div>
                                                <div ng-if="dealOffer.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                                <div ng-if="dealOffer.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="offer-request-right">
                                    <div class="offer-request-comments-box offer-comments">
                                        <p>{{::dealOffer.description}}</p>
                                    </div>

<!--                                    <div class="offer-request-link-box">-->
<!--                                        <a class="offer-link" href="" ng-click="dealsCompletedCtrl.updateUserFeedback(item.deal.user_feedback_id,null,item.dealOffer.id);">--><?//= Yii::t('app', 'Handel bewerten') ?><!--</a>-->
<!--                                    </div>-->
                                </div>
                            </div>

                            <div class="deal-accept-box clearfix">
                                <div class="deal-accept-text">
                                    <?= Yii::t('app', 'Du wurdest bewertet von') ?> <a ui-sref="userProfile({id: dealOffer.user.id})">{{::dealOffer.user|userName}}</a> <?= Yii::t('app', 'mit') ?>:
                                </div>

                                <div class="deal-accept-rating-box clearfix">
                                    <div class="star-rating">
                                        <span ng-style="{width:(+dealOffer.counter_rating)+'%'}"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="deal-accept-box clearfix">
                                <div class="deal-accept-text">
                                    <span ng-if="dealOffer.rating"><?= Yii::t('app', 'Du hast') ?> <a ui-sref="userProfile({id: dealOffer.user.id})">{{::dealOffer.user|userName}}</a> <?= Yii::t('app', 'daf&uuml;r bewertet mit') ?>:</span>
                                    <span ng-if="!dealOffer.rating" class="red"><?= Yii::t('app','Noch nicht bewertet') ?></span>
                                </div>

                                <div class="deal-accept-rating-box clearfix">
                                    <div ng-if="dealOffer.rating" class="star-rating">
                                        <span ng-style="{width:(+dealOffer.rating)+'%'}"></span>
                                    </div>
                                    <a class="offer-link" href="" ng-click="dealsCompletedCtrl.updateUserFeedback(item.deal.user_feedback_id,null,dealOffer.id);"><?= Yii::t('app', 'Handel bewerten') ?></a>
                                </div>
                            </div>

                        </li>
                    </ul>
                </div>

                <div ng-if="item.type=='offer_request'" class="offer-box offer-type4">
                    <div class="offer clearfix">
                        <div class="offer-picture-box">
                            <a ui-sref="offers.details({id:item.deal.id})">
                                <div class="offer-picture">
                                    <img ng-src="{{::item.deal.image}}" alt=""/>
                                </div>
                            </a>
                        </div>

                        <div class="offer-info-box">
                            <div class="offer-info-type"><?= Yii::t('app', 'Du hast folgenden Artikel gekauft') ?></div>
                            <div class="offer-info-title"><h2><a ui-sref="offers.details({id:item.deal.id})">{{::item.deal.title}}</a></h2></div>
                            <ul class="offer-info-category">
                                <li>{{::item.deal.level1Interest}}</li>
                                <li ng-if="item.deal.level2Interest">{{::item.deal.level2Interest}}</li>
                                <li ng-if="item.deal.level3Interests">{{::item.deal.level3Interests}}</li>
                            </ul>
                            
                            <div class="deal-user-box clearfix">
                                <div class="offer-from-user-text"><?= Yii::t('app', 'von') ?>:</div>
                                <div class="offer-user-box clearfix">
                                    <a ui-sref="userProfile({id: item.deal.user.id})">
                                        <div class="offer-user-avatar"><img ng-src="{{::item.deal.user.avatarSmall}}" alt=""/></div>
                                    </a>
                                    <div class="offer-user-name">{{::item.deal.user|userName}}</div>
                                    <div class="offer-user-rating">
                                        <div class="star-rating">
                                            <span once-style="{width:(+item.deal.user.rating)+'%'}"></span>
                                        </div>
                                        <div class="user-feedback-count">({{::item.deal.user.feedback_count}})</div>
                                        <div ng-if="item.deal.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                        <div ng-if="item.deal.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="offer-info-others-box">
                            <div class="offer-info-status-box clearfix">
                                <div class="offer-date">{{::item.deal.create_dt|date:"dd.MM.yyyy"}}</div>
                            </div>
                            <div ng-if="item.deal.view_bonus>0" class="offer-bonus promotion-bonus">
                                <?= Yii::t('app', 'Werbebonus: ') ?>
                                <span class="bonus-value">{{::item.deal.view_bonus}} <jugl-currency></jugl-currency></span>
                            </div>
                            <div ng-if="item.deal.buy_bonus>0" class="offer-bonus buy-bonus">
                                <?= Yii::t('app', 'Kaufbonus: ') ?>
                                <span class="bonus-value">{{::item.deal.buy_bonus}} <jugl-currency></jugl-currency></span>
                            </div>
                        </div>
                    </div>

                    <ul class="offer-users-list offer-request-list">
                        <li>
                            <div ng-if="item.dealOffer.description" class="offer-users-list-box">
                                <div class="deal-comments-title"><?= Yii::t('app', 'Dein Kommentar') ?>:</div>
                                <div class="deal-comments-box offer-comments">
                                    <p>{{::item.dealOffer.description}}</p>
                                </div>
                            </div>

                            <div class="deal-accept-box clearfix">
                                <div class="deal-accept-text">
                                    <?= Yii::t('app', 'Du wurdest bewertet von') ?> <a ui-sref="userProfile({id: item.deal.user.id})">{{::item.deal.user|userName}}</a> <?= Yii::t('app', 'mit') ?>:
                                </div>

                                <div class="deal-accept-rating-box clearfix">
                                    <div class="star-rating">
                                        <span ng-style="{width:(+item.dealOffer.rating)+'%'}"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="deal-accept-box clearfix">
                                <div class="deal-accept-text">
                                    <span ng-if="item.dealOffer.counter_rating"><?= Yii::t('app', 'Du hast') ?> <a ui-sref="userProfile({id: item.deal.user.id})">{{::item.deal.user|userName}}</a> <?= Yii::t('app', 'daf&uuml;r bewertet mit') ?>:</span>
                                    <span ng-if="!item.dealOffer.counter_rating" class="red"><?= Yii::t('app','Noch nicht bewertet') ?></span>
                                </div>

                                <div class="deal-accept-rating-box clearfix">
                                    <div ng-if="item.dealOffer.counter_rating" class="star-rating">
                                        <span ng-style="{width:(+item.dealOffer.counter_rating)+'%'}"></span>
                                    </div>
                                    <a class="offer-link" href="" ng-click="dealsCompletedCtrl.updateCounterUserFeedback(null,null,item.dealOffer.id);"><?= Yii::t('app', 'Handel bewerten') ?></a>
                                </div>
                            </div>


                        </li>
                    </ul>
                </div>

            </div>

            <div class="bottom-corner"></div>
        </div>
    </div>
</div>