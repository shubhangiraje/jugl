<div class="searches-details">
    <div class="container">

        <div class="box-details-select-category clearfix">
            <ul class="list-details-select-category">
                <li>{{::searchRequest.level1Interest}}</li>
                <li ng-if="searchRequest.level2Interest">{{::searchRequest.level2Interest}}</li>
                <li ng-if="searchRequest.level3Interests">{{::searchRequest.level3Interests}}</li>
            </ul>
            <a class="link-back" ui-sref="searches.search"><?= Yii::t('app', 'Zurück zur Übersicht'); ?></a>
        </div>

        <div class="box-title-details">
            <h2>{{::searchRequest.title}}</h2>
            <ul class="list-details-price">
                <li ng-if="searchRequest.price_to"><?= Yii::t('app', 'Preise'); ?>: <span>{{::searchRequest.price_from|priceFormat}} - {{::searchRequest.price_to|priceFormat}} &euro;</span></li>
                <li ng-if="!searchRequest.price_to"><?= Yii::t('app', 'Preise'); ?>: <span>{{::searchRequest.price_from|priceFormat}} &euro;</span></li>

                <li once-if="searchRequest.bonus" class="bonus"><?= Yii::t('app', 'Vermittlungsbonus'); ?>: <span>{{::searchRequest.bonus|priceFormat}} <jugl-currency></jugl-currency></span></li>
            </ul>
        </div>

        <div class="box-details-info clearfix">

            <div class="details-column">
                <div class="details-gallery">
                    <div class="box-details-gallery">
                        <div class="box-preview-details-image">
                            <a ng-if="searchRequest.bigImages.length>0" href="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" fancybox fancybox-data="searchRequest.fancyboxImages" fancybox-force-init="true">
                                <img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" />
                            </a>
                            <img ng-if="!searchRequest.bigImages" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" />
                        </div>
                        <div class="box-details-carousel">
                            <div class="details-gallery-container">
                                <ul carousel-gallery carousel-gallery-change-image-callback="searchRequestDetailsCtrl.activeImageChanged(idx)" class="list-details-carousel">
                                    <li ng-repeat="image in searchRequest.images"><img data-big-src="{{::image}}" ng-src="{{::image}}" data-id="{{$index}}" /></li>
                                </ul>
                            </div>
                            <div carousel="{type:'horizontal',move:-146,container:'.details-gallery-container',time:300}" class="scroll-left carousel-btn"></div>
                            <div carousel="{type:'horizontal',move:146,container:'.details-gallery-container',time:300}" class="scroll-right carousel-btn"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="details-column">
                <div class="details-info">
                    <h2><?= Yii::t('app', 'Details'); ?></h2>
                    <div class="details-info-box">
                        <div class="details-favorite-box clearfix">
                            <div class="favorite" ng-if="searchRequest.favorite"  ng-class="{'favorite-true': searchRequest.favorite}"><?= Yii::t('app', 'Gemerkt'); ?></div>
                            <div class="favorite" ng-if="!searchRequest.favorite" ng-click="searchRequestDetailsCtrl.addFavorite(searchRequest.id)" ng-class="{'favorite-false': !searchRequest.favorite}"><?= Yii::t('app', 'Merken'); ?></div>
                        </div>

                        <ul class="list-details-info">
                            <li ng-repeat="pv in searchRequest.paramValues" once-if="pv.value">
                                <span class="details-param">{{::pv.title}}:</span>
                                <span class="details-value">{{::pv.value}}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="bottom-corner"></div>
                </div>
            </div>

        </div>

        <div class="box-details-description">
            <h2><?= Yii::t('app', 'Beschreibung'); ?></h2>
            <div class="details-description">
                <p ng-if="searchRequest.search_request_type==='STANDART'"ng-bind-html="searchRequest.description|linky:'_blank'"></p>
                <p ng-if="searchRequest.search_request_type==='EXTERNAL_AD'"ng-bind-html="searchRequest.description"></p>
            </div>
            <div class="bottom-corner"></div>
        </div>

        <div class="box-details-description details-place">
            <h2><?= Yii::t('app', 'Ort'); ?></h2>
            <div class="details-description clearfix">
                <div class="details-places">
                    <ul class="list-details-place">
                        <li><?= Yii::t('app', 'PLZ') ?>:<span>{{::searchRequest.zip}}</span></li>
                        <li><?= Yii::t('app', 'Ort') ?>:<span>{{::searchRequest.city}}</span></li>
                        <li><?= Yii::t('app', 'Straße/Nr.') ?>:<span>{{::searchRequest.address}}</span></li>
                    </ul>
                    <div class="details-country"><?= Yii::t('app', 'Land') ?>:<span>{{::searchRequest.country|translate}}</span></div>
                </div>
                <div class="details-user">
                    <div class="offer-user-box clearfix">
                        <a ui-sref="userProfile({id: searchRequest.user.id})">
                            <div class="offer-user-avatar"><img ng-src="{{::searchRequest.user.avatarSmall}}" alt=""/></div>
                        </a>
                        <div class="offer-user-name">{{::searchRequest.user|userName}}</div>
                        <div class="offer-user-rating">
                            <div class="star-rating">
                                <span once-style="{width:(+searchRequest.user.rating)+'%'}"></span>
                            </div>
                            <div class="user-feedback-count">({{::searchRequest.user.feedback_count}})</div>
                            <div ng-if="searchRequest.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                            <div ng-if="searchRequest.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom-corner"></div>
        </div>


        <div class="box-details-description">
            <h2><?= Yii::t('app', 'Fragen zum Auftrag / zum Produkt'); ?></h2>
            <div class="details-description">
                <button class="btn btn-submit" ng-click="searchRequestDetailsCtrl.addComment()"><?= Yii::t('app','Frage hinzufügen') ?></button>

                <ul ng-if="comments.items.length>0" class="feedback-list" scroll-load="searchRequestDetailsCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="comments.hasMore">
                    <li ng-repeat="itemComment in comments.items">
                        <div class="feedback-user">
                            <div class="offer-user-box clearfix">
                                <a ui-sref="userProfile({id: itemComment.user.id})">
                                    <div class="offer-user-avatar"><img ng-src="{{::itemComment.user.avatarSmall}}" alt=""/></div>
                                </a>
                                <div class="offer-user-name">{{::itemComment.user|userName}}</div>
                                <div class="offer-user-rating">
                                    <div class="star-rating">
                                        <span once-style="{width:(+itemComment.user.rating)+'%'}"></span>
                                    </div>
                                    <div class="user-feedback-count">({{::itemComment.user.feedback_count}})</div>
                                    <div ng-if="itemComment.user.packet=='VIP'" class="user-packet">&nbsp;</div>
                                    <div ng-if="itemComment.user.packet=='VIP_PLUS'" class="user-packet-vip-plus">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                        <div class="feedback-text">
                            <p ng-bind-html="itemComment.comment|linky:'_blank'"></p>

                            <ul ng-if="itemComment.response" class="feedback-response-box">
                                <li>
                                    <div class="feedback-user">
                                        <div class="offer-user-box clearfix">
                                            <a ui-sref="userProfile({id: searchRequest.user.id})">
                                                <div class="offer-user-avatar"><img ng-src="{{::searchRequest.user.avatarSmall}}" alt=""/></div>
                                            </a>
                                            <div class="offer-user-name">{{::searchRequest.user|userName}}</div>
                                            <div class="offer-user-rating">
                                                <div class="feedback-response-dt">{{itemComment.response_dt|date:"dd.MM.yyyy HH:mm"}}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="feedback-text">
                                        <p ng-bind-html="itemComment.response|linky:'_blank'"></p>
                                    </div>
                                </li>
                            </ul>
                            <br/>
                            <button ng-if="searchRequest.user.id==status.id" type="button" class="btn btn-submit" ng-click="searchRequestDetailsCtrl.commentResponse(itemComment)"><?= Yii::t('app','Antworten') ?></button>
                        </div>
                        <div class="feedback-date-and-rating clearfix">
                            <div class="feedback-date">{{::itemComment.create_dt|date:"dd.MM.yyyy HH:mm"}}</div>
                        </div>
                    </li>
                </ul>

            </div>

            <div class="bottom-corner"></div>

        </div>



        <div class="show-other-searches">
            <a ui-sref="searches.searchByUser({id:searchRequest.user.id})"><?= Yii::t('app', 'Weitere Anzeigen des Anbieters anzeigen') ?></a>
        </div>

        <div class="details-button-box" ng-if="status.id != searchRequest.user.id && searchRequest.status == 'ACTIVE' && searchRequest.search_request_type != 'EXTERNAL_AD'">
            <a ui-sref="searches.addOffer({id:searchRequest.id})"><?= Yii::t('app', 'Anbieten') ?></a>
            <a ng-if="!searchRequest.spamReported" href="" ng-click="spamReport({search_request_id:searchRequest.id})"><?= Yii::t('app', 'Spam melden') ?></a>
            <a ng-if="searchRequest.spamReported" href="" ng-click=""><?= Yii::t('app', 'Spam gemeldet') ?></a>
        </div>

    </div>
</div>
