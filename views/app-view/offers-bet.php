<?php


?>
<div class="offers-bet">
    <div class="container">
        <div class="welcome-text">
            <h2><?= Yii::t('app', 'Gebot abgeben') ?></h2>
        </div>

        <div class="offers-bet-box">

            <div class="offers-bet-field2-wrap clearfix">
                <div class="offers-bet-field2-box clearfix">
                    <label><?= Yii::t('app', 'Dein Gebot') ?><span>*</span>:</label>
                    <div class="field-box-input offer-bet-price" bs-has-classes>
                        <input ng-model="offerBet.price" price-validator server-error="offerBet.$errors.price" />
                        <span class="currency">&euro;</span>
                    </div>
                </div>
                <div class="offers-bet-field2-box clearfix">
                    <label><?= Yii::t('app', 'Dein Gebot ist gültig') ?><span>*</span>:</label>
                    <div class="field-box-select offer-bet-period" dropdown-toggle select-click bs-has-classes>
                        <select ng-model="offerBet.period" selectpicker="{title:''}" server-error="offerBet.$errors.period">
                            <option value=""></option>
                            <option value="1 hour"><?= Yii::t('app', '1 Std.')?></option>
                            <option value="3 hour"><?= Yii::t('app', '3 Std.')?></option>
                            <option value="6 hour"><?= Yii::t('app', '6 Std.')?></option>
                            <option value="12 hour"><?= Yii::t('app', '12 Std.')?></option>
                            <option value="24 hour"><?= Yii::t('app', '24 Std.')?></option>
                            <option value="3 day"><?= Yii::t('app', '3 Tage')?></option>
                            <option value="1 week"><?= Yii::t('app', '1 Woche')?></option>
                            <option value="4 week"><?= Yii::t('app', '4 Woche')?></option>
                            <option value="3 month"><?= Yii::t('app', '3 Monate')?></option>
                            <option value="6 month"><?= Yii::t('app', '6 Monate')?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="offers-bet-field-box">
                <label><?= Yii::t('app', 'Nachricht an den Verkäufer') ?>:</label>
                <div class="field-box-textarea offer-bet-description">
                    <textarea ng-model="offerBet.description" maxlength="1000"></textarea>
                </div>
            </div>

            <div class="offers-bet-note"><?= Yii::t('app', 'Es handelt sich um ein verbindliches Gebot. Wird dein Gebot akzeptiert, musst du es auch halten.') ?></div>
            <div class="offers-bet-note-light"><?= Yii::t('app', 'Der Verkäufer hat die Möglichkeit, sich unabhängig von der Höhe des Gebots einen Käufer auszusuchen.') ?></div>

        </div>

        <ul class="errors-list" ng-if="offerBet.$allErrors">
            <li ng-repeat="error in offerBet.$allErrors">
                <span>{{::error}}</span>
            </li>
        </ul>

        <div class="btn-box">
            <button ng-click="offerBetCtrl.bet()" class="btn btn-submit"><?= Yii::t('app', 'Gebot Abgeben') ?></button>
        </div>


    </div>
</div>