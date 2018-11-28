
<div class="container">

    <div class="welcome-text">
        <h2><?=Yii::t('app','Entwürfe')?></h2>
    </div>

    <div class="offers-container" scroll-load="searchRequestDraftCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="results.hasMore">

        <div ng-if="results.items.length == 0" class="result-empty-text">
            <?= Yii::t('app', 'Keine Entwürfe vorhanden') ?>
        </div>

        <div class="offer-box" ng-repeat="item in results.items">
            <div class="offer clearfix">
                <div class="offer-picture-box">
                    <a ui-sref="searches.draft-update({id:item.id})">
                        <div class="offer-picture">
                            <img ng-src="{{::item.image}}" alt=""/>
                        </div>
                    </a>
                </div>

                <div class="offer-info-box">
                    <div class="offer-info-title">
                        <h2>
                            <a ui-sref="searches.draft-update({id:item.id})">
                                <span ng-if="!item.title">(<?= Yii::t('app','Entwurf'); ?>)</span>
                                <span ng-if="item.title">{{::item.title}}</span>
                            </a>
                        </h2>
                    </div>
                    <ul class="offer-info-category">
                        <li>{{::item.level1Interest}}</li>
                        <li ng-if="item.level2Interest">{{::item.level2Interest}}</li>
                        <li ng-if="item.level3Interests">{{::item.level3Interests}}</li>
                    </ul>
                </div>

                <div class="offer-info-others-box">
                    <div class="offer-info-status-box clearfix">
                        <div class="offer-date">{{::item.create_dt|date:"dd.MM.yyyy H:mm:ss"}}</div>
                    </div>
                    <div ng-if="item.price_from && item.price_to" class="offer-price"><span>{{::item.price_from|priceFormat}} - {{::item.price_to|priceFormat}} &euro;</span></div>
                    <div ng-if="item.price_from && !item.price_to" class="offer-price"><span>{{::item.price_from|priceFormat}} &euro;</span></div>
                    <div ng-if="item.bonus" class="offer-bonus">
                        <span class="bonus-value">{{::item.bonus|priceFormat}} <jugl-currency></jugl-currency></span>
                    </div>
                </div>

                <div class="btn-del-offer">
                    <button ng-click="searchRequestDraftCtrl.delete(item.id)"></button>
                </div>

            </div>

        </div>

        <div class="bottom-corner"></div>
    </div>

</div>
