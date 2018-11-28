<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\ImageIdWidget;

?>

    <div class="admin-form">

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

     <?= $form->field($model, 'name')->textInput(['maxlength' => 256]) ?>
		<?= $form->field($model, 'bonus')->textInput(['maxlength' => 256]) ?>
<div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
            </div>
        </div>

      
        <?php ActiveForm::end(); ?>

    </div>
