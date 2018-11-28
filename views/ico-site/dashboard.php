<?php

use yii\helpers\Url;
use app\components\Helper;
use yii\helpers\Html;
use app\components\EDateTime;

?>

<header class="site-header is-sticky">
    <?= $this->render('../layouts/ico-nav') ?>
    <div class="header-bottom-box">
        <h1><?=Yii::t('app','Hallo, {user}', ['user'=>Yii::$app->user->identity->name])?></h1>
    </div>
</header>


<div class="section">
    <div class="container clearfix">

        <div class="row text-center">
            <div class="col-lg-12">
                <div class="section-head">
                    <div class="section-title"><?= Yii::t('app', 'Mein Guthaben') ?></div>
                </div>

                <div class="balance-container">

                    <p><?= Yii::t('app', 'Hier siehst Du Deinen aktuellen Kontostand bei jugl.net.') ?></p>

                    <div class="buy-tokens-links">
                        <a href="<?=Url::to(['ico-payment/index'])?>" class="btn"><?=Yii::t('app','Tokens kaufen')?></a>
                        <a href="<?=Url::to(['ico-payment-deposit/index'])?>" class="btn"><?=Yii::t('app','Tokens festlegen')?></a>
                    </div>


                    <div class="balance-tabs-box">
                        <ul class="nav nav-tabs">
                            <li><a href="#kontostand" class="active show" data-toggle="tab" aria-expanded="false"><?=Yii::t('app','Kontostand')?></a></li>
                            <li><a href="#tokenstand" data-toggle="tab" aria-expanded="true"><?=Yii::t('app','Tokenstand')?></a></li>
                            <li><a href="#token-deposit" data-toggle="tab" aria-expanded="true"><?=Yii::t('app','Tokens festgelegt')?></a></li>
                        </ul>

                        <div class="tab-content">
                            <div id="kontostand" class="tab-pane show active">
                                <div class="balance-box clearfix">
                                    <div class="balance-item-box">
                                        <div class="balance-item balance-item-main">
                                            <div class="balance-item-title"><?=Yii::t('app','Kontostand')?></div>
                                            <div class="balance-item-value"><?=Helper::formatPrice($balance)?> <i class="symbol-jugl"></i></div>
                                            <div class="payable-box">
                                                <div><?=Yii::t('app','Davon nicht auszahlbar')?>:<span><?=Helper::formatPrice($balance_buyed)?> <i class="symbol-jugl"></i></span></div>
                                                <div><?=Yii::t('app','Davon auszahlbar')?>:<span><?=Helper::formatPrice($balance_earned+$balance_token_deposit_percent)?> <i class="symbol-jugl"></i></span></div>
                                            </div>
                                            <div class="text-left">
                                                <?= Html::a(Yii::t('app','Jugls jetzt einlösen'), 'https://jugl.net/my#/funds/payout', ['class'=>'white-link', 'target'=>'_blank']); ?>
                                            </div>
                                            <div class="payable-box">
                                                <div><?=Yii::t('app','Davon Zinsen')?>:<span><?=Helper::formatPrice($balance_token_deposit_percent)?> <i class="symbol-jugl"></i></span></div>
                                            </div>
                                            <div class="text-left">
                                                <?= Html::button(Yii::t('app','Garantierte Auszahlung beantragen'), [
                                                    'class'=>'white-link',
                                                    'onclick'=>Yii::$app->user->identity->validation_status!=\app\models\User::VALIDATION_STATUS_SUCCESS ?
                                                        '$("#payout-validation-required").show();'
                                                        :
                                                        ($balance_token_deposit_percent/\app\models\Setting::get("EXCHANGE_JUGLS_PER_EURO")>=50 ?'$("#guaranteed-payout-50-popup").show();':'$("#guaranteed-payout-not-50-popup").show();')
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="balance-item-box">
                                        <div class="balance-item">
                                            <div class="balance-pic"><i class="jugl-icon icon-calendar"></i></div>
                                            <div class="balance-title"><?=Yii::t('app','Verdienst heute')?>:</div>
                                            <div class="balance-value"><?=Helper::formatPrice($earned_today)?> <i class="symbol-jugl"></i></div>
                                        </div>
                                    </div>
                                    <div class="balance-item-box">
                                        <div class="balance-item">
                                            <div class="balance-pic"><i class="jugl-icon icon-calendar"></i></div>
                                            <div class="balance-title"><?=Yii::t('app','Verdienst gestern')?>:</div>
                                            <div class="balance-value"><?=Helper::formatPrice($earned_yesterday)?> <i class="symbol-jugl"></i></div>
                                        </div>
                                    </div>
                                    <div class="balance-item-box">
                                        <div class="balance-item">
                                            <div class="balance-item">
                                                <div class="balance-pic"><i class="jugl-icon icon-calendar"></i></div>
                                                <div class="balance-title"><?=Yii::t('app','Verdienst dieses Monats')?>:</div>
                                                <div class="balance-value"><?=Helper::formatPrice($earned_this_month)?> <i class="symbol-jugl"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="balance-item-box">
                                        <div class="balance-item">
                                            <div class="balance-item">
                                                <div class="balance-pic"><i class="jugl-icon icon-calendar"></i></div>
                                                <div class="balance-title"><?=Yii::t('app','Verdienst dieses Jahres')?>:</div>
                                                <div class="balance-value"><?=Helper::formatPrice($earned_this_year)?> <i class="symbol-jugl"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="balance-item-box">
                                        <div class="balance-item">
                                            <div class="balance-item">
                                                <div class="balance-pic"><i class="jugl-icon icon-calendar"></i></div>
                                                <div class="balance-title"><?=Yii::t('app','Gesamtverdienst seit Registrierung')?>:</div>
                                                <div class="balance-value"><?=Helper::formatPrice($earned_total)?> <i class="symbol-jugl"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="tokenstand" class="tab-pane">
                                <div class="balance-box clearfix">
                                    <div class="balance-item-box">
                                        <div class="balance-item balance-item-main">
                                            <div class="balance-pic"><i class="jugl-icon icon-coins"></i></div>
                                            <div class="balance-title"><?=Yii::t('app','Tokenstand')?></div>
                                            <div class="balance-value"><?=Helper::formatPrice($balance_token)?></div>
                                        </div>
                                    </div>
                                    <div class="balance-item-box">
                                        <div class="balance-item">
                                            <div class="balance-pic"><i class="jugl-icon icon-coins"></i></div>
                                            <div class="balance-title"><?=Yii::t('app','Tokens durch eigenen Kauf')?>:</div>
                                            <div class="balance-value"><?=Helper::formatPrice($balance_token_buyed)?></div>
                                        </div>
                                    </div>
                                    <div class="balance-item-box">
                                        <div class="balance-item">
                                            <div class="balance-pic"><i class="jugl-icon icon-coins"></i></div>
                                            <div class="balance-title"><?=Yii::t('app','Tokens durch Netzwerk')?>:</div>
                                            <div class="balance-value"><?=Helper::formatPrice($balance_token_earned)?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div id="token-deposit" class="tab-pane">
                                <div class="token-deposit-box">
                                    <?php foreach ($tokenDeposits as $tokenDeposit) { ?>
                                        <div class="token-deposit-item">
                                            <div class="token-deposit-balance">
                                                <div class="balance-pic"><i class="jugl-icon icon-coins"></i></div>
                                                <div class="balance-title"><?= Yii::t('app','Tokens festgelegt') ?></div>
                                                <div class="balance-value"><?= Helper::formatPrice($tokenDeposit['sum']) ?></div>
                                            </div>
                                            <div class="token-deposit-details">
                                                <div class="token-deposit-detail-item clearfix">
                                                    <div class="token-deposit-detail-title"><?= Yii::t('app','Zu welchem Preis gekauft') ?>:</div>
                                                    <div class="token-deposit-detail-value">
                                                        <?= Helper::formatPrice($tokenDeposit['buy_sum']).' '.$tokenDeposit['buy_currency'] ?>
                                                    </div>
                                                </div>
                                                <div class="token-deposit-detail-item clearfix">
                                                    <div class="token-deposit-detail-title"><?= Yii::t('app','Festgelegter Zeitraum') ?>:</div>
                                                    <div class="token-deposit-detail-value"><?= $tokenDeposit['period_months']/12 ?> <?=Yii::t('app','Jahr(e)')?></div>
                                                </div>
                                                <div class="token-deposit-detail-item clearfix">
                                                    <div class="token-deposit-detail-title"><?= Yii::t('app','Zinssatz p.a.') ?>:</div>
                                                    <div class="token-deposit-detail-value"><?= $tokenDeposit['contribution_percentage'] ?> %</div>
                                                </div>
                                                <div class="token-deposit-detail-item clearfix">
                                                    <div class="token-deposit-detail-title"><?= Yii::t('app','Zinsertrag in Jugls') ?>:</div>
                                                    <div class="token-deposit-detail-value"><?= Helper::formatPriceWithSmallPart($tokenDeposit['percents_payed_sum']) ?></div>
                                                </div>
                                                <div class="token-deposit-detail-item clearfix">
                                                    <div class="token-deposit-detail-title"><?= Yii::t('app','Erstellt am') ?>:</div>
                                                    <div class="token-deposit-detail-value"><?= (new EDateTime($tokenDeposit['created_at']))->format('d.m.Y') ?></div>
                                                </div>
                                                <div class="token-deposit-detail-item clearfix">
                                                    <div class="token-deposit-detail-title"><?= Yii::t('app','Wird freigegeben am') ?>:</div>
                                                    <div class="token-deposit-detail-value"><?= (new EDateTime($tokenDeposit['completion_dt']))->format('d.m.Y') ?></div>
                                                </div>
                                                <div class="trade-tokens-box clearfix">
                                                    <div class="trade-tokens-text"><?= Yii::t('app', 'Tokens eintauschen gegen:') ?></div>
                                                    <div class="trade-tokens-btns">

                                                        <?php
                                                            if ((new EDateTime($tokenDeposit['completion_dt']))>=(new EDateTime())) {
                                                                echo Html::button(Yii::t('app', 'Jugls'), [
                                                                    'class' => 'btn-blue',
                                                                    'onclick' => '
                                                                        var tokenDepositCompletionDt = $("#trade-tokens-time-not-come-popup").find(".token-deposit-completion-dt");
                                                                        tokenDepositCompletionDt.text("");
                                                                        tokenDepositCompletionDt.text("' . (new EDateTime($tokenDeposit['completion_dt']))->format("d.m.Y H:i") . '"); 
                                                                        $("#trade-tokens-time-not-come-popup").show();
                                                                    '
                                                                ]);
                                                                echo Html::button(Yii::t('app', 'Euro'), [
                                                                    'class' => 'btn-blue',
                                                                    'onclick' => '
                                                                        var tokenDepositCompletionDt = $("#trade-tokens-time-not-come-popup").find(".token-deposit-completion-dt");
                                                                        tokenDepositCompletionDt.text("");
                                                                        tokenDepositCompletionDt.text("' . (new EDateTime($tokenDeposit['completion_dt']))->format("d.m.Y H:i") . '"); 
                                                                        $("#trade-tokens-time-not-come-popup").show();
                                                                    '
                                                                ]);
                                                            } else {
                                                                echo Html::button(Yii::t('app', 'Jugls'), [
                                                                    'class'=>'btn-blue',
                                                                    'onclick'=>'$("#trade-tokens-jugls-popup input[name=id]").val('.$tokenDeposit['id'].');$("#trade-tokens-jugls-popup").show();',
                                                                ]);
                                                                echo Html::button(Yii::t('app', 'Euro'), [
                                                                    'class'=>'btn-blue',
                                                                    'onclick'=>Yii::$app->user->identity->validation_status!=\app\models\User::VALIDATION_STATUS_SUCCESS ?
                                                                        '$("#payout-validation-required").show()'
                                                                        :
                                                                        '$("#trade-tokens-euro-popup input[name=id]").val('.$tokenDeposit['id'].');$("#trade-tokens-euro-popup").show();'
                                                                ]);
                                                            }
                                                        ?>

                                                        <?php /* ?>
                                                        <?= Html::button(Yii::t('app', 'Jugls'), [
                                                            'class'=>'btn-blue',
                                                            'onclick'=>'$("#trade-tokens-jugls-popup input[name=id]").val('.$tokenDeposit['id'].');$("#trade-tokens-jugls-popup").show();',
                                                            'disabled'=>(new EDateTime($tokenDeposit['completion_dt']))>=(new EDateTime())
                                                        ]) ?>
                                                        <?= Html::button(Yii::t('app', 'Euro'), [
                                                            'class'=>'btn-blue',
                                                            'onclick'=>Yii::$app->user->identity->validation_status!=\app\models\User::VALIDATION_STATUS_SUCCESS ?
                                                                '$("#payout-validation-required").show()'
                                                                :
                                                                '$("#trade-tokens-euro-popup input[name=id]").val('.$tokenDeposit['id'].');$("#trade-tokens-euro-popup").show();',
                                                            'disabled'=>(new EDateTime($tokenDeposit['completion_dt']))>=(new EDateTime())
                                                        ]) ?>
                                                        <?php */ ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>


    </div>
</div>

<div class="popup-wrapper" id="trade-tokens-time-not-come-popup">
    <div class="popup-box">
        <div class="popup-close btn-popup-close"></div>
        <div class="popup-content">
            <p><?= Yii::t('app', 'Die Tokens sind bis zum {time} festgelegt. Erst danach werden diese Buttons aktiv.', [
                    'time'=>'<span class="token-deposit-completion-dt"></span>'
               ]) ?>
            </p>
        </div>
        <div class="popup-buttons">
            <?= Html::button(Yii::t('app', 'Ok'), ['class'=>'btn popup-close']) ?>
        </div>
    </div>
</div>


<div class="popup-wrapper popup-wrapper-md" id="trade-tokens-jugls-popup">
    <div class="popup-box">
        <div class="popup-close btn-popup-close"></div>
        <div class="popup-content">
            <p><?= Yii::t('app', 'Willst Du die freigegebenen Tokens gegen Jugls eintauschen?') ?></p>
            <p><?= Yii::t('app', 'Diese Aktion ist einmalig und kann später nicht rückgängig gemacht werden.') ?></p>
        </div>
        <div class="popup-buttons">
            <?php $form=\yii\widgets\ActiveForm::begin(['action'=>['token-deposit-payout'],'method'=>'GET']) ?>
                <?= Html::hiddenInput('type','JUGLS') ?>
                <?= Html::hiddenInput('id','') ?>
                <?= Html::button(Yii::t('app', 'Jetzt eintauschen'), ['class'=>'btn','type'=>'submit']) ?>
                <?= Html::button(Yii::t('app', 'Abbrechen'), ['class'=>'btn popup-close']) ?>
            <?php \yii\widgets\ActiveForm::end() ?>
        </div>
    </div>
</div>

<div class="popup-wrapper popup-wrapper-md" id="trade-tokens-euro-popup">
    <div class="popup-box">
        <div class="popup-close btn-popup-close"></div>
        <div class="popup-content">
            <p><?= Yii::t('app', 'Willst Du die freigegebenen Tokens gegen Eur eintauschen?') ?></p>
            <p><?= Yii::t('app', 'Diese Aktion ist einmalig und kann später nicht rückgängig gemacht werden.') ?></p>
        </div>
        <div class="popup-buttons">
            <?php $form=\yii\widgets\ActiveForm::begin(['action'=>['token-deposit-payout'],'method'=>'GET']) ?>
            <?= Html::hiddenInput('type','EUR') ?>
            <?= Html::hiddenInput('id','') ?>
            <?= Html::button(Yii::t('app', 'Jetzt eintauschen'), ['class'=>'btn','type'=>'submit']) ?>
            <?= Html::button(Yii::t('app', 'Abbrechen'), ['class'=>'btn popup-close']) ?>
            <?php \yii\widgets\ActiveForm::end() ?>
        </div>
    </div>
</div>


<div class="popup-wrapper popup-wrapper-md" id="guaranteed-payout-not-50-popup">
    <div class="popup-box">
        <div class="popup-close btn-popup-close"></div>
        <div class="popup-content">
            <p><?= Yii::t('app', 'Der Betrag soll mehr als 50 EUR (umgerechten {jugls} Jugls) sein, damit eine Auszahlung gemacht werden kann.',['jugls'=>\app\components\Helper::formatPrice(50*\app\models\Setting::get("EXCHANGE_JUGLS_PER_EURO"))]) ?></p>
        </div>
        <div class="popup-buttons">
            <?= Html::button(Yii::t('app', 'Ok'), ['class'=>'btn popup-close']) ?>
        </div>
    </div>
</div>

<div class="popup-wrapper popup-wrapper-md" id="payout-validation-required">
    <div class="popup-box">
        <div class="popup-close btn-popup-close"></div>
        <div class="popup-content">
            <p><?= Yii::t('app', 'Um Deine Auszahlungsanfrage verarbeiten zu können musst Du Dich vorab durch unser Team verifizieren lassen.') ?></p>
        </div>
        <div class="popup-buttons">
            <?= Html::a(Yii::t('app', 'Zum jugl.net'), 'https://jugl.net/my#/funds/payout' ,['class'=>'btn popup-close']) ?>
        </div>
    </div>
</div>

<div class="popup-wrapper popup-wrapper-md" id="guaranteed-payout-50-popup">
    <div class="popup-box">
        <div class="popup-close btn-popup-close"></div>
        <div class="popup-content">
            <p><?= Yii::t('app', 'Du bist dabei, ejne Auszahlung von {jugls} Jugls (umgerechten {euro} Euro) zu beantragen.',[
                    'jugls'=>\app\components\Helper::formatPrice($balance_token_deposit_percent),
                    'euro' =>\app\components\Helper::formatPrice($balance_token_deposit_percent/\app\models\Setting::get("EXCHANGE_JUGLS_PER_EURO"))
                ]) ?></p>
        </div>
        <div class="popup-buttons">
            <?= Html::a(Yii::t('app', 'Weiter'),['token-percent-payout'],['class'=>'btn']) ?>
            <?= Html::button(Yii::t('app', 'Abbrechen'), ['class'=>'btn popup-close']) ?>
        </div>
    </div>
</div>

