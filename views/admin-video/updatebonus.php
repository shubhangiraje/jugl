<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\password\PasswordInput;
use kartik\datecontrol\DateControl;

?>
	
	<h1><?= Yii::t('app', 'Updating Bonus')?></h1>
    <div class="user-form">

        <?php if (Yii::$app->session->hasFlash('result')) { ?>
            <p class="bg-success" style="padding: 15px;">
                <?=Yii::$app->session->getFlash('result')?>
            </p>
        <?php } ?>

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

        <?= $form->field($model, 'type')->dropDownList(\app\models\AdminVideoUpdateBonusForm::getTypeList()) ?>

        <?= $form->field($model, 'input')->input('text', ['placeholder' => "z.B 0.10"]) ?>

        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
