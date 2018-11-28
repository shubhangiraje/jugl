<div class="searches-offer-details">
    <div class="container">
        <div class="offered-details">
            <div class="box-details-info clearfix">
                <div class="details-column">
                    <div class="details-info">
                        <h2><?= Yii::t('app', 'Details'); ?></h2>
                        <div class="details-info-box">
                            <ul class="list-details-info">
                                <li ng-repeat="param in searchRequestOffer.paramValues" once-if="param.value">
                                    <span ng-class="{'icon-checked':param.match,'icon-no-checked':!param.match}"></span>
                                    <span class="details-param">{{::param.title}}</span>
                                    <span class="details-value">{{::param.value}}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="bottom-corner"></div>
                    </div>
                </div>
                <div class="details-column">
                    <div class="details-info-others">
                        <div class="offered-providers-box">
                            <h2><?= Yii::t('app', 'Anbieter:') ?></h2>
                            <div class="offer-user-box clearfix">
                                <a ui-sref="userProfile({id: searchRequestOffer.user.id})">
                                    <div class="offer-user-avatar"><img ng-src="{{::searchRequestOffer.user.avatarSmall}}" alt=""/></div>
                                </a>
                                <div class="offer-user-name">{{::searchRequestOffer.user|userName}}</div>
                                <div class="offer-user-rating">
                                    <div class="star-rating">
                                        <span once-style="{width:(+searchRequestOffer.user.rating)+'%'}"></span>
                                    </div>
                                    <div class="user-feedback-count">({{::searchRequestOffer.user.feedback_count}})</div>
                                    <div ng-if="searchRequestOffer.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                    <div ng-if="searchRequestOffer.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                </div>
                            </div>
                        </div>

                        <div once-if="searchRequestOffer.images.length>0" class="offered-photo-box">
                            <h2><?= Yii::t('app', 'Fotos:') ?></h2>
                            <div class="box-details-carousel">
                                <div class="details-gallery-container">
                                    <ul carousel-gallery class="list-details-carousel">
                                        <li ng-repeat="image in searchRequestOffer.images"><a rel="group" href="{{::image.fancybox}}" fancybox><img ng-src="{{::image.small}}" /></a></li>
                                    </ul>
                                </div>
                                <div carousel="{type:'horizontal',move:-146,container:'.details-gallery-container',time:300}" class="scroll-left carousel-btn"></div>
                                <div carousel="{type:'horizontal',move:146,container:'.details-gallery-container',time:300}" class="scroll-right carousel-btn"></div>
                            </div>
                        </div>

                        <div class="offered-price-box">
                            <h2><?= Yii::t('app', 'Preis:') ?></h2>
                            <div ng-if="searchRequestOffer.price_to" class="offer-price">{{::searchRequestOffer.price_from|priceFormat}} - {{::searchRequestOffer.price_to|priceFormat}} &euro;</div>
                            <div ng-if="!searchRequestOffer.price_to" class="offer-price">{{::searchRequestOffer.price_from|priceFormat}} &euro;</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>





        <div class="box-details-description">
            <h2><?= Yii::t('app', 'Nachricht'); ?></h2>
            <div class="details-description">
                <p ng-bind-html="searchRequestOffer.description|linky"></p>
            </div>
            <div class="bottom-corner"></div>
        </div>

        <div class="box-details-description box-details-once-accept">
            <h2><?= Yii::t('app', 'Angebotsdetails (wird eingeblendet sobald Du das Angebot akzeptierst)'); ?></h2>
            <div class="details-description clearfix">
                <div class="box-details-once-accept-left">
                    <p ng-class="{blurDetails:searchRequestOffer.blurDetails}" ng-bind-html="searchRequestOffer.details|linky"></p>
                </div>

                <div class="box-details-once-accept-right">
                    <div once-if="searchRequestOffer.details_images.length>0" class="offered-photo-box">
                        <div class="box-details-carousel">
                            <div class="details-gallery-container">
                                <ul carousel-gallery class="list-details-carousel">
                                    <li ng-repeat="image in searchRequestOffer.details_images"><a rel="group2" href="{{::image.fancybox}}" fancybox><img ng-src="{{::image.small}}" /></a></li>
                                </ul>
                            </div>
                            <div carousel="{type:'horizontal',move:-146,container:'.details-gallery-container',time:300}" class="scroll-left carousel-btn"></div>
                            <div carousel="{type:'horizontal',move:146,container:'.details-gallery-container',time:300}" class="scroll-right carousel-btn"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bottom-corner"></div>
        </div>

        <div class="offered-btn-box" ng-if="(searchRequestOffer.status == 'NEW' || searchRequestOffer.status == 'CONTACTED')  && searchRequestOffer.forMe">
            <button class="write-message-btn btn" ng-click="searchRequestOfferDetailsCtrl.contact()"><?= Yii::t('app', 'Nachricht schreiben') ?></button>
            <button class="accept-rate-btn btn" ng-click="searchRequestOfferDetailsCtrl.accept()" ng-disabled="searchRequestOffer.saving" ><?= Yii::t('app', 'Akzeptieren') ?></button>
            <button class="decline-btn btn" ng-click="searchRequestOfferDetailsCtrl.reject()"><?= Yii::t('app', 'Ablehnen') ?></button>
        </div>

        <div class="offered-btn-box" ng-if="searchRequestOffer.status == 'ACCEPTED' && searchRequestOffer.forMe">
            <button class="accept-rate-btn btn" ng-click="searchRequestOfferDetailsCtrl.feedback();"><?= Yii::t('app', 'Bewerten') ?></button>
        </div>


    </div>
</div>
