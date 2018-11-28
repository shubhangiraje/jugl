<?php

use \yii\helpers\Html;


switch($payInRequest->type) {
    case \app\models\PayInRequest::TYPE_PACKET:
        $payInRequestTypeDescription=Yii::t('app','Premium-Mitgliedschaft');
        break;
    case \app\models\PayInRequest::TYPE_PACKET_VIP_PLUS:
        $payInRequestTypeDescription=Yii::t('app','PremiumPlus Mitgliedschaft');
        break;
    case \app\models\PayInRequest::TYPE_PAY_IN:
        $payInRequestTypeDescription=Yii::t('app','Jugls aufladen');
        break;
}

?>

<style>
    .document-header-logo {
        width: 100px;
        float: left;
    }

    .document-header-logo img {
        width: 100%;
        height: auto;
        display: block;
    }

    .document-header-data {
        float: left;
        width: 200px;
        padding-left: 20px;
        padding-top: 12px;
    }

    p {
        margin: 0;
        font-size: 14px;
    }

    table td {
        vertical-align: top;
    }

    .table-no-border td {
        padding: 3px 20px 3px 0;
    }

    .invoice-table td {
        width: 25%;
        border: 1px solid #000000;
        padding: 5px;
        font-size: 14px;
    }

    .document-footer {
        position: absolute;
        bottom: 50px;
        width: 100%;
    }

    .footer-table td {
        width: 20%;
        font-size: 12px;
        padding: 1px 0;
    }
</style>

<div style="height: 100px">
    <div style="float: right; width: 320px">
        <div class="document-header-logo"><img src="/static/images/site/logo-jugl.png" alt="logo"></div>
        <div class="document-header-data">
            <div><b>JuglApp GmbH</b></div>
            <div><b>Kurfürstendamm 178/179</b></div>
            <div><b>10707 Berlin</b></div>
            <div>E-Mail juglapp@gmx.de</div>
        </div>
    </div>
</div>

<br>
<p style="font-size: 12px; text-decoration: underline">JuglApp GmbH Kurfürstendamm 178/179 10707 Berlin</p>
<br>
<p><?= $payInRequest->user->name ?></p>
<p><?= $payInRequest->user->street ?> <?= $payInRequest->user->house_number ?></p>
<br>
<p><?= $payInRequest->user->zip ?> <?= $payInRequest->user->city ?></p>
<br><br>
<p><b><?= Yii::t('app', 'Rechnung') ?></b></p>
<br><br>

<table class="table-no-border" style="border-spacing: 0; padding: 0">
    <tr><td><?= Yii::t('app', 'Rechnungsdatum')?>:</td><td><?= Yii::$app->formatter->asDate($payInRequest->dt, 'php:d.m.Y'); ?></td></tr>
    <tr><td><?= Yii::t('app', 'Rechnungsnummer') ?>:</td><td><?= $payInRequest->id ?></td></tr>
    <tr><td><?= Yii::t('app', 'Produkt') ?>:</td><td>
            <?=$payInRequestTypeDescription?>
        </td></tr>
</table>

<br><br>

<table class="invoice-table" style="border-collapse: collapse;">
    <tr><td><?= Yii::t('app', 'POS.') ?></td><td><?= Yii::t('app', 'Bezeichnung') ?></td><td></td><td><?= Yii::t('app', 'Preis in EUR') ?></td></tr>
    <tr>
        <td>1</td>
        <td><?= $payInRequest->type == \app\models\PayInRequest::TYPE_PACKET ? $payInRequestTypeDescription : 'Jugls aufladen' ?> <?= Yii::t('app', 'zum') ?> <?= Yii::$app->formatter->asDate($payInRequest->dt, 'php:d.m.Y'); ?></td>
        <td><?= Yii::t('app', 'Netto') ?></td>
        <td><?= number_format($payInRequest->currency_sum/1.19, 2, ',', ' ') ?></td>
    </tr>
    <tr><td></td><td></td><td><?= Yii::t('app', 'USt') ?> 19 %</td><td><?= number_format($payInRequest->currency_sum-$payInRequest->currency_sum/1.19, 2, ',', ' ') ?></td></tr>
    <tr><td></td><td></td><td><?= Yii::t('app', 'Brutto-Gesamt') ?></td><td><?= number_format($payInRequest->currency_sum, 2, ',', ' ') ?></td></tr>
</table>

<br><br>

<p><?= Yii::t('app', 'Zahlungsart') ?>: <?= $payInRequest->paymentMethodLabel() ?></p>

<div class="document-footer">
    <table class="footer-table">
        <tr>
            <td>JuglApp GmbH</td>
            <td>
                Steuernummer: 27/250/25032<br>
                Ust.IdNr.: DE296478119<br>
                Handelsregisterrnummer: <br>HRG 156803 B
            </td>
            <td>
                Amtsgericht Charlottenburg<br>
                Geschäftsführer: <br>Peter Jugl
            </td>
        </tr>
    </table>
</div>


