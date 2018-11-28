<?php

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

?>
<div class="modal-dialog login-dialog">
    <div class="modal-content">
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'enableClientValidation'=>false
        ]); ?>
        <div class="modal-header">
            <h4 class="modal-title"><?=Yii::t('app','Please login')?></h4>
        </div>
        <div class="modal-body">
            <?=$form->field($model,'username')?>
            <?=$form->field($model,'password')->passwordInput()?>

            <?php
            if ($model->getScenario()=='withCaptcha') {
                echo $form->field($model,'verifyCode',[
                ])->widget(Captcha::className());
            }
            ?>

            <?=''//$form->field($model,'rememberMe')->checkbox()?>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary"><?=Yii::t('app','Sign in')?></button>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
