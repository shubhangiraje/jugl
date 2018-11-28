<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\ImageIdWidget;

/* @var $this yii\web\View */
/* @var $model app\modules\foms\models\Admin */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="admin-form">

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

        <?= $form->field($model, 'title')->textInput() ?>

        <?= $form->field($model, 'type',['wrapperOptions'=>['class'=>'col-sm-2']])->dropDownList(\app\models\Param::getTypeList(),['prompt'=>Yii::t('app','')])?>

        <?= $form->field($model, 'required')->checkbox() ?>

        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
