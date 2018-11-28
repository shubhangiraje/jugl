<?php

use yii\widgets\ActiveForm;
use app\models\IcoPayment;
use yii\helpers\Html;

?>


<header class="site-header is-sticky">

    <?= $this->render('../layouts/ico-nav') ?>

    <div id="header" class="header d-flex align-items-center">
        <div class="container">
            <div class="header-box text-center animated fadeIn" data-animate="fadeIn" data-delay=".8" style="visibility: visible; animation-delay: 0.8s;">
                <h1><?= Yii::t('app', 'Jugl-Tokens kaufen') ?></h1>
                <span><?= Yii::t('app', 'Hallo').', '.Yii::$app->user->identity->name ?></span>
                <p><?= Yii::t('app', 'Hier hast Du die Möglichkeit, Jugl-Tokens zu kaufen. Wähle einfach die gewünschte Anzahl der Tokens und bezahle sicher, einfach und bequem mit der Zahlungsart Deiner Wahl.') ?></p>
            </div>
        </div>
    </div>

</header>


<div class="section">
    <div class="container">

        <?php $form = ActiveForm::begin([
            'id'=>$model->formName(),
            'action'=>\yii\helpers\Url::to(['ico-payment/index']),
            'validationUrl'=>\yii\helpers\Url::to(['ico-payment/validate']),
            'enableAjaxValidation' => true,
            'scrollToError'=>false
        ]); ?>

        <div class="row text-center">
            <div class="col-lg-12">
                <div class="section-head">
                    <div class="section-number animated fadeInUp" data-animate="fadeInUp" data-delay=".0" style="visibility: visible; animation-delay: 0s;">01.</div>
                    <div class="section-title animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;"><?= Yii::t('app', 'Wähle die gewünschte Anzahl der Tokens:') ?></div>
                </div>

                <div class="token-sum-box">

                    <?= $form->field($model, 'tokens', [
                        'template'=>'<div class="field-input">{label}{input}{error}</div>'
                    ])->textInput([
                        'class'=>'text-center input-line',
                        'oninput'=>'
                            var val = $(this).val();
                            var eur_exchange_rate= '.\app\models\Setting::get('TOKEN_TO_EURO_EXCHANGE_RATE').';
                            var jugl_exchange_rate= '.\app\models\Setting::get('TOKEN_TO_JUGL_EXCHANGE_RATE').';
                            var res_eur=val.match(/^\d+([.,]\d*)?$/) ? val*eur_exchange_rate:"";     
                            var res_jugl=val.match(/^\d+([.,]\d*)?$/) ? val*jugl_exchange_rate:"";
                            res_eur = Math.floor((res_eur)*100+0.5)/100;
                            res_jugl = Math.floor((res_jugl)*100+0.5)/100;
                            
                            $(".tokens-value").text(val);
                            $(".price-value").text(res_eur);
                            $(".price-value-jugls").text(res_jugl);
                            
                            if (val!=="") {
                                $(".payment-method-sum-res>.res-eur").html(res_eur);
                                $(".payment-method-sum-res>.res-jugl").html(res_jugl);
                                $(".payment-method-sum-res").show();
                            } else {
                                $(".payment-method-sum-res").hide();
                            }
                        '
                    ])->label(Yii::t('app', 'Bitte gewünschte Anzahl an Tokens eingeben (von 10 bis 100.000)')) ?>:
                    <div class="payment-method-sum-res">
                        <?= Yii::t('app', 'entspricht <span class="res-eur"></span> EUR / <span class="res-jugl"></span> Jugls') ?>
                    </div>
                </div>

            </div>
        </div>

        <div class="row text-center">
            <div class="col-lg-10 offset-lg-1">
                <div class="section-head">
                    <div class="section-number animated fadeInUp" data-animate="fadeInUp" data-delay=".0" style="visibility: visible; animation-delay: 0s;">02.</div>
                    <div class="section-title animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;"><?= Yii::t('app', 'Wähle die gewünschte Zahlungsart') ?></div>
                </div>

                <div class="payment-method-fields">
                    <?= $form->field($model, 'payment_method')->radioList(
                        IcoPayment::getPaymentMethodList(),
                        [
                            'item' => function($index, $label, $name, $checked, $value) {
                                $template = '<div class="field-radio">';
                                $template .= '<label class="radio-custom">';
                                $template .= '<input type="radio" name="'.$name.'" value="'.$value.'"'.($checked ? " checked":"").'>';
                                $template .= '<span class="check-circle"></span>';
                                $template .= $label;
                                $template .= '</label>';

                                if ($value == IcoPayment::PAYMENT_METHOD_PAYONE_CC) {
                                    $template .= '<div class="field-note">'.Yii::t('app', 'Visa, Mastercard, American Express').'</div>';
                                }

                                $template .= '</div>';
                                return $template;
                            }
                        ]
                    )->label(false); ?>
                </div>

            </div>
        </div>

        <div class="row text-center">
            <div class="col-lg-8 offset-lg-2">
                <p><?= Yii::t('app', 'Die Belastung Deines Kreditkartenkontos erfolgt i.d.R. 2-3 Werktage nach Abschluss der Bestellung. Deine Kreditkartendaten kannst Du auf der nächsten Seite eingeben. Bitte weiter klicken.') ?></p>

                <div class="payment-picture-box">
                    <img src="/static/images/account/visa.png" alt="visa">
                    <img src="/static/images/account/mastercard.png" alt="mastercard">
                </div>
            </div>
        </div>

        <?=$form->errorSummary($model)?>

        <div class="text-center">
            <?= Html::button(Yii::t('app', 'Weiter'), ['class'=>'btn', 'onclick'=>'onContinueClicked()']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>


<div class="popup-wrapper" id="payment-confirm-popup">
    <div class="popup-box">
        <div class="popup-content">
            <p>
                <?= Yii::t('app', 'Du kaufst jetzt {tokens} Tokens für {price} Euro', [
                    'tokens'=>'<b class="tokens-value">0</b>',
                    'price'=>'<b class="price-value">0</b>'
                ]); ?>
            </p>
        </div>
        <div class="popup-buttons">
            <?= Html::button(Yii::t('app', 'Ok'), ['class'=>'btn', 'onclick'=>'$(this).closest(".popup-wrapper").hide(); $("#'.$model->formName().'").submit();']) ?>
            <?= Html::button(Yii::t('app', 'Abbrechen'), ['class'=>'btn popup-close']) ?>
        </div>
    </div>
</div>

<div class="popup-wrapper" id="payment-confirm-popup-jugls">
    <div class="popup-box">
        <div class="popup-content">
            <p>
                <?= Yii::t('app', 'Du kaufst jetzt {tokens} Tokens für {price} Jugls', [
                    'tokens'=>'<b class="tokens-value">0</b>',
                    'price'=>'<b class="price-value-jugls">0</b>'
                ]); ?>
            </p>
        </div>
        <div class="popup-buttons">
            <?= Html::button(Yii::t('app', 'Ok'), ['class'=>'btn', 'onclick'=>'$(this).closest(".popup-wrapper").hide(); $("#'.$model->formName().'").submit();']) ?>
            <?= Html::button(Yii::t('app', 'Abbrechen'), ['class'=>'btn popup-close']) ?>
        </div>
    </div>
</div>

<div class="popup-wrapper" id="payment-confirm-popup-not-enough-jugls">
    <div class="popup-box">
        <div class="popup-content">
            <p><?= Yii::t('app', 'Lade jetzt Dein Jugl Konto auf oder wähle eine andere Zahlungsart.') ?></p>
        </div>
        <div class="popup-buttons">
            <?= Html::button(Yii::t('app', 'Jugl Konto Aufladen'), ['class'=>'btn', 'onclick'=>'$(this).closest(".popup-wrapper").hide(); window.location.href="https://jugl.net/my#/funds/payin"']) ?>
            <?= Html::button(Yii::t('app', 'Abbrechen'), ['class'=>'btn popup-close']) ?>
        </div>
    </div>
</div>

<script>
    function onContinueClicked() {
        var buyForJugls=$("input[type=radio]:checked").val()=="JUGL";

        if (buyForJugls && <?=floatval(Yii::$app->user->identity->balance)?><+$(".price-value-jugls").text()) {
            $("#payment-confirm-popup-not-enough-jugls").show();
            return;
        }

        var popupName=buyForJugls ? "#payment-confirm-popup-jugls":"#payment-confirm-popup";
        $(popupName).show();
    }
</script>