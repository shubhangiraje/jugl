<?php

use app\components\ActiveForm;

?>

<div class="content">
    <div class=" container clearfix">

        <?php if(Yii::$app->session->hasFlash('success')) { ?>
            <div class="success-sent">
                <p><?= Yii::t('app','Passwort wurde erfolgreich ge&auml;ndert'); ?></p>
            </div>

        <?php } else { ?>
            <div class="page-form clearfix">
                <div class="page-form-box">
                    <?php $form = ActiveForm::begin([
                        'id' => 'password-restore-step2-form',
                        'fieldConfig'=>['template'=>'{input}'],
                        'enableClientValidation'=>false,
                        'labelInPlaceholder'=>true
                    ]); ?>

                    <?= $form->errorSummary($model); ?>

                    <div class="field-input-box">
                        <?= $form->field($model,'password')->passwordInput(); ?>
                    </div>

                    <div class="field-input-box">
                        <?= $form->field($model,'password_repeat')->passwordInput(); ?>
                    </div>

                    <button type="submit" class="btn btn-submit"><?= Yii::t('app','Absenden'); ?></button>

                    <?php ActiveForm::end(); ?>
                </div>

            </div>
        <?php } ?>

    </div>
</div>