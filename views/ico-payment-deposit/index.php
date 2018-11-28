<?php

use yii\widgets\ActiveForm;
use app\models\IcoPayment;
use yii\helpers\Html;

?>

<header class="site-header is-sticky">
    <?= $this->render('../layouts/ico-nav') ?>
    <div class="header-bottom-box">
        <h1><?= Yii::t('app', 'Tokens festlegen') ?></h1>
    </div>
</header>


<div class="section">
    <div class="container">

        <?php $form = ActiveForm::begin([
            'id'=>$model->formName(),
            'action'=>\yii\helpers\Url::to(['ico-payment-deposit/index']),
            'validationUrl'=>\yii\helpers\Url::to(['ico-payment-deposit/validate']),
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
                            var eur_exchange_rate= '.\app\models\Setting::TOKEN_DEPOSIT_TOKEN_TO_EURO_EXCHANGE_RATE.';
                            var jugl_exchange_rate= '.\app\models\Setting::TOKEN_DEPOSIT_TOKEN_TO_JUGL_EXCHANGE_RATE.';
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
                            
                            $(".period-input").each(function() {
                                var text="";
                                if (val>0) {
                                    var percent=$(this).attr("data-percent");
                                    var sum=(percent/100*val);
                                    sum=Math.floor((sum)*100+0.5)/100;
                                    text=" ("+percent+"%, "+sum+")";    
                                }
                                $(this).closest(".field-radio").find(".period-input-hint").html(text);
                            });
                        '
                    ])->label(Yii::t('app', 'Bitte gewünschte Anzahl an Tokens eingeben (von 10 bis 100.000):')) ?>
                    <div class="payment-method-sum-res">
                        <?= Yii::t('app', 'entspricht <span class="res-eur"></span> EUR / <span class="res-jugl"></span> Jugls') ?>
                    </div>
                </div>

            </div>
        </div>

        <div class="row text-center">
            <div class="col-lg-12">
                <div class="section-head">
                    <div class="section-number animated fadeInUp" data-animate="fadeInUp" data-delay=".0" style="visibility: visible; animation-delay: 0s;">02.</div>
                    <div class="section-title animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;"><?= Yii::t('app', 'Zeitraum festlegen:') ?></div>
                </div>

                <div class="payment-method-fields">
                    <?= $form->field($model, 'period_months')->radioList(
                        \app\models\TokenDeposit::getPeriodList(),
                        [
                            'item' => function($index, $label, $name, $checked, $value) {
                                $template = '<div class="field-radio">';
                                $template .= '<label class="radio-custom">';
                                $template .= '<input type="radio" class="period-input" data-percent="'.\app\models\Setting::get('TOKEN_DEPOSIT_PERCENT_'.$value.'_MONTHS').'" name="'.$name.'" value="'.$value.'"'.($checked ? " checked":"").'>';
                                $template .= '<span class="check-circle"></span>';
                                $template .= $label;
                                $template .= '<div class="period-input-hint"></div></label>';
                                $template .= '</div>';
                                return $template;
                            }
                        ]
                    )->label(false); ?>
                </div>
            </div>
        </div>

        <?php $immobilie=\app\models\TokenDepositGuarantee::getList(); ?>

        <?php if (count($immobilie)>0) { ?>
            <div class="row text-center">
            <div class="col-lg-12">
                <div class="section-head">
                    <div class="section-number animated fadeInUp" data-animate="fadeInUp" data-delay=".0" style="visibility: visible; animation-delay: 0s;">03.</div>
                    <div class="section-title animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;"><?= Yii::t('app', 'Immobilie auswählen:') ?></div>
                </div>

                <div class="tdg-container">
                    <?= $form->field($model, 'token_deposit_guarantee_id')->radioList(
                        $immobilie,
                        [

                            'class'=>'tdg-box clearfix',
                            'item' => function($index, $label, $name, $checked, $value) {
                                $TDG=\app\models\TokenDepositGuarantee::find()->where(['id'=>$value])->with(['tokenDepositGuaranteeFiles','tokenDepositGuaranteeFiles.file'])->one();
                                $template = '<div class="tdg-item">';
                                $template .= '<label class="radio-custom">';
                                $template .= '<input type="radio" name="'.$name.'" value="'.$value.'"'.($checked ? " checked":"").'>';
                                $template .= '<span class="check-circle"></span>';
                                $template .= '</label>';

                                $template .= '<div class="tdg-item-box clearfix">';

                                if (count($TDG->tokenDepositGuaranteeFiles)>0) {
                                    $template .= '<div class="tdg-picture-box">';
                                    $i = 1;
                                    foreach($TDG->tokenDepositGuaranteeFiles as $image) {
                                        $template .= Html::a(\yii\helpers\Html::img($image->file->getThumbUrl('tokenDepositGuarantee')), $image->file->getThumbUrl('fancybox'), ['class'=>'fancybox', 'rel'=>'pictures-'.$TDG->id, 'style'=>($i>1)?'display: none':'']);
                                        $i++;
                                    }
                                    $template .= '</div>';
                                }



                                $template .= '<div class="tdg-info-box '.(count($TDG->tokenDepositGuaranteeFiles)>0 ?null:'full').'" >';
                                $template .= '<div class="tdg-title" title="'.$TDG->title_de.'">'.$TDG->title_de.'</div>';
                                $template .= '<div class="tdg-desc">'.$TDG->description_de.'</div>';
                                $template .= '<div class="tdg-price">'.Yii::t('app','Wert').': '.app\components\Helper::formatPrice($TDG->sum_cost)." EUR".'</div>';
                                $template .= '<div class="tdg-price tdg-price2">'.Yii::t('app','Bereits festgelegt').': '.app\components\Helper::formatPrice($TDG->sum)." Tokens".'</div>';
                                $template .= '</div>';


                                $template .= '</div>';


                                $template .= '</div>';
                                return $template;
                            }
                        ]
                    )->label(false); ?>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="row text-center">
            <div class="col-lg-10 offset-lg-1">
                <div class="section-head">
                    <div class="section-number animated fadeInUp" data-animate="fadeInUp" data-delay=".0" style="visibility: visible; animation-delay: 0s;">0<?=count($immobilie) ? 4:3?>.</div>
                    <div class="section-title animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;"><?= Yii::t('app', 'Wähle die gewünschte Zahlungsart:') ?></div>
                </div>

                <div class="payment-method-fields" id="payment_method_selector">
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
                <?= Yii::t('app', 'Du legst gerade {tokens} Tokens für {price} Euro fest', [
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
                <?= Yii::t('app', 'Du legst gerade {tokens} Tokens für {price} Jugls fest', [
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
            <p>
                Lade jetzt Dein Jugl Konto auf oder wähle eine andere Zahlungsart.
            </p>
        </div>
        <div class="popup-buttons">
            <?= Html::button(Yii::t('app', 'Jugl Konto Aufladen'), ['class'=>'btn', 'onclick'=>'$("#payment-confirm-popup").hide(); window.location.href="https://jugl.net/my#/funds/payin"']) ?>
            <?= Html::button(Yii::t('app', 'Abbrechen'), ['class'=>'btn popup-close']) ?>
        </div>
    </div>
</div>

<script>
    function onContinueClicked() {
        var buyForJugls=$("#payment_method_selector input[type=radio]:checked").val()=="JUGL";

        if (buyForJugls && <?=floatval(Yii::$app->user->identity->balance)?><+$(".price-value-jugls").text()) {
            $("#payment-confirm-popup-not-enough-jugls").show();
            return;
        }

        var popupName=buyForJugls ? "#payment-confirm-popup-jugls":"#payment-confirm-popup";
        $(popupName).show();
    }
</script>