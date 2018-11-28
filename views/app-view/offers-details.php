<?php use \app\models\Offer; ?>
<div class="offers-details">
    <div class="container">

        <div ng-if="!offer.preview" class="text-center">
            <div class="offer-details-bonus-views">
                <?= Yii::t('app','Views') ?>: <span>{{offer.count_offer_view}}</span>
            </div>
        </div>

        <div class="offer-details-type" ng-if="offer.type == 'AUCTION'"><span><?= Yii::t('app', 'Bieterverfahren') ?></span></div>
        <div class="offer-details-type" ng-if="offer.type == 'AD'"><span><?= Yii::t('app', 'Keine Kaufmöglichkeit') ?></span></div>
        <div class="offer-details-type" ng-if="offer.type == 'AUTOSELL'"><span><?= Yii::t('app', 'Sofortkauf') ?></span></div>

        <div class="welcome-text">
            <h2>{{::offer.title}}</h2>
            <ul class="found-category">
                <li>{{::offer.level1Interest}}</li>
                <li ng-if="offer.level2Interest">{{::offer.level2Interest}}</li>
                <li ng-if="offer.level3Interests">{{::offer.level3Interests}}</li>
            </ul>
        </div>

        <div class="offers-details-title-box clearfix">
            <div class="offers-details-title-user">
                <div class="offer-user-box">
                    <a ui-sref="userProfile({id: offer.user.id})">
                        <div class="offer-user-avatar"><img ng-src="{{::offer.user.avatarSmall}}" alt=""/></div>
                    </a>
                    <div class="offer-user-name">{{::offer.user|userName}} <div ng-click="updateCountry(offer.user.id,offer.user)"  id="{{::offer.user.flag}}" class="flag flag-32 flag-{{offer.user.flag}}"></div></div>
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

            <div class="offers-details-title-info">
                <div ng-if="offer.type == 'AUTOSELL'" class="offers-details-price"><?= Yii::t('app', 'Preis') ?>: <span>{{::offer.price|priceFormat}} &euro;</span></div>
                <div ng-if="offer.type == 'AUCTION'" class="offers-details-price"><?= Yii::t('app', 'Preisvorstellung') ?>: <span>{{::offer.price|priceFormat}} &euro;</span></div>
                <div class="offers-details-place">{{::offer.zip}} {{::offer.city}}</div>
                <div class="offers-details-place">{{::offer.country|translate}}</div>
            </div>
            <div once-if="offer.view_bonus" class="offers-details-promotion-bonus"><?= Yii::t('app', 'Werbebonus: ') ?><span>{{::offer.view_bonus|priceFormat}} <jugl-currency></jugl-currency></span></div>
            <div  ng-if="offer.type!='<?=Offer::TYPE_AD?>'" once-if="offer.buy_bonus" class="offers-details-buy-bonus"><?= Yii::t('app', 'Kaufbonus: ') ?><span>{{::offer.buy_bonus|priceFormat}}<jugl-currency></jugl-currency></span></div>

            <div class="offers-details-title-others">
                <div class="offers-details-date">{{::offer.create_dt|date:"dd.MM.yyyy"}}</div>
                <div ng-if="!offer.preview" class="found-relevance relevance">
                    <?= Yii::t('app', 'Relevance'); ?>
                    <div class="relevance-percent">{{::offer.relevancy}}%</div>
                </div>
            </div>

        </div>

        <div class="box-details-info clearfix">

            <div class="details-column">
                <div class="details-gallery">
                    <div class="box-details-gallery">
                        <div class="box-preview-details-image">
                            <a ng-if="offer.bigImages.length>0" href="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" fancybox fancybox-data="offer.fancyboxImages" fancybox-force-init="true">
                                <img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" />
                            </a>
                            <img ng-if="!offer.bigImages" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" />
                        </div>
                        <div class="box-details-carousel">
                            <div class="details-gallery-container">
                                <ul carousel-gallery carousel-gallery-change-image-callback="offer.preview ?offerPreviewCtrl.activeImageChanged(idx) : offerDetailsCtrl.activeImageChanged(idx)" class="list-details-carousel">
                                    <li ng-repeat="image in offer.images"><img data-big-src="{{::image}}" ng-src="{{::image}}" data-id="{{$index}}"/></li>
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
                        <div ng-if="!offer.preview" class="details-favorite-box clearfix">
                            <div class="favorite" ng-if="offer.favorite"  ng-class="{'favorite-true': offer.favorite}"><?= Yii::t('app', 'Gemerkt'); ?></div>
                            <div class="favorite" ng-if="!offer.favorite" ng-click="offerDetailsCtrl.addFavorite(offer.id)" ng-class="{'favorite-false': !offer.favorite}"><?= Yii::t('app', 'Merken'); ?></div>
                        </div>

                        <div ng-if="offer.show_amount == 1 ||offer.delivery_days>0 || offer.delivery_cost>0" class="details-params-box">
                            <div ng-if="offer.show_amount == 1" class="details-params-param"><?= Yii::t('app', 'St&uuml;ckzahl') ?>: <b><?= Yii::t('app', 'noch {amount} verf&uuml;gbar', ['amount' => '{{offer.amount}}' ]) ?></b></div>
                            <div ng-if="offer.delivery_days>0" class="details-params-param"><?= Yii::t('app', 'Lieferzeit') ?>: <b>{{::offer.delivery_days}} <?= Yii::t('app', 'Tage') ?></b></div>
                            <div ng-if="offer.delivery_cost>0" class="details-params-param"><?= Yii::t('app', 'Versandkosten') ?>: <b>{{::offer.delivery_cost|priceFormat}} &euro; </b><span>(<?= Yii::t('app', 'innerhalb von') ?> {{offer.country|translate}})</span> </div>
                        </div>

                        <div ng-if="offer.type!='AD'" class="details-params-box">
                            <h3><?= Yii::t('app', 'Zahlungarten') ?>:</h3>
                            <ul class="details-payment-methods-list">
                                <li><span ng-class="{'icon-checked':offer.pay_allow_bank,'icon-no-checked':!offer.pay_allow_bank}"></span><?= Yii::t('app', 'Zahlung per Bank&uuml;berweisung') ?></li>
                                <li><span ng-class="{'icon-checked':offer.pay_allow_paypal,'icon-no-checked':!offer.pay_allow_paypal}"></span><?= Yii::t('app', 'Zahlung per Paypal') ?></li>
                                <li><span ng-class="{'icon-checked':offer.pay_allow_jugl,'icon-no-checked':!offer.pay_allow_jugl}"></span><?= Yii::t('app', 'Zahlung mit Jugls') ?></li>
                                <li><span ng-class="{'icon-checked':offer.pay_allow_pod,'icon-no-checked':!offer.pay_allow_pod}"></span><?= Yii::t('app', 'Barzahlung bei Abholung') ?></li>
                            </ul>
                        </div>

                        <div class="details-params-box">
                            <div class="details-params-param"><?= Yii::t('app', 'Aktiv bis') ?>: <b>{{::offer.active_till|date:"dd.MM.yyyy"}}</b></div>
                        </div>

                        <div class="details-params-box">
                            <div class="details-params-param"><?= Yii::t('app', 'Typ der Werbung') ?>:
                                <b ng-if="offer.type == 'AUCTION'"><?= Yii::t('app', 'Bieterverfahren') ?></b>
                                <b ng-if="offer.type == 'AD'"><?= Yii::t('app', 'Keine Kaufmöglichkeit') ?></b>
                                <b ng-if="offer.type == 'AUTOSELL'"><?= Yii::t('app', 'Sofortkauf') ?></b>
                            </div>
                        </div>

                        <div class="details-params-box">
                            <div class="details-params-param"><?= Yii::t('app', 'Beschreibung') ?>: <div><b class="description-text" ng-bind-html="offer.description|linky"></b></div></div>
                        </div>

                        <div ng-if="offer.paramValues.length>0" class="details-params-box">
                            <div ng-repeat="pv in offer.paramValues" once-if="pv.value" class="details-params-param">
                                {{::pv.title}}: <b>{{::pv.value}}</b>
                            </div>
                        </div>

                        <div ng-if="offer.comment" class="details-params-box">
                            <div class="details-params-param"><?= Yii::t('app', 'Kommentar') ?>: <div><b class="description-text" ng-bind-html="offer.comment|linky"></b></div></div>
                        </div>

                    </div>

                    <div class="bottom-corner"></div>
                </div>
            </div>

            <div ng-if="offer.user.is_company_name" class="details-column-impressum">
                <div class="details-info">
                    <h2><?= Yii::t('app', 'Impressum'); ?></h2>
                    <div class="details-info-box">
                        <div class="details-impressum-box description-text" ng-bind-html="offer.user.impressum"></div>
                        <div class="link-open-agb">
                            <a href="" ng-click="offer.preview ? offerPreviewCtrl.openAgb() : offerDetailsCtrl.openAgb()"><?= Yii::t('app','AGB des Anbieters lesen') ?></a>
                        </div>
                    </div>
                    <div class="bottom-corner"></div>
                </div>
            </div>

        </div>

        <div ng-if="!offer.preview" class="show-other-searches">
            <a ui-sref="offers.searchByUser({id:offer.user.id})"><?= Yii::t('app', 'Weitere Angebote des Anbieters anzeigen') ?></a>
        </div>


        <div class="offered-btn-box" ng-if="status.id != offer.user.id && offer.status == 'ACTIVE' && !offer.preview">
            <a ng-if="offer.allow_contact" class="write-message-btn btn" href="" ng-click="offerDetailsCtrl.openChat()"><?= Yii::t('app', 'Nachricht schreiben') ?></a>
            <a class="accept-btn btn" once-if="offer.canAccept" href="" ng-click="offerDetailsCtrl.request();">
                <span ng-if="offer.type=='AUTOSELL'"><?= Yii::t('app', 'Kaufen') ?></span>
                <span ng-if="offer.type=='AUCTION'"><?= Yii::t('app', 'Verbindlich bieten') ?></span>
            </a>
            <a class="accepted-btn btn" once-if="!offer.canAccept && offer.type!='AD'" ><?= Yii::t('app', 'Gebot bereits abgegeben') ?></a>
            <a ng-if="!offer.spamReported" class="spam-report-btn btn" href="" ng-click="spamReport({offer_id:offer.id})"><?= Yii::t('app', 'Spam melden') ?></a>
            <a ng-if="offer.spamReported" class="spam-report-btn btn" href="" ng-click=""><?= Yii::t('app', 'Spam gemeldet') ?></a>
        </div>

        <div ng-if="offer.preview" class="offered-btn-box">
            <button class="btn btn-submit" ng-click="offerPreviewCtrl.back()"><?= Yii::t('app','Zurück') ?></button>
            <button class="btn btn-submit" ng-disabled="offer.saving" ng-click="offerPreviewCtrl.save()"><?=Yii::t('app','Einstellen');?></button>
        </div>


    </div>
</div>