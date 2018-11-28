<?php

use \app\models\Offer;

$debounce=" ng-model-options=\"{ updateOn: 'default blur', debounce: { default: 500, blur: 0 } }\" ";
?>

<div class="searches offers-add">
    <div class="container">
        <div ng-click="showInfoPopup('view-offers-add')" ng-class="{'blink':isOneShowInfoPopup('view-offers-add')}" class="info-popup-btn"></div>
        <div class="welcome-text">
            <h1><?= Yii::t('app', 'Kostenlos inserieren<br /> Verkaufen / Werbung schalten'); ?></h1>
            <p>Bitte beachten: Eine Mehrfachschaltung kostenloser Anzeigen mit identischem Inhalt im gleichen Zeitraum ist nicht möglich.</div>
        <div class="searches-title-box"><h2><?=Yii::t('app','Kategorie')?></h2></div>
        <div class="searches-box clearfix">

            <div class="searches-users-related-interests">
                <?= Yii::t('app', 'Nutzer-/Kundenkreis'); ?>: <span class="countRequests">{{offer.receiversAllCount|priceFormat}}</span>
            </div>
            <div class="searches-category clearfix fields-full">
                <label><?=Yii::t('app', 'Kategorie');?><span>*</span>:</label>
                <ul class="list-category">
                    <li ng-if="offer.offerInterests[0].level1Interest.id">{{offer.offerInterests[0].level1Interest.title}}</li>
                    <li ng-if="offer.offerInterests[0].level2Interest.id">{{offer.offerInterests[0].level2Interest.title}}</li>
                    <li ng-if="offer.offerInterests[0].level3Interest.id"><span ng-repeat="interest in offer.offerInterests">{{interest.level3Interest.title}}{{!$last ? ', ':''}}</span></li>
                </ul>
                <div class="clearfix"></div>
                <p><?=Yii::t('app', 'Hier kannst Du die Kategorie auswählen, unter der die Anzeige erscheinen soll.');?></p>
                <a href="" ng-click="offerAddCtrl.addInterests()" class="btn btn-submit add-category-btn" ><?=Yii::t('app', 'Kategorie w&auml;hlen');?></a>
            </div>

            <div class="searches-details-fields">
                <div class="searches-fields-box fields-full">
                    <div class="field-box field-offer-type clearfix">
                        <?php /*
                        <div class="field-box-label">
                            <label><?=Offer::getEncodedAttributeLabel('type')?><span>*</span>:</label>
                        </div>
                        <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                            <select ng-model="offer.type" selectpicker server-error="offer.$errors.type">
                                <?php foreach(Offer::getTypeList() as $k=>$v) { ?>
                                    <option value="<?=$k?>"><?=$v?></option>
                                <?php } ?>
                            </select>
                        </div>
                        */ ?>
                        <div class="field-restrict-form radio-line-box">
                            <?php foreach(Offer::getTypeList() as $k => $v) { ?>

                                <div class="field-box-radio fields-full" bs-has-classes>

                                    <?php
                                    if($k == 'AUCTION'){
                                        $desc = '<p class="clearfix">'.Yii::t('app', 'Du bietest etwas an und erhältst Gebote in unterschiedlicher Höhe auf Deine Ware. Du kannst Dir, unabhängig von der Höhe des Gebots, einen Käufer aussuchen. Falls Du Dich entscheiden solltest, Deine Ware doch nicht zu verkaufen, bist Du nicht verpflichtet ein Gebot anzunehmen. (z.B. wenn die Gebote nicht Deinen Vorstellungen entsprechen). Du kannst auch mehrere Gebote in unterschiedlicher Höhe annehmen und somit in einem Bieterverfahren mehrere Stücke einer Ware verkaufen.').'</p>';
                                        $afterlabel = Yii::t('app', '(mit Kaufbonus und Provision)');
                                    }elseif($k == 'AD'){
                                        $desc = '<p class="clearfix">'.Yii::t('app', 'Die klassische Form der Werbung - Gewinne neue Kunden, mach auf Dich aufmerksam. Informiere potentielle Interessenten über Deine Ware und Angebote. (Werbung für konkurrierende Unternehmen ist nicht kostenfrei.)').'</p>';
                                        $afterlabel = Yii::t('app', '(kostenlos)');
                                    }elseif($k == 'AUTOSELL'){
                                        $desc = '<p class="clearfix">'.Yii::t('app', 'Du bietest etwas an und wenn der Interessent auf den "Kaufen"-Button klickt, kommt der Handel sofort zustande.').'</p>';
                                        $afterlabel = Yii::t('app', '(mit Kaufbonus und Provision)');
                                    }else{
                                        $desc = '';
                                    }
                                    ?>
                                    <?= $desc; ?>
                                    <input type="radio" i-check ng-model="offer.type" value="<?=$k?>" <?=$debounce?> server-error="offer.$errors.type" />
                                    <label><?=Yii::t('app',$v).' '.$afterlabel;?></label>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                    <div class="field-box clearfix fields-full">
                        <div class="fieldin-item">
                            <p class="clearfix"><?=Yii::t('app', 'Interessenten können Dir direkt über die Anzeige eine Nachricht senden. Im Messenger wird Dir diese Nachricht als zu der Werbung zugehörig angezeigt.');?></p>
                        </div>
                        <div class="fieldin-item">
                            <div class="field-box-entry" bs-has-classes>
                                <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="offer.allow_contact" server-error="offer.$errors.allow_contact">
                            </div>
                            <div class="field-box-label">
                                <label><?=Offer::getEncodedAttributeLabel('allow_contact')?>:</label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div ng-if="offer.offerParamValues[0]" class="searches-category-fields">
                <div class="searches-category-fields-params clearfix">

                    <div ng-repeat="srpv in offer.offerParamValues" class="item-searches-category-field-param">

                        <div class="field-box clearfix">
                            <div class="field-box-label">
                                <label>{{srpv.param.title}}<span ng-if="srpv.param.required">*</span>:</label>
                            </div>
                            <div ng-if="srpv.param.type==='LIST'" class="field-box-select" dropdown-toggle select-click>
                                <select ng-model="srpv.param_value_id" selectpicker ng-options="value.id as value.title for value in srpv.param.values">
                                    <option></option>
                                </select>
                            </div>
                            <div ng-if="srpv.param.type!=='LIST'" class="field-box-select">
                                <div class="field-box-input">
                                    <input type="text" ng-model="srpv.param_value">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--<div ng-if="offer.offerParamValues[0]" class="searches-category-fields">
                <div class="searches-fields-box fields-left">
                    <div ng-repeat="srpv in params1" class="field-box clearfix">
                        <div class="field-box-label">
                            <label>{{srpv.param.title}}<span ng-if="srpv.param.required">*</span>:</label>
                        </div>
                        <div ng-if="srpv.param.type==='LIST'" class="field-box-select" dropdown-toggle select-click>
                            <select ng-model="srpv.param_value_id" ng-options="value.id as value.title for value in srpv.param.values">
                                <option></option>
                            </select>
                        </div>
                        <div ng-if="srpv.param.type!=='LIST'" class="field-box-select">
                            <div class="field-box-input">
                                <input type="text" ng-model="srpv.param_value">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="searches-fields-box fields-right">
                    <div ng-repeat="srpv in params2" class="field-box clearfix">
                        <div class="field-box-label">
                            <label>{{srpv.param.title}}<span ng-if="srpv.param.required">*</span>:</label>
                        </div>
                        <div ng-if="srpv.param.type==='LIST'" class="field-box-select" dropdown-toggle select-click>
                            <select ng-model="srpv.param_value_id" ng-options="value.id as value.title for value in srpv.param.values">
                                <option></option>
                            </select>
                        </div>
                        <div ng-if="srpv.param.type!=='LIST'" class="field-box-select">
                            <div class="field-box-input">
                                <input type="text" ng-model="srpv.param_value">
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->
        </div>

        <div class="searches-title-box"><h2><?=Yii::t('app','Anzeigedetails')?></h2></div>
        <div class="searches-box clearfix">
            <div class="searches-details-fields">
                <div class="searches-fields-box fields-full">

                    <div class="field-box clearfix fields-full">
                        <div class="fieldin-item">
                            <p class="clearfix"><?=Yii::t('app', 'Gib hier den Titel Deiner Anzeige ein. Er ist das Erste, was ein potentieller Interessent liest.');?></p>
                        </div>
                        <div class="fieldin-item">
                            <div class="field-box-entry entry-full-block">
                                <div class="field-box-input" bs-has-classes>
                                    <input type="text" ng-model="offer.title" server-error="offer.$errors.title" placeholder="<?=Yii::t('app', 'Titel eingeben*'); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="field-box clearfix fields-full">
                        <div class="fieldin-item">
                            <p class="clearfix"><?=Yii::t('app', 'Gib hier die Beschreibung Deiner Anzeige ein. Je detaillierter diese ist, desto genauer wissen potentielle Interessenten, worum es bei Deiner Anzeige geht.');?></p>
                        </div>
                        <div class="fieldin-item">
                            <div class="field-box-entry entry-full-block">
                                <div class="field-box-textarea" bs-has-classes>
                                    <textarea ng-model="offer.description" maxlength="2000" server-error="offer.$errors.description" placeholder="<?=Offer::getEncodedAttributeLabel('description')?> eingeben *"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="searches-fields-box fields-full">
                        <div class="field-box clearfix">
                            <div class="fieldin-item">
                                <p class="clearfix"><?=Yii::t('app', 'Hebe Deine Anzeige hervor, um Deine Anzeigen attraktiver zu gestalten. Lade Bilder Deiner Ware hoch. Steigere die Aufmerksamkeit der Jugler und Deine Erfolgsquote.');?></p>
                            </div>
                            <div class="fieldin-item">
                                <div class="fields-box-image">
                                    <div ng-repeat="file in offer.files" class="preview-upload-image">
                                        <img ng-src="{{file.thumbs.imageBig}}" />
                                        <button ng-click="offerAddCtrl.deleteFile(file.id)" class="btn-del-image"></button>
                                    </div>
                                    <div class="box-input-file" ng-if="uploader.queue.length != uploader.queueLimit">
                                        <div class="spinner" ng-if="uploader.isUploading"></div>
                                        <span class="icon-input-file" ng-if="!uploader.isUploading"></span>
                                        <input type="file" nv-file-select filters="imageFilter,queueLimit" uploader="uploader" options="fileUploadOptions" multiple />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php /*
						<div class="box-add-bilder">
							<button class="btn btn-submit"><?=Yii::t('app','Bilder hinzufugen');?></button>
						</div>
						*/ ?>
                    </div>

                    <div ng-if="offer.type=='<?=Offer::TYPE_AUTOSELL?>'" class="field-box fields-full clearfix">
                        <div class="fieldin-item">
                            <p class="clearfix"><?=Yii::t('app', 'Preis angeben');?></p>
                        </div>
                        <div class="fieldin-item">
                            <div class="field-box-entry">
                                <div class="jugl-box-input" bs-has-classes>
                                    <input price-validator type="text" ng-model="offer.price" server-error="offer.$errors.price" placeholder="<?=Yii::t('app','Preis');?>" />
                                    <span>&euro;</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ng-if="offer.type=='<?=Offer::TYPE_AUCTION?>'" class="field-box fields-full clearfix">
                        <div class="fieldin-item">
                            <p class="clearfix"><?=Yii::t('app', 'Gib hier Deine Preisvorstellung ein. Diese dient als Richtwert für die Erstellung der Gebote Deiner Interessenten. Bitte beachte, dass diese Angabe in Euro ist.');?></p>
                        </div>
                        <div class="fieldin-item">
                            <div class="field-box-entry entry-full-block">
                                <div class="jugl-box-input" bs-has-classes>
                                    <input price-validator type="text" ng-model="offer.price" server-error="offer.$errors.price" placeholder="<?=Yii::t('app','Preisvorstellung eingeben');?>*" />
                                    <span>&euro;</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ng-if="offer.type=='<?=Offer::TYPE_AUCTION?>'" class="field-box fields-full clearfix">
                        <div class="fieldin-item">
                            <p class="clearfix"><?=Yii::t('app', 'Hier kannst Du einstellen, ab welcher Gebotshöhe Du eine Benachrichtigung von Jugl.net erhalten möchtest.');?></p>
                        </div>
                        <div class="fieldin-item">
                            <div class="field-box-entry entry-full-block">
                                <div class="jugl-box-input" bs-has-classes>
                                    <input placeholder="<?=Yii::t('app','Gebots-Benachrichtigung erhalten ab*');?>" price-validator type="text" ng-model="offer.notify_if_price_bigger" server-error="offer.$errors.notify_if_price_bigger" />
                                    <span>&euro;</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ng-if="offer.type!='<?=Offer::TYPE_AD?>'" class="field-box fields-full clearfix">
                        <div class="fieldin-item">
                            <p class="clearfix"><?=Yii::t('app', 'Gib hier die Stückzahl ein. Wenn alle Waren verkauft sind, läuft die Anzeige automatisch aus.');?></p>

                            <div class="field-box-entry entry-full-block">
                                <div class="jugl-box-input" bs-has-classes>
                                    <input number-valid type="text" ng-model="offer.amount" server-error="offer.$errors.amount" placeholder="<?=Offer::getEncodedAttributeLabel('amount')?> angeben*" />
                                </div>
                            </div>
                        </div>
                        <div class="fieldin-item">
                            <p class="clearfix"><?=Yii::t('app', 'Wenn Du hier ein Häkchen setzt, dann wird in Deiner Anzeige die noch verfügbare Stückzahl Deiner Ware angezeigt.');?></p>
                            <div ng-if="offer.type!='<?=Offer::TYPE_AD?>'">
                                <div class="field-box-entry" bs-has-classes>
                                    <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="offer.show_amount" server-error="offer.$errors.show_amount" />
                                </div>
                                <div class="field-box-label">
                                    <label><?=Offer::getEncodedAttributeLabel('show_amount')?></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ng-if="offer.type!='<?=Offer::TYPE_AD?>'" class="field-box fields-full clearfix">
                        <div class="fieldin-item">
                            <p class="clearfix"><?=Yii::t('app', 'Wenn Du Deine Ware versenden möchtest, dann gib hier bitte die Lieferzeit ein, die Dein Paket bis zum Empfänger benötigt und wie viel der Versand kosten soll. Wenn Du Deine Ware nur zur Selbstabholung anbietest, dann gib hier bitte jeweils eine 0 ein.');?></p>
                            <div class="field-box-entry entry-full-block">
                                <div class="jugl-box-input" bs-has-classes>
                                    <input number-valid type="text" ng-model="offer.delivery_days" server-error="offer.$errors.delivery_days" placeholder="<?= Yii::t('app', 'Lieferzeit angeben*') ?>" />
                                    <span class="delivery-days-label"><?= Yii::t('app', 'Tage') ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="fieldin-item">
                            <div ng-if="offer.type!='<?=Offer::TYPE_AD?>'" class="field-box clearfix">
                                <div class="field-box-entry entry-full-block">
                                    <div class="jugl-box-input" bs-has-classes>
                                        <input price-validator type="text" ng-model="offer.delivery_cost" server-error="offer.$errors.delivery_cost" placeholder="<?= Yii::t('app', 'Versandkosten angeben') ?>" />
                                        <span>&euro;</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ng-if="offer.type!='<?=Offer::TYPE_AD?>'" class="field-box fields-full clearfix">
                        <div class="fieldin-item">
                            <p class="clearfix"><?=Yii::t('app', 'Wähle nun die von Dir akzeptierten Zahlungsmethoden aus. Bitte beachte, dass Du eine Zahlung per Überweisung oder Paypal nur angeben kannst, wenn Du die dazugehörigen Daten in Deinem Profil (Menüleiste -> Mein Profil / Meine Daten) hinterlegt hast.');?></p>
                            <div class="fieldin-item">
                                <div class="field-box-entry" bs-has-classes>
                                    <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="offer.pay_allow_bank" server-error="offer.$errors.pay_allow_bank" />
                                </div>
                                <div class="field-box-label">
                                    <label><?=Offer::getEncodedAttributeLabel('pay_allow_bank')?>:</label>
                                </div>
                            </div>
                            <div class="fieldin-item">
                                <div class="field-box-entry" bs-has-classes>
                                    <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="offer.pay_allow_paypal" server-error="offer.$errors.pay_allow_paypal" />
                                </div>
                                <div class="field-box-label">
                                    <label><?=Offer::getEncodedAttributeLabel('pay_allow_paypal')?>:</label>
                                </div>
                            </div>
                            <div class="fieldin-item">
                                <div class="field-box-entry" bs-has-classes>
                                    <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="offer.pay_allow_jugl" server-error="offer.$errors.pay_allow_jugl" />
                                </div>
                                <div class="field-box-label">
                                    <label><?=Offer::getEncodedAttributeLabel('pay_allow_jugl')?>:</label>
                                </div>
                            </div>
                            <div class="fieldin-item">
                                <div class="field-box-entry" bs-has-classes>
                                    <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="offer.pay_allow_pod" server-error="offer.$errors.pay_allow_pod" />
                                </div>
                                <div class="field-box-label">
                                    <label><?=Offer::getEncodedAttributeLabel('pay_allow_pod')?>:</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div ng-if="offer.type!='<?=Offer::TYPE_AD?>'" class="searches-title-box"><h2><?=Yii::t('app','Ort')?></h2></div>
        <div ng-if="offer.type!='<?=Offer::TYPE_AD?>'" class="searches-box clearfix">
            <div class="places-fields-box clearfix">
                <p class="clearfix"><?=Yii::t('app', 'Gib hier den Ort an, an dem Deine Ware abgeholt werden kann. Wenn Deine Ware nicht zur Selbstabholung angeboten wird, gib hier Deinen Standort an.');?></p>
                <div class="places-field-left-box clearfix">
                    <div class="field-box-label">
                        <label><?= Yii::t('app', 'Land') ?><span>*</span>:</label>
                    </div>
                    <div class="field-box-entry">
                        <div class="field-box-select" dropdown-toggle select-click>
                            <select ng-model="offer.country_id" selectpicker ng-options="item.id as item.country for item in countries">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="places-field-right-box clearfix">
                    <div class="field-box-label">
                        <label><?=Offer::getEncodedAttributeLabel('city')?>:</label>
                    </div>
                    <div class="field-box-entry">
                        <div class="field-box-input" bs-has-classes>
                            <input type="text" ng-model="offer.city" server-error="offer.$errors.city" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="places-fields-box clearfix">
                <div class="places-field-left-box clearfix">
                    <div class="field-box-label">
                        <label><?=Offer::getEncodedAttributeLabel('zip')?><span>*</span>:</label>
                    </div>
                    <div class="field-box-entry">
                        <div class="field-box-input" bs-has-classes>
                            <input type="text" ng-model="offer.zip" server-error="offer.$errors.zip" />
                        </div>
                    </div>
                </div>
                <div class="places-field-right-box clearfix">
                    <div class="field-box-label">
                        <label><?=Offer::getEncodedAttributeLabel('address')?>:</label>
                    </div>
                    <div class="field-box-entry">
                        <div class="field-box-input" bs-has-classes>
                            <input type="text" ng-model="offer.address" server-error="offer.$errors.address" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="searches-title-box"><h2><?=Yii::t('app','Dauer der Ver&ouml;ffentlichung')?></h2></div>
        <div class="searches-box duration clearfix">
            <p class="clearfix"><?=Yii::t('app', 'Lege fest, wie lange Deine Werbung bei Jugl.net zu sehen sein soll.');?></p>
            <div class="field-box">
                <div class="field-box-label">
                    <label><?=Yii::t('app','Anzeige aktiv bis');?>:</label>
                </div>
                <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                    <select ng-model="offer.active_till_parts.day" selectpicker="{title:''}" ng-options="item.key as item.val for item in birthDayList">
                        <option value=""><?= Yii::t('app', 'Day'); ?></option>
                    </select>
                </div>
                <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                    <select ng-model="offer.active_till_parts.month" selectpicker="{title:''}" ng-options="item.key as item.val for item in birthMonthList">
                        <option value=""><?= Yii::t('app', 'Monat'); ?></option>
                    </select>
                </div>
                <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                    <select ng-model="offer.active_till_parts.year" selectpicker="{title:''}" ng-options="item.key as item.val for item in birthYearList">
                        <option value=""><?= Yii::t('app', 'Jahr'); ?></option>
                    </select>
                </div>
            </div>
            <span class="notes-field"><?= Yii::t('app', 'Bei fehlender Eingabe bleibt die Anzeige 6 Monate aktiv.'); ?></span>
        </div>

        <div ng-if="offer.type!='<?=Offer::TYPE_AD?>'" class="searches-title-box"><h2><?=Yii::t('app','Boni festlegen')?></h2></div>
        <div ng-if="offer.type!='<?=Offer::TYPE_AD?>'" class="searches-box duration clearfix">
            <p class="clearfix"><?=Yii::t('app', 'Gib hier den Kaufbonus für Deine/n Käufer an. Nachdem Du den Geldeingang bestätigt hast, erhält der Käufer den Kaufbonus. Der Kaufbonus sollte ca. 1-5% des Kaufpreises sein.');?></p>
            <div class="field-box">
                <div class="field-box fields-full clearfix">
                    <p class="clearfix"><?= Yii::t('app', 'Je realistischer der Kaufbonus ist, desto seriöser wirkt Deine Anzeige auf den Käufer.'); ?></p>
                    <div class="field-box-entry entry-full-block">
                        <div class="jugl-box-input" bs-has-classes>
                            <input price-validator type="text" ng-model="offer.buy_bonus" server-error="offer.$errors.buy_bonus" placeholder="Kaufbonus eingeben*" />
                            <span class="jugl-icon-light"></span>
                        </div>
                    </div>
                </div>
                <div ng-if="offer.type!='<?=Offer::TYPE_AD?>'" class="field-box fields-full clearfix">
                    <p class="clearfix">{{SELLBONUS_SELLER_PARENTS_PERCENT}}% <?= Yii::t('app', 'des Kaufbonus-Wertes erhält derjenige, der Dich zu Jugl.net eingeladen hat, nachdem Du den Geldeingang bestätigst')?>:</p>
                    <div class="field-box-entry entry-full-block">
                        <div class="jugl-box-input" bs-has-classes>
                            <input price-validator readonly="readonly" type="text" ng-model="offer.buy_bonus_provision" server-error="offer.$errors.buy_bonus_provision" />
                            <span class="jugl-icon-light"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="searches-title-box"><h2><?=Yii::t('app','Annonce auf Startseite anzeigen (kostenpflichtig)')?></h2></div>
        <div class="searches-box clearfix">
            <div class="searches-details-fields">
                <p class="clearfix"><?=Yii::t('app', 'Wenn Du dies nicht willst, setze hier einen Haken:');?></p>
                <div class="fieldin-item clearfix">
                    <div class="field-box-entry" bs-has-classes>
                        <input type="checkbox" ng-true-value="1" ng-false-value="0" i-check ng-model="offer.without_view_bonus" server-error="offer.$errors.show_amount" />
                    </div>
                    <div class="field-box-label">
                        <label><?=Yii::t('app', 'Werbung ohne Werbebonus schalten');?>:</label>
                    </div>
                </div>
                <div ng-if="!offer.without_view_bonus" class="field-box fields-full clearfix">
                    <p class="clearfix"><?=Yii::t('app', 'Du kannst Deine Annonce auf der Startseite anzeigen lassen. Dabei erhält jeder, der Deine Anzeige ansieht (mind. 30 Sekunden), eine von Dir festgelegte Anzahl von Jugl-Punkten (Werbebonus).');?></p>
                    <p class="clearfix"><?=Yii::t('app', 'Maximale Aufmerksamkeit für Dich und Dein Produkt! Schalte Deine Annonce auf der Startseite und profitiere von unserem cleveren System. Mit dem Werbebonus = "Jugls for view" wird der User von Dir dafür belohnt, dass er sich über Deine Neuigkeiten oder Produkte informiert. Kunden, die Du normalerweise nicht erreicht hättest, werden durch den Werbebonus auf Dein Produkt aufmerksam.');?></p>
                    <div class="clearfix searches-category  offer-txt">
                        <?=Yii::t('app', 'Gib an, wie viel Werbebonus der einzelne Interessent für das Lesen Deiner Werbung auf der Startseite in der Kategorie'); ?>
                        <ul class="list-category">
                            <li ng-if="offer.offerInterests[0].level1Interest.id">{{offer.offerInterests[0].level1Interest.title}}</li>
                            <li ng-if="offer.offerInterests[0].level2Interest.id">{{offer.offerInterests[0].level2Interest.title}}</li>
                            <li ng-if="offer.offerInterests[0].level3Interest.id"><span ng-repeat="interest in offer.offerInterests">{{interest.level3Interest.title}}{{!$last ? ', ':''}}</span></li>
                        </ul>
                        <?=Yii::t('app', 'erhält ( mind. {{offer.view_bonus_interest}} ). 1 Jugl = 1 Cent.');?>
                    </div>

                    <a href="" ng-click="offerAddCtrl.addInterests()" class="btn btn-submit add-category-btn" ><?=Yii::t('app', 'Kategorie w&auml;hlen');?></a>
                    <br /><br />

                    <div class="fieldin-item clearfix">
                        <div class="field-box-entry entry-full-block">
                            <div class="jugl-box-input werbeboni" bs-has-classes>
                                <input price-validator type="text" ng-model="offer.view_bonus" server-error="offer.$errors.view_bonus" placeholder="Werbebonus festlegen ( mind. {{offer.view_bonus_interest}} )*" />
                                <span class="jugl-icon-light"></span>
                            </div>
                        </div>
                    </div>
                    <p class="clearfix"><?=Yii::t('app', 'Bei jedem Klick vom Werbebonus gehen zusätzlich<br />+ 10% an Jugl.net und<br />+ 10% an den, der Dich zu Jugl.net eingeladen hat. ');?></p>

                    <div class="clearfix"></div>
                    <!--<div ng-if="!offer.without_view_bonus">
							<div class="field-box-entry">
								<div class="notes-field" style="padding-top:0;">
									<?= Yii::t('app','Bei jedem Klick vom  Werbebonus zusätzlich<br/>+{percent1} an Jugl.net und<br/>+{percent2} an den, der Dich zu Jugl eingeladen hat', [
                        'percent1'=>'<span ng-if="offer.view_bonus">{{offer.view_bonus*'.\app\models\Offer::VIEW_BONUS_PERCENT_JUGL.'/100|priceFormat}}<span class="jugl-icon-light"></span></span> ('.\app\models\Offer::VIEW_BONUS_PERCENT_JUGL.'%)',
                        'percent2'=>'<span ng-if="offer.view_bonus">{{offer.view_bonus*'.\app\models\Offer::VIEW_BONUS_PERCENT_PARENT.'/100|priceFormat}}<span class="jugl-icon-light"></span></span> ('.\app\models\Offer::VIEW_BONUS_PERCENT_PARENT.'%)'
                    ]) ?>
								</div>
							</div>
						</div>-->
                </div>
                <div ng-if="!offer.without_view_bonus" class="field-box fields-full clearfix">
                    <p class="clearfix"><?=Yii::t('app', 'Lege Dein Budget fest ( mind. {{offer.view_bonus_total_interest}} <span class="jugl-icon-light"></span> ). Nicht verbrauchtes Budget wird sofort wieder gutgeschrieben.');?> </p>
                    <div class="fieldin-item clearfix">
                        <div class="field-box-entry entry-full-block">
                            <div class="jugl-box-input werbeaktion" bs-has-classes>
                                <input price-validator type="text" ng-model="offer.view_bonus_total" server-error="offer.$errors.view_bonus_total" placeholder="Gesamtbudget für Werbeaktion in Jugls festlegen ( mind. {{offer.view_bonus_total_interest}} )*" />
                                <span class="jugl-icon-light"></span>
                            </div>
                        </div>
                    </div>
                    <a ui-sref="funds.payin" ui-sref-active="active" class="btn btn-submit payin-jugl-btn" ><?= Yii::t('app', 'Jugl-Punkte aufladen') ?></a>
                    <p class="clearfix txt-center"><?=Yii::t('app', 'Du kannst Deine Anzeige auch ohne Werbebonus schalten. Sie ist dann jedoch nicht auf der Startseite zu sehen und nur in "Inserate durchsuchen" zu finden.');?></p>
                </div>
            </div>
        </div>
        <div ng-if="!offer.without_view_bonus" class="offer-restrict-wrap">

            <div class="searches-title-box">
                <h2><?= Yii::t('app', 'Interessenten-/Kundenkreis einschränken'); ?></h2>
                <h3><?= Yii::t('app', 'Definiere genau, wer Deine Werbung mit Werbebonus sehen kann.'); ?></h3>
            </div>

            <div class="searches-box">
                <div class="offer-restrict-form">
                    <h3 class="subhead no-padding-top"><?=Yii::t('app','In den nachfolgenden Feldern kannst Du den Kreis derer einschränken, die Deine Werbung mit Werbebonus (Jugls for view) sehen können. Damit kannst Du Dein Werbebudget ganz gezielt einsetzen.'); ?></h3>
                    <div class="field-restrict-form radio-line-box field-box fields-full clearfix">
                        <h3 class="clearfix txt-left"><?= Yii::t('app', 'Anzeige sichtbar für:'); ?></h3>
                        <div class="field-box-radio" bs-has-classes>
                            <input type="radio" i-check ng-model="offer.uf_packet" value="VIP" <?=$debounce?> server-error="offer.$errors.uf_packet" />
                            <label><?=Yii::t('app','Premium-Mitglieder')?></label>
                        </div>

                        <div class="field-box-radio" bs-has-classes>
                            <input type="radio" i-check ng-model="offer.uf_packet" value="VIP_PLUS" <?=$debounce?> server-error="offer.$errors.uf_packet" />
                            <label><?=Yii::t('app','PremiumPlus-Mitglieder')?></label>
                        </div>

                        <div class="field-box-radio" bs-has-classes>
                            <input type="radio" i-check ng-model="offer.uf_packet" value="STANDART" <?=$debounce?> server-error="offer.$errors.uf_packet" />
                            <label><?=Yii::t('app','Basis-Mitglieder')?></label>
                        </div>

                        <div class="field-box-radio" bs-has-classes>
                            <input type="radio" i-check ng-model="offer.uf_packet" value="ALL" <?=$debounce?> server-error="offer.$errors.uf_packet" />
                            <label><?=Yii::t('app','Alle')?></label>
                        </div>

                    </div>

                    <div class="field-restrict-form radio-line-box field-box fields-full clearfix">
                        <h3 class="clearfix txt-left"><?= Yii::t('app', 'Anzeige sichtbar für:'); ?></h3>
                        <div class="field-box-radio" bs-has-classes>
                            <input type="radio" i-check ng-model="offer.uf_sex" value="M" <?=$debounce?> server-error="offer.$errors.uf_sex" />
                            <label><?=Yii::t('app','Man')?></label>
                        </div>

                        <div class="field-box-radio" bs-has-classes>
                            <input type="radio" i-check ng-model="offer.uf_sex" value="F" <?=$debounce?> server-error="offer.$errors.uf_sex" />
                            <label><?=Yii::t('app','Woman')?></label>
                        </div>

                        <div class="field-box-radio" bs-has-classes>
                            <input type="radio" i-check ng-model="offer.uf_sex" value="A" <?=$debounce?> server-error="offer.$errors.uf_sex" />
                            <label><?=Yii::t('app','Alle')?></label>
                        </div>
                    </div>


                    <div class="field-restrict-form field-box fields-full clearfix">
                        <div class="fields-col2">
                            <div class="field-box-label">
                                <label><?=Yii::t('app','Alter')?>:</label>
                            </div>

                            <div class="field-box-from-to">
                                <div class="field-from-box">
                                    <div class="field-box-input" bs-has-classes>
                                        <input number-valid type="text" ng-model="offer.uf_age_from" <?=$debounce?> server-error="offer.$errors.uf_age_from" placeholder="<?=Yii::t('app','von');?>" />
                                    </div>
                                </div>
                                <div class="field-to-box">

                                    <div class="field-box-input" bs-has-classes>
                                        <input number-valid type="text" ng-model="offer.uf_age_to" <?=$debounce?> server-error="offer.$errors.uf_age_to" placeholder="<?=Yii::t('app','bis');?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="field-restrict-form radio-line-box field-box fields-full clearfix">
                        <h3 class="clearfix txt-left"><?= Yii::t('app', 'Sichtbarkeit räumlich einschränken:'); ?></h3>
                        <!--<div class="field-restrict-form">
							<div class="field-box-input" bs-has-classes>
								<?php /* <?=\app\models\Offer::getEncodedAttributeLabel('uf_zip')?> */ ?>
								<input type="text" ng-model="offer.uf_zip" <?php//=$debounce?> server-error="offer.$errors.uf_zip" placeholder="Postleitzahl" />
							</div>
						</div>

						<div class="field-restrict-form">
							<div class="field-box-input" bs-has-classes>
								<?php /* <?=\app\models\Offer::getEncodedAttributeLabel('uf_city')?> */ ?>
								<input type="text" ng-model="offer.uf_city" <?php//=$debounce?> server-error="offer.$errors.uf_city" placeholder="Ort" />
							</div>
						</div>

						<div class="field-restrict-form">
							<div class="field-box-select" dropdown-toggle select-click bs-has-classes>
								<select ng-model="offer.uf_distance_km" selectpicker="{title:''}" <?php//=$debounce?> server-error="offer.$errors.uf_distance_km" >
									<option value=""><?php//=Yii::t('app','Umkreis (km)')?></option>
									<option value="1">1 km</option>
									<option value="5">5 km</option>
									<option value="10">10 km</option>
									<option value="20">20 km</option>
									<option value="50">50 km</option>
									<option value="100">100 km</option>
								</select>
							</div>
						</div>-->

                        <div class="field-restrict-form">
                            <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                                <select ng-model="offer.uf_country_id" selectpicker="{title:'Land auswählen'}" ng-options="item.id as item.country for item in countries" <?=$debounce?> server-error="offer.$errors.uf_country_id" />
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="field-restrict-form field-box fields-full clearfix">
                        <p class="clearfix"><?=Yii::t('app','Hier kannst Du zum Beispiel, wenn Du 0-3 eingibst, nur neuen Mitgliedern Deine Werbung zeigen. Gibst Du "ab 25 Tage" ein, handelt es sich eher um erfahrene Juglmitglieder'); ?></p>
                        <div class="fields-col2">
                            <div class="field-box-label">
                                <label><?=Yii::t('app','Mitglied seit (in Tagen)')?>:</label>
                            </div>

                            <div class="field-box-from-to">
                                <div class="field-from-box">

                                    <div class="field-box-input" bs-has-classes>
                                        <input number-valid type="text" ng-model="offer.uf_member_from" <?=$debounce?> server-error="offer.$errors.uf_member_from" placeholder="<?=Yii::t('app','von');?>" />
                                    </div>
                                </div>
                                <div class="field-to-box">
                                    <div class="field-box-input" bs-has-classes>
                                        <input number-valid type="text" ng-model="offer.uf_member_to" <?=$debounce?> server-error="offer.$errors.uf_member_to" placeholder="<?=Yii::t('app','bis');?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="field-restrict-form field-box fields-full clearfix">
                        <p class="clearfix"><?=Yii::t('app','Hier kannst Du auswählen, ob nur Mitglieder, die schon einmal einen Suchauftrag bei Jugl.net erstellt haben, Deine Werbung sehen können.')?></p>
                        <div class="fields-col2">
                            <div class="field-box-label">
                                <label><?=Yii::t('app','Anzahl aktiver Suchaufträge bei Jugl.net'); ?></label>
                            </div>
                            <div class="field-box-input" bs-has-classes>
                                <input type="text" ng-model="offer.uf_active_search_requests_from" <?=$debounce?> server-error="offer.$errors.uf_active_search_requests_from" placeholder="0" />
                            </div>
                        </div>
                    </div>

                    <div class="field-restrict-form field-box fields-full clearfix">
                        <p class="clearfix"><?=Yii::t('app','Zeige Deine Werbung nur aktiven Mitgliedern, die regelmäßig Nachrichten schreiben.'); ?></p>
                        <div class="fields-col2">
                            <div class="field-box-label">
                                <label><?=Yii::t('app','Durchschnittswert Nachrichten User pro 24Std.')?>:</label>
                            </div>

                            <div class="field-box-from-to">
                                <div class="field-from-box">

                                    <div class="field-box-input" bs-has-classes>
                                        <input number-valid type="text" ng-model="offer.uf_messages_per_day_from" <?=$debounce?> server-error="offer.$errors.uf_messages_per_day_from" placeholder="<?=Yii::t('app','von');?>" />
                                    </div>
                                </div>
                                <div class="field-to-box">
                                    <div class="field-box-input" bs-has-classes>
                                        <input number-valid type="text" ng-model="offer.uf_messages_per_day_to" <?=$debounce?> server-error="offer.$errors.uf_messages_per_day_to" placeholder="<?=Yii::t('app','bis');?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="field-restrict-form field-box fields-full clearfix">
                        <p class="clearfix"><?=Yii::t('app','Hier kannst Du eingeben, wie oft sich ein User Werbung ansieht, bevor dieser einen Artikel kauft. Gibst Du hier z.B. von 50 bis 100 ein, dann zeigst Du Deine Werbung nur Mitgliedern, die auf 50-100 gelesene Werbungen einen Kauf tätigen.');?></p>
                        <div class="fields-col2">
                            <div class="field-box-label">
                                <label><?=Yii::t('app','Verh&auml;ltnis gekaufer Artikel zu gelesener Werbung (Kaufbonus erhalten) 1')?>:</label>
                            </div>

                            <div class="field-box-from-to">
                                <div class="field-from-box">

                                    <div class="field-box-input" bs-has-classes>
                                        <input number-valid type="text" ng-model="offer.uf_offers_view_buy_ratio_from" <?=$debounce?> server-error="offer.$errors.uf_offers_view_buy_ratio_from" placeholder="<?=Yii::t('app','von');?>" />
                                    </div>
                                </div>
                                <div class="field-to-box">
                                    <div class="field-box-input" bs-has-classes>
                                        <input number-valid type="text" ng-model="offer.uf_offers_view_buy_ratio_to" <?=$debounce?> server-error="offer.$errors.uf_offers_view_buy_ratio_to" placeholder="<?=Yii::t('app','bis');?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="field-restrict-form field-box fields-full clearfix">
                        <p class="clearfix"><?=Yii::t('app','Hier kannst Du auswählen, wie viel Umsatz in Euro ein Mitglied durchschnittlich erzielt haben muss, um Deine Werbung sehen zu können.'); ?></p>
                        <div class="fields-col2">
                            <div class="field-box-label">
                                <label><?=Yii::t('app','Durchschnittlicher Umsatz in &euro;')?>:</label>
                            </div>

                            <div class="field-box-from-to">
                                <div class="field-from-box">
                                    <div class="field-box-input" bs-has-classes>
                                        <input number-valid type="text" ng-model="offer.uf_offer_year_turnover_from" <?=$debounce?> server-error="offer.$errors.uf_offer_year_turnover_from" placeholder="<?=Yii::t('app','von');?>" />
                                    </div>
                                </div>
                                <div class="field-to-box">
                                    <div class="field-box-input" bs-has-classes>
                                        <input number-valid type="text" ng-model="offer.uf_offer_year_turnover_to" <?=$debounce?> server-error="offer.$errors.uf_offer_year_turnover_to" placeholder="<?=Yii::t('app','bis');?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="field-restrict-form field-box fields-full clearfix">
                        <p class="clearfix"><?=Yii::t('app','Zeige Deine Werbung nur Mitgliedern, die genügend Punkte auf ihrem Jugl-Konto haben, um Deine Waren erwerben zu können.');?></p>
                        <div class="fields-col2">
                            <div class="field-box-label">
                                <label><?=Yii::t('app','Kontostand (in Jugl-Punkten)');?></label>
                            </div>
                            <div class="field-box-input" bs-has-classes>
                                <input number-valid type="text" ng-model="offer.uf_balance_from" <?=$debounce?> server-error="offer.$errors.uf_balance_from" placeholder="0"/>
                            </div>
                        </div>
                    </div>

                    <div class="field-restrict-form field-box fields-full clearfix">
                        <div class="field-box-label">
                            <label><?=Yii::t('app','Zeige Deine Anzeige nur Nutzern, die schon einmal Artikel in dieser Kategorie gekauft haben.');?></label>
                        </div>
                        <div class="field-box-select" dropdown-toggle select-click bs-has-classes>
                            <select ng-model="offer.uf_offer_request_completed_interest_id"  selectpicker="{title:'Kategorie auswählen'}" ng-options="item.id as item.title for item in level1Interests" <?=$debounce?> server-error="offer.$errors.uf_offer_request_completed_interest_id" />
                            </select>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <ul class="errors-list" ng-if="offer.$allErrors">
            <li ng-repeat="error in offer.$allErrors">
                <span ng-if="error!='NOT_ENOUGH_JUGL'">{{::error}}</span>
                <span ng-if="error=='NOT_ENOUGH_JUGL'"><?= Yii::t('app', 'Du hast leider nicht genug Jugls auf Deinem Konto.')?>&nbsp;<a ui-sref="funds.payin"><?= Yii::t('app', 'Jetzt Jugls aufladen')?></a></span>
            </li>
        </ul>

        <?php /* ?>
        <div class="searches-submit-box">
            <button class="btn btn-submit" ng-disabled="offer.saving" ng-click="offerAddCtrl.save()"><?=Yii::t('app','Werbeaktion jetzt starten');?></button>
        </div>
        <?php */ ?>

        <div class="searches-users-related-interests" style="text-align:center; padding-bottom:0px;">
            <?= Yii::t('app', 'Nutzer-/Kundenkreis'); ?>: <span class="countRequests">{{offer.receiversAllCount|priceFormat}}</span>
        </div>
        <div class="searches-submit-box">
            <button class="btn btn-submit" ng-click="offerAddCtrl.preview()"><?= Yii::t('app', 'Vorschau ansehen') ?></button>
            <h4 class="clearfix"><?= Yii::t('app', 'Die Vorschau ermöglicht es Dir, Deine Anzeige vor der Veröffentlichung noch einmal genau zu überprüfen und ggf. Korrekturen vorzunehmen.')?></h4>
        </div>

        <button ng-click="offerAddCtrl.saveDraft()">saveDraft</button>


    </div>
