<div class="searches-my">
    <div class="container">
        <div class="offers-container searches-offers-list">

            <div class="offer-box">
                <div class="offer clearfix">
                    <div class="offer-picture-box">
                        <a ui-sref="searches.details({id:searchRequest.id})">
                            <div class="offer-picture">
                                <img ng-src="{{::searchRequest.image}}" alt=""/>
                            </div>
                        </a>
                    </div>

                    <div class="offer-info-box">
                        <div class="offer-info-title"><h2><a ui-sref="searches.details({id:searchRequest.id})">{{::searchRequest.title}}</a></h2></div>
                        <ul class="offer-info-category">
                            <li>{{::searchRequest.level1Interest}}</li>
                            <li ng-if="searchRequest.level2Interest">{{::searchRequest.level2Interest}}</li>
                            <li ng-if="searchRequest.level3Interests">{{::searchRequest.level3Interests}}</li>
                        </ul>
                    </div>

                    <div class="offer-info-others-box">
                        <div class="offer-info-status-box clearfix">
                            <div class="offer-date">{{::searchRequest.create_dt|date:"dd.MM.yyyy H:mm:ss"}}</div>
                            <div class="offer-count">
                                <span>{{::searchRequest.searchRequestOffers.length}}</span><?= Yii::t('app', 'Angebote'); ?>
                            </div>
                        </div>
                        <div ng-if="searchRequest.price_to" class="offer-price"><span>{{::searchRequest.price_from|priceFormat}} - {{::searchRequest.price_to|priceFormat}} &euro;</span></div>
                        <div ng-if="!searchRequest.price_to" class="offer-price"><span>{{::searchRequest.price_from|priceFormat}} &euro;</span></div>
                        <div once-if="item.bonus" class="offer-bonus">
                            <span class="bonus-value">{{::searchRequest.bonus|priceFormat}} <jugl-currency></jugl-currency></span>
                        </div>
                    </div>
                </div>


                <ul class="offer-users-list offer-request-list">
                    <li ng-repeat="offer in searchRequest.searchRequestOffers" class="clearfix">
                        <div class="offer-users-list-box">
                            <div class="offer-request-right">
                                <div class="offer-request-comments-box offer-comments blurDetails">
                                    <p ng-bind-html="offer.description|linky"></p>
                                </div>

                                <div class="offer-request-price-box">
                                    <div ng-if="offer.price_to" class="offer-price"><span>{{::offer.price_from|priceFormat}} - {{::offer.price_to|priceFormat}} &euro;</span></div>
                                    <div ng-if="!offer.price_to" class="offer-price"><span>{{::offer.price_from|priceFormat}} &euro;</span></div>
                                </div>

                                <div class="offer-request-link-box">
                                    <div ng-if="offer.status=='ACCEPTED'" class="offer-request-status contacted"><?=Yii::t('app','SEARCH_REQUEST_OFFER_STATUS_ACCEPTED')?></div>
                                    <div ng-if="offer.status=='CONTACTED'" class="offer-request-status contacted"><?=Yii::t('app','SEARCH_REQUEST_OFFER_STATUS_CONTACTED')?></div>
                                    <div ng-if="offer.status=='REJECTED'" class="offer-request-status rejected"><?=Yii::t('app','SEARCH_REQUEST_OFFER_STATUS_REJECTED')?></div>
                                </div>

                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="bottom-corner"></div>
        </div>

    </div>
</div>