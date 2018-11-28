<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\ImageIdWidget;

/* @var $this yii\web\View */
/* @var $model app\models\InfoComment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="info-comment-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
