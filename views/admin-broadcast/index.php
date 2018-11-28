<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\password\PasswordInput;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<h1><?= Yii::t('app', 'Send message to all users')?></h1>
    <div class="user-form">

        <?php if (Yii::$app->session->hasFlash('result')) { ?>
            <p class="bg-success" style="padding: 15px;">
                <?=Yii::$app->session->getFlash('result')?>
            </p>
        <?php } ?>

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

        <?= $form->field($model, 'type')->dropDownList(\app\models\AdminBroadcastMessageForm::getTypeList()) ?>

        <?= $form->field($model, 'text')->textarea(['rows'=>5]) ?>

        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
