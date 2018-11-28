<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\ImageIdWidget;
use app\models\Country;
use app\models\InterestSelection;

?>

    <div class="admin-form">
	 <hr />
		<h3 style="margin-top:20px; margin-bottom:20px; text-align:center;"><?= Yii::t('app', 'Daten zum Anbieter') ?></h3>
			 <hr />
        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
		<?= $form->field($model, 'status')->dropDownList(\app\models\AdminAdvertisingForm::getStatusList()) ?>
     <?= $form->field($model, 'advertising_name')->textInput(['maxlength' => 255]) ?>
	 <?= $form->field($model, 'advertising_display_name')->textInput(['maxlength' => 255]) ?>
	 <?= $form->field($model, 'provider')->dropDownList($model->getProviderList()) ?>
	 <?= $form->field($model, 'advertising_total_bonus')->textInput(['maxlength' => 10]) ?>
	 <?= $form->field($model, 'advertising_total_views')->textInput(['maxlength' => 10]) ?>
	 <?= $form->field($model, 'advertising_total_clicks')->textInput(['maxlength' => 10]) ?>
	 <hr />
<h3 style="margin-top:20px; margin-bottom:20px; text-align:center;"><?= Yii::t('app', 'Daten zum Nutzer') ?></h3>
	 <hr />
	 <?= $form->field($model, 'user_bonus')->textInput(['maxlength' => 255]) ?>
	 <?= $form->field($model, 'click_interval')->textInput() ?>
	  <?=$form->field($isModel, 'level1Interest_id')->dropDownList($isModel->getLevel1List(), ['id'=>'level1-id','prompt'=>'']);?>

	  <?= $form->field($isModel, 'level2Interest_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'options'=>['id'=>'level2-id'],
        'data'=>$isModel->level2Interest_id ? $isModel->getNestedLevelList($isModel->level1Interest_id) : array_merge([''=>''],$isModel->getNestedLevelList($isModel->level1Interest_id)),
        'pluginOptions'=>[
            'depends'=>['level1-id'],
            'placeholder'=>' ',
            'url'=>\yii\helpers\Url::to(['interest-nested-level2'])
        ]
    ]); ?>

    <?= $form->field($isModel, 'level3Interest_ids')->widget(\kartik\depdrop\DepDrop::classname(), [
        'options'=>['id'=>'level3-id','multiple'=>true],
        //'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
        'data'=>$isModel->getNestedLevelList($isModel->level2Interest_id),
        'pluginOptions'=>[
            'depends'=>['level1-id','level2-id'],
            'placeholder'=>' ',
            'url'=>\yii\helpers\Url::to(['interest-nested-level3'])
        ]
    ]); ?>
	  
    <?php foreach($model->searchRequestParamValues as $pv) {?>
        <div class="form-group">
            <label class="control-label col-sm-3"><?=Html::encode($pv->param->title)?></label>
            <div class="col-sm-6">
                <div class="control-value"><?=Html::encode($pv->paramValue ? $pv->paramValue->title:$pv->param_value)?></div>
            </div>
        </div>
    <?php } ?>

    <style>
        .type_auction, .type_autosell, .type_ad {
            display: none;
        }
    </style>
	 <hr />
<h3 style="margin-top:20px; margin-bottom:20px; text-align:center;"><?= Yii::t('app', 'Daten zur Werbung') ?></h3>
<hr />
	  <?= $form->field($model, 'advertising_type')->dropDownList($model->getAdvertisingTypeList()) ?>
	 <?= $form->field($model, 'banner')->textInput() ?>
	 <?= $form->field($model, 'banner_width')->textInput() ?>
	 <?= $form->field($model, 'banner_height')->textInput() ?>
	 <?= $form->field($model, 'link')->textInput(['maxlength' => 255]) ?>
	 <?= $form->field($model, 'advertising_position')->dropDownList($model->getAdvertisingPositionList()) ?>
	 <?= $form->field($model, 'popup_interval')->textInput() ?>
	 <?=$form->field($model,'release_date',['template'=>"{label}\n<div class=\"col-sm-6\">{input}</div>"])->widget(\kartik\datecontrol\DateControl::classname(), [
        'type'=>\kartik\datecontrol\DateControl::FORMAT_DATETIME,
        'ajaxConversion'=>false,
        'options' => [
            'pluginOptions' => [
                'autoclose' => true
            ],
            'removeButton'=>false
        ]
    ])
    ?>
	  <?=$form->field($model,'display_date',['template'=>"{label}\n<div class=\"col-sm-6\">{input}</div>"])->widget(\kartik\datecontrol\DateControl::classname(), [
        'type'=>\kartik\datecontrol\DateControl::FORMAT_DATETIME,
        'ajaxConversion'=>false,
        'options' => [
            'pluginOptions' => [
                'autoclose' => true
            ],
            'removeButton'=>false
        ]
    ])
    ?>
	
	<?= $form->field($model, 'country_id')->dropDownList(Country::getList(),['options'=>array($model->country_id => array('selected'=>true))]) ?>



	
	 
<div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
            </div>
        </div>

      
        <?php ActiveForm::end(); ?>

    </div>
