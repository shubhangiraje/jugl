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

        <?= $form->field($model, 'sex')->dropDownList($model->getSexList()) ?>

        <?= $form->field($model, 'first_name')->textInput(['maxlength' => 256]) ?>

        <?= $form->field($model, 'last_name')->textInput(['maxlength' => 256]) ?>

        <?= $form->field($model, 'nick_name')->textInput(['maxlength' => 256]) ?>

        <?= $form->field($model, 'birthday')->widget(DateControl::classname(),['type'=>DateControl::FORMAT_DATE]); ?>

        <?= $form->field($model, 'phone')->textInput(['maxlength' => 64]) ?>

        <?= $form->field($model, 'street')->textInput(['maxlength' => 64]) ?>

        <?= $form->field($model, 'house_number')->textInput(['maxlength' => 64]) ?>

        <?= $form->field($model, 'zip')->textInput(['maxlength' => 64]) ?>

        <?= $form->field($model, 'city')->textInput(['maxlength' => 64]) ?>

        <hr/>

        <?= $form->field($model, 'validation_type')->dropDownList($model->getValidationTypeList(),['prompt'=>Yii::t('app','-- Please select --')]) ?>

        <?= $form->field($model, 'validation_status')->dropDownList($model->getValidationStatusList(),['onclick'=>'toggleValidationFailureReasonField()','id'=>'validation-status']) ?>

        <div id="validation-failure-reason" style="display:none">
            <?= $form->field($model, 'validation_failure_reason')->textarea(['rows'=>10, 'maxlength' => 2000]) ?>
        </div>

        <?php if ($model->validationPhoto1File || $model->validationPhoto2File) { ?>
            <div class="form-group">
                 <div class="col-sm-9 col-sm-offset-3">
                     <?php if ($model->validationPhoto1File) { ?>
                         <a style="margin-right:15px;" target="_blank" href="<?=$model->validationPhoto1File->url?>"><img class="img-thumbnail" src="<?=$model->validationPhoto1File->getThumbUrl('validationSmall')?>"/></a>
                     <?php } ?>
                     <?php if ($model->validationPhoto2File) { ?>
                         <a target="_blank" href="<?=$model->validationPhoto2File->url?>"><img class="img-thumbnail" src="<?=$model->validationPhoto2File->getThumbUrl('validationSmall')?>"/></a>
                     <?php } ?>
                     <?php if ($model->validationPhoto3File) { ?>
                         <a style="margin-left:15px;" target="_blank" href="<?=$model->validationPhoto3File->url?>"><img class="img-thumbnail" src="<?=$model->validationPhoto3File->getThumbUrl('validationSmall')?>"/></a>
                     <?php } ?>
                 </div>
            </div>
        <?php } ?>

        <?php if(!empty($model->validation_changelog)) { ?>
            <?= $form->field($model, 'validation_changelog')->textarea([
                'rows'=>10,
                'readonly'=>true
            ]) ?>
        <?php } ?>

        <?= $form->field($model, 'validation_details')->textarea() ?>

        <?php ob_start(); ?>
        <script>
            window.toggleValidationFailureReasonField=function() {
                if ($('#validation-status option:selected').val()=='FAILURE') {
                    $('#validation-failure-reason').show();
                } else {
                    $('#validation-failure-reason').hide();
                }
            }

            toggleValidationFailureReasonField();
        </script>
        <?php $this->registerJs(preg_replace('%</?script>%','',ob_get_clean()))?>

        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
