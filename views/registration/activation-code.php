<?php

use app\components\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Url;

?>


<div class="content">
    <div class="container clearfix">

        <div class="page-title">
            <h1><?=Yii::t('app', 'Aktivierungscode')?></h1>
            <p><?=Yii::t('app', 'Bitte gib hier den Aktivierungscode ein, den Du per SMS erhalten hast.')?></p>
        </div>


        <div class="page-form clearfix">
            <div class="page-form-box">
                <?php $form = ActiveForm::begin([
                    'id'=>'registration-activation-code',
                    'fieldConfig'=>['template'=>'{input}'],
                    'labelInPlaceholder'=>true,
                    'enableClientValidation'=>false
                ]); ?>

                <?= $form->errorSummary($model); ?>

                <div class="field-input-box">
                    <?= $form->field($model,'code')->textInput([
                        'autocomplete' => 'off',
                        'autocorrect'  => 'off'
                    ]); ?>
                </div>

                <?php if($model->getScenario()=='withCaptcha') { ?>
                    <div class="captcha-box">
                        <?= $form->field($model,'verifyCode',[
                            'template'=>'{input}'
                        ])->widget(Captcha::className(), [
                        'template' => '{image}<a onclick="$(this).parent().find(\'img\').click();" class="refrech-captcha">'.Yii::t('app','Captcha-Code neu generieren').'</a><div class="field-input-box">{input}</div>',
                            'options'=>[
                                'placeholder'=>$model->getAttributeLabel('verifyCode'),
                                'autocomplete' => 'off',
                                'autocorrect'  => 'off'
                            ]
                        ]); ?>
                    </div>
                <?php } ?>

                <?=$form->field($model,'request_help')->hiddenInput(array('id'=>'request_help'))?>

                <?php if (Yii::$app->session->hasFlash('help_requested')) { ?>
                    <div class="popup-registration-help-wrap">
                        <div class="popup-registration-help-box">
                            <span class="popup-registration-help-close"></span>
                            <div class="popup-registration-help-data">
                                <?= Yii::t('app', 'Vielen Dank, deine Hilfe-Anfrage wurde an unser Team weitergeleitet. Wir werden uns mit dir in Kürze in Verbindung setzen.')?>
                            </div>
                            <div class="popup-registration-help-btn-ok btn btn-green"><?= Yii::t('app', 'Ok') ?></div>
                        </div>
                    </div>
                <?php } ?>

                <div class="activation-code-btn-box clearfix">
                    <button type="submit" class="btn btn-submit"><?= Yii::t('app','Best&auml;tigen'); ?></button>
                    <a href="<?= Url::to(['data']) ?>" class="btn btn-repetition"><?= Yii::t('app','Erneut zusenden'); ?></a>
                </div>

                <?php ActiveForm::end(); ?>

                <div class="registration-stage-btn-box">
                    <button type="button" class="registration-stage-btn"><?= Yii::t('app','Ich komme nicht weiter und benötige Hilfe'); ?></button>
                </div>
            </div>
        </div>

    </div>
</div>


