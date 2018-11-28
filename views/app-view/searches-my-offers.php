<div class="searches">
    <div class="container">
        <div ng-click="showInfoPopup('view-searches-my-offers')" ng-class="{'blink':isOneShowInfoPopup('view-searches-my-offers')}" class="info-popup-btn"></div>
        <div class="welcome-text">
            <h2><?=Yii::t('app','Was habe ich anderen angeboten / vermittelt?')?></h2>
        </div>


        <div class="offers-container">
            <div class="offers-filter-container clearfix">
                <div class="offers-filter-box">
                    <div class="field-box-select" dropdown-toggle select-click>
                        <select ng-model="filter.status" selectpicker>
                            <option value=""><?= Yii::t('app', 'Alle anzeigen') ?></option>
                            <option value="ACCEPTED">Angenommene Angebote</option>
                            <option value="REJECTED">Abgelehnte Angebote</option>
                            <option value="AWAITING">Ausstehend</option>
                        </select>
                    </div>
                </div>
            </div>


            <div class="searches-my-offers-box" ng-repeat="item in results.items">

                <div class="searches-my-offer-item-head"><?= Yii::t('app', 'Du hast auf diesen Suchauftrag...') ?></div>

                <div class="searches-my-offer-item clearfix">
                    <div class="offer-picture-box">
                        <a ui-sref="searches.details({id:item.id})">
                            <div class="offer-picture">
                                <img ng-src="{{::item.image}}" alt=""/>
                            </div>
                        </a>
                    </div>

                    <div class="searches-my-offer-info">
                        <div class="found-title-info"><?= Yii::t('app', 'ICH SUCHE:') ?></div>
                        <div class="offer-info-title"><h2><a ui-sref="searches.details({id:item.id})">{{::item.title}}</a></h2></div>
                        <div class="found-text">
                            <p ng-bind-html="item.description|linky"></p>
                        </div>
                        <ul class="offer-info-category">
                            <li>{{::item.level1Interest}}</li>
                            <li ng-if="item.level2Interest">{{::item.level2Interest}}</li>
                            <li ng-if="item.level3Interests">{{::item.level3Interests}}</li>
                        </ul>
                    </div>

                    <div class="searches-my-offer-info">
                        <div ng-if="item.price_to" class="found-price">{{::item.price_from|priceFormat}} - {{::item.price_to|priceFormat}} &euro;</div>
                        <div ng-if="!item.price_to"class="found-price">{{::item.price_from|priceFormat}} &euro;</div>

                        <div once-if="item.bonus" class="found-offer-param"><?= Yii::t('app', 'Für die Vermittlung eines passenden Angebots zahle ich:') ?> <span class="found-offer-value">{{::item.bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
                        <div class="found-place">{{::item.zip}} {{::item.city}}</div>
                        <div class="found-user-box">
                            <a ui-sref="userProfile({id: item.user.id})"><div class="found-user-avatar"><img ng-src="{{::item.user.avatarSmall}}" alt=""/></div></a>
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

                    <div class="searches-my-offer-info-two clearfix">
                        <div class="found-date">{{::item.create_dt|date:"dd.MM.yyyy"}}</div>
                        <div class="found-relevance relevance">
                            <?= Yii::t('app', 'Relevance'); ?>
                            <div class="relevance-percent">{{::item.relevancy}}%</div>
                        </div>
                    </div>
                </div>



                <div ng-if="item.offers.length>0" class="searches-my-offers-list-title"><?= Yii::t('app', '...folgende Angebote abgegeben:') ?></div>
                <ul ng-if="item.offers.length>0" class="searches-my-offers-list-box clearfix">
                    <li ng-repeat="offer in item.offers">
                        <div class="item-price-offer-box fix-width-box">
                            <div>
                                <?= Yii::t('app', 'Angebotspreis: ') ?>
                                <span ng-if="offer.price_to">{{::offer.price_from|priceFormat}} - {{::offer.price_to|priceFormat}} &euro;</span>
                                <span ng-if="!offer.price_to">{{::offer.price_from|priceFormat}} &euro;</span>
                            </div>
                        </div>
                        <div class="item-description-offer-box">
                            <p ng-bind-html="offer.description|linky"></p>
                        </div>
                        <div class="item-details-offer-box fix-width-box">
                            <div class="item-details-offer-status">
                                <div class="status-orange" ng-if="offer.status=='NEW' || offer.status=='CONTACTED' "><?= Yii::t('app', 'Ausstehend') ?><span></span></div>
                                <div class="status-green" ng-if="offer.status=='ACCEPTED'">
                                    <?= Yii::t('app', 'Аngenommen') ?><br/><?= Yii::t('app', 'am') ?> {{::offer.closed_dt|date:"dd.MM.yyyy H:mm:ss"}}<br/>
                                    <span></span>
                                </div>
                                <div class="status-red" ng-if="offer.status=='DELETED' || offer.status=='REJECTED' ">
                                    <?= Yii::t('app', 'Abgelehnt') ?><br/><?= Yii::t('app', 'am') ?> {{::offer.closed_dt|date:"dd.MM.yyyy H:mm:ss"}}<br/>
                                    <span></span>
                                </div>
                            </div>
                            <a ui-sref="searches.offerDetails({id:offer.id})" ><?= Yii::t('app', 'Details') ?></a>
                        </div>
                    </li>
                </ul>



            </div>




            <div class="bottom-corner"></div>
        </div>


    </div>
</div>
