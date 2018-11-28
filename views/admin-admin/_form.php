<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\password\PasswordInput;

/* @var $this yii\web\View */
/* @var $model app\modules\foms\models\Admin */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="admin-form">

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

        <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>

        <?= $form->field($model, 'type')->dropDownList($model->getTypeList(),['onchange'=>'toggleAccessFields()','id'=>"type"]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => 256]) ?>

        <?= $form->field($model, 'first_name')->textInput(['maxlength' => 256]) ?>

        <?= $form->field($model, 'last_name')->textInput(['maxlength' => 256]) ?>

        <?= $form->field($model, 'plainPassword')->widget(PasswordInput::classname(),['language'=>Yii::$app->language]); ?>

        <?= $form->field($model, 'access_translator',[
			'template'=>"{label}\n<div class=\"col-sm-6\" style='padding-top:7px;'>{input}</div>",
			'labelOptions'=>['class'=>'col-sm-3 control-label'],
		])->checkbox([],false) ?>
        

        <div id="access_fields" style="display:none;">
            <?= $form->field($model, 'access_dashboard')->checkbox() ?>
            <?= $form->field($model, 'access_user_view')->checkbox() ?>
            <?= $form->field($model, 'access_user_update')->checkbox() ?>
            <?= $form->field($model, 'access_user_validation')->checkbox() ?>
            <?= $form->field($model, 'access_payouts')->checkbox() ?>
            <?= $form->field($model, 'access_interests')->checkbox() ?>
            <?= $form->field($model, 'access_search_request_view')->checkbox() ?>
            <?= $form->field($model, 'access_search_request_validate')->checkbox() ?>
            <?= $form->field($model, 'access_search_request_update')->checkbox() ?>
            <?= $form->field($model, 'access_offer_view')->checkbox() ?>
            <?= $form->field($model, 'access_offer_validate')->checkbox() ?>
            <?= $form->field($model, 'access_offer_update')->checkbox() ?>
            <?= $form->field($model, 'access_settings')->checkbox() ?>
            <?= $form->field($model, 'access_broadcast')->checkbox() ?>
            <?= $form->field($model, 'access_news')->checkbox() ?>
        </div>

<?php ob_start(); ?>
        <script>
            window.toggleAccessFields=function() {
                if ($('#type option:selected').val()=='MANAGER') {
                    $('#access_fields').show();
                } else {
                    $('#access_fields').hide();
                }
            }

            toggleAccessFields();
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
