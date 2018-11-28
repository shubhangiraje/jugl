<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>


<header class="site-header is-sticky">
    <?= $this->render('../layouts/ico-nav') ?>
</header>

<div class="section">
    <div class="container">

        <div class="row text-center">
            <div class="col-lg-12">
                <div class="section-head">
                    <div class="section-title animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;"><?= Yii::t('app', 'Auszahlung des Tokenbetrags') ?></div>
                </div>
            </div>
        </div>

        <div class="token-payout-box">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($icoPayoutForm, 'payment_method')->radioList(
                \app\models\IcoPayoutForm::getPaymentMethodList(),
                [
                    'item' => function($index, $label, $name, $checked, $value) {
                        $template = '<div class="field-radio">';
                        $template .= '<label class="radio-custom">';
                        $template .= '<input type="radio" name="'.$name.'" value="'.$value.'"'.($checked ? " checked":"").'>';
                        $template .= '<span class="check-circle"></span>';
                        $template .= $label;
                        $template .= '</label>';
                        $template .= '</div>';
                        return $template;
                    }
                ]
            )->label(false); ?>

            <div class="token-payout-fields-box">
                <?= $form->field($icoPayoutForm, 'iban')->textInput(['placeholder'=>Yii::t('app', 'IBAN')])->label(false) ?>
                <?= $form->field($icoPayoutForm, 'bic')->textInput(['placeholder'=>Yii::t('app', 'BIC')])->label(false) ?>
                <?= $form->field($icoPayoutForm, 'kontoinhaber')->textInput(['placeholder'=>Yii::t('app', 'Kontoinhaber')])->label(false) ?>
            </div>

            <div class="text-center">
                <?= Html::submitButton(Yii::t('app', 'Jetzt einlösen'), ['class'=>'btn']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>


    </div>
</div>
