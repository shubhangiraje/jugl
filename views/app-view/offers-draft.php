<div class="container">

    <div class="welcome-text">
        <h2><?=Yii::t('app','Entwürfe')?></h2>
    </div>

    <div class="offers-container offers-draft-container">

        <div ng-if="results.items.length == 0" class="result-empty-text">
            <?= Yii::t('app', 'Keine Entwürfe vorhanden') ?>
        </div>

        <div scroll-load="offerDraftCtrl.loadMore" scroll-load-visible="0.7" scroll-load-has-more="results.hasMore">

            <div class="offer-box" ng-repeat="item in results.items" id="offer-{{item.id}}">
                <div class="offer clearfix">
                    <div class="offer-picture-box">
                        <a ui-sref="offers.draft-update({id:item.id})">
                            <div class="offer-picture">
                                <img ng-src="{{::item.image}}" alt=""/>
                            </div>
                        </a>
                    </div>

                    <div class="offer-info-box">
                        <div class="found-offer-type" ng-click="showInfoPopup('info-offer-type')">
                            <span ng-if="item.type == 'AUCTION'"><?= Yii::t('app', 'Bieterverfahren') ?></span>
                            <span ng-if="item.type == 'AD'"><?= Yii::t('app', 'Keine Kaufmöglichkeit') ?></span>
                            <span ng-if="item.type == 'AUTOSELL'"><?= Yii::t('app', 'Sofortkauf') ?></span>
                        </div>
                        <div class="offer-info-title">
                            <h2>
                                <a ui-sref="offers.draft-update({id:item.id})">
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
                            <div class="offer-date">{{::item.create_dt|date:"dd.MM.yyyy"}}</div>
                        </div>

                        <div ng-if="item.type=='AUTOSELL' && item.price" class="offer-price"><b><?= Yii::t('app', 'Preis') ?>: </b><span>{{::item.price|priceFormat}} &euro;</span></div>
                        <div ng-if="item.type=='AUCTION' && item.price" class="offer-price"><b><?= Yii::t('app', 'Preisvorstellung') ?>: </b><span>{{::item.price|priceFormat}} &euro;</span></div>

                        <div ng-if="item.view_bonus" class="offer-bonus promotion-bonus">
                            <?= Yii::t('app', 'Werbebonus') ?>:
                            <span class="bonus-value">{{::item.view_bonus|priceFormat}} <jugl-currency></jugl-currency></span>
                        </div>
                        <div ng-if="item.buy_bonus" class="offer-bonus buy-bonus">
                            <?= Yii::t('app', 'Kaufbonus') ?>:
                            <span class="bonus-value">{{::item.buy_bonus|priceFormat}} <jugl-currency></jugl-currency></span>
                        </div>

                        <div ng-if="item.view_bonus_total" class="offer-bonus">
                            <?= Yii::t('app', 'Budget') ?>:
                            <span class="bonus-value">{{::item.view_bonus_total|priceFormat}} <jugl-currency></jugl-currency></span>
                        </div>

                        <div ng-if="item.view_bonus_used" class="offer-bonus">
                            <?= Yii::t('app', 'Budget verbraucht') ?>:
                            <span class="bonus-value">{{::item.view_bonus_used|priceFormat}} <jugl-currency></jugl-currency></span>
                        </div>

                        <div ng-if="item.show_amount == 1" class="offer-bonus">
                            <?= Yii::t('app', 'St&uuml;ckzahl') ?>:
                            <b>{{::item.amount}}</b>
                        </div>
                        <div ng-if="item.active_till" class="offer-bonus">
                            <?= Yii::t('app', 'Aktiv bis') ?>:
                            <b>{{::item.active_till|date:"dd.MM.yyyy"}}</b>
                        </div>
                        <div ng-if="item.delivery_cost>0" class="offer-bonus">
                            <?= Yii::t('app', 'Versandkosten') ?>:
                            <b>{{::item.delivery_cost|priceFormat}} &euro;</b>
                        </div>

                        <div ng-if="item.bestBet>0" class="offer-bonus">
                            <b><?= Yii::t('app', 'Aktuelles Höchstgebot') ?>: </b>
                            <span class="bonus-value">{{::item.bestBet|priceFormat}} &euro;</span>
                        </div>

                    </div>
                    <div class="btn-del-offer">
                        <button ng-click="offerDraftCtrl.delete(item.id)"></button>
                    </div>

                </div>

            </div>
        </div>
        <div class="bottom-corner"></div>
    </div>
</div>
