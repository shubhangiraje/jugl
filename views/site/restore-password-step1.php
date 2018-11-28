<?php

use \app\components\ActiveForm;
use \yii\captcha\Captcha;

?>

<div class="content">
    <div class="container clearfix">

        <div class="page-title">
            <h1><?=Yii::t('app','Passwort vergessen')?></h1>
        </div>

        <?php if (Yii::$app->session->hasFlash('success')) { ?>
            <div class="success-sent">
                <p><?=Yii::t('app','Dir wurde eine Email zum Zurücksetzen Deines Passworts an {email} gesendet',['email'=>Yii::$app->session->getFlash('success')])?></p>
            </div>
        <?php } else { ?>
             <div class="page-form clearfix">
                 <div class="page-form-box">
                     <?php $form = ActiveForm::begin([
                        'id' => 'password-restore-step1-form',
                        'fieldConfig'=>['template'=>'{input}'],
                        'labelInPlaceholder'=>true
                     ])?>

                     <?=$form->errorSummary($model)?>

                     <div class="field-input-box">
                         <?=$form->field($model,'email')?>
                     </div>

                     <div class="captcha-box">
                         <?=$form->field($model,'verifyCode',[
                            'template'=>'{input}'
                         ])->widget(Captcha::className(), [
                            'template' => '{image}<a onclick="$(this).parent().find(\'img\').click();" class="refrech-captcha">'.Yii::t('app','Captcha - Code neu generieren').'</a><div class="field-input-box">{input}</div>',
                            'options'=> [
                                'placeholder'=>$model->getAttributeLabel('verifyCode'),
                                'autocomplete' => 'off',
                                'autocorrect'  => 'off'
                            ]
                         ])?>

                         <?=$this->registerJs('$(\'img[id$=-verifycode-image]\').click();')?>
                     </div>

                     <button type="submit" class="btn btn-submit"><?=Yii::t('app','Absenden')?></button>

                     <?php ActiveForm::end() ?>
                 </div>
             </div>
        <?php } ?>
    </div>
</div>






