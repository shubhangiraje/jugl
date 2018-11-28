<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\password\PasswordInput;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="user-form">

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

        <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>

        <div class="form-group">
            <label class="control-label col-sm-3"><?=Yii::t('app','Users')?></label>
            <div class="col-sm-6">
                <div class="control-value"><?=Html::encode($model->user->name)?></div>
            </div>
        </div>

        <?= $form->field($model, 'description')->textarea(['rows' => 10]) ?>

        <hr>
        <?= $form->field($model, 'pay_status')->dropDownList(\app\components\Helper::addEmptyValue($model->getPayStatusList()))?>
        <?= $form->field($model, 'pay_tx_id')?>
        <?= $form->field($model, 'pay_method')->dropDownList(\app\components\Helper::addEmptyValue($model->getPayMethodList()))?>
        <?= $form->field($model, 'pay_data')?>
        <?= $form->field($model, 'delivery_address')?>

        <?php if ($model->offer->type==\app\models\Offer::TYPE_AUCTION) { ?>
            <?=$form->field($model,'bet_price') ?>
            <?=$form->field($model,'bet_period') ?>
            <?=$form->field($model,'bet_active_till') ?>
        <?php } ?>

        <?php if(hasCurrentActionPostAccess()) { ?>
        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
            </div>
        </div>
        <?php } ?>

        <?php ActiveForm::end(); ?>

    </div>
