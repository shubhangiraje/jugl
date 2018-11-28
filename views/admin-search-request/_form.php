<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<?php Modal::begin([
    'id'=> 'modal-search-request-accept',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Suchauftrag freigegeben').'</h4>',
]); ?>
<div id="modal-search-request-accept-content"></div>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id'=> 'modal-search-request-reject',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Suchauftrag abgelehnt').'</h4>',
]); ?>
<div id="modal-search-request-reject-content"></div>
<?php Modal::end(); ?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>

    <?= $form->field($model, 'validation_status')->dropDownList($model->getValidationStatusList()) ?>
	
	<?= $form->field($model, 'search_request_type')->dropDownList($model->getSearchRequestTypeList()) ?>
	 <?= $form->field($model, 'provider_id')->dropDownList([''=>'--bitte auswählen--']+$model->getProviderList(),['options'=>array($model->provider_id => array('selected'=>true))]) ?>

    <div class="form-group">
        <label class="control-label col-sm-3"><?=Yii::t('app','Users')?></label>
        <div class="col-sm-6">
            <div class="control-value"><?=Html::encode($model->user->name)?></div>
        </div>
    </div>

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



    <?php /* ?>
    <?php
    $data=[];
    if (count($model->searchRequestInterests)>0) {
        $data['level1Interest']=strval($model->searchRequestInterests[0]->level1Interest);
        $data['level2Interest']=strval($model->searchRequestInterests[0]->level2Interest);

        $level3Interests=[];
        foreach($model->searchRequestInterests as $sri) {
            $level3Interests[]=$sri->level3Interest;
        }
        $data['level3Interests']=implode(', ',$level3Interests);
    }

    ?>

    <div class="form-group">
        <label class="control-label col-sm-3"><?=Yii::t('app','Interests')?></label>
        <div class="col-sm-6">
            <div class="control-value"><?=Html::encode($data['level1Interest'])?>
                <?php if ($data['level2Interest']) { ?>
                    &gt; <?=Html::encode($data['level2Interest'])?>
                    <?php if ($data['level3Interests']) { ?>
                        &gt; <?=Html::encode($data['level3Interests'])?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php foreach($model->searchRequestParamValues as $pv) {?>
        <div class="form-group">
            <label class="control-label col-sm-3"><?=Html::encode($pv->param->title)?></label>
            <div class="col-sm-6">
                <div class="control-value"><?=Html::encode($pv->paramValue ? $pv->paramValue->title:$pv->param_value)?></div>
            </div>
        </div>
    <?php } ?>

    <?php */ ?>

    <?php if ($model->status==\app\models\SearchRequest::STATUS_SCHEDULED) { ?>
        <div class="form-group">
            <label class="col-sm-3 text-right">Datum der Veröffentlichung</label>
            <div class="col-sm-6">
                <?= (new \app\components\EDateTime($model->scheduled_dt))->format('d.m.Y H:i') ?>
            </div>
        </div>
    <?php } ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 200]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 10]) ?>

    <?php /* if (count($model->files)>0) { ?>
        <div class="form-group">
                <div class="col-sm-9 col-sm-offset-3">
                    <?php foreach($model->files as $file) { ?>
                        <a target="_blank" href="<?=$file->url?>"><img style="margin: 0 10px 10px 0" class="img-thumbnail" src="<?=$file->getThumbUrl('searchRequestMobile')?>"/></a>
                    <?php } ?>
                </div>
        </div>
    <?php } */ ?>

    <?= $form->field($model, 'price_from')->textInput() ?>
    <?= $form->field($model, 'price_to')->textInput() ?>
    <?= $form->field($model, 'bonus')->textInput() ?>
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
			<ul class="nav nav-tabs" role="tablist">
				<li class="active"><a href="#tab1" role="tab" data-toggle="tab"><?=Yii::t('app','DE')?></a></li>
				<li><a href="#tab2" role="tab" data-toggle="tab"><?=Yii::t('app','EN')?></a></li>
				<li><a href="#tab3" role="tab" data-toggle="tab"><?=Yii::t('app','RU')?></a></li>
			</ul>
		</div>
	</div>
	<br>
	
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="tab1">
			<?= $form->field($model, 'feedback_text_de')->textarea(['rows' => 10]) ?>
		</div>
		<div role="tabpanel" class="tab-pane" id="tab2">
			<?= $form->field($model, 'feedback_text_en')->textarea(['rows' => 10]) ?>
		</div>
		<div role="tabpanel" class="tab-pane" id="tab3">
			<?= $form->field($model, 'feedback_text_ru')->textarea(['rows' => 10]) ?>
		</div>
	</div>
    <?php foreach($searchRequestFiles as $k=>$searchRequestFile) { ?>
        <?=$form->field($searchRequestFile, "[$k]file_id")->widget(\app\components\ImageIdWidget::className())?>
    <?php } ?>

    <?= $form->field($model, 'country_id')->dropDownList(\app\models\Country::getList()) ?>
    <?= $form->field($model, 'zip')->textInput() ?>
    <?= $form->field($model, 'city')->textInput() ?>
    <?= $form->field($model, 'address')->textInput() ?>

    <?=$form->field($model,'active_till',['template'=>"{label}\n<div class=\"col-sm-2\">{input}</div>"])->widget(\kartik\datecontrol\DateControl::classname(), [
        'type'=>\kartik\datecontrol\DateControl::FORMAT_DATE,
        'ajaxConversion'=>false,
        'options' => [
            'pluginOptions' => [
                'autoclose' => true
            ],
            'removeButton'=>false
        ]
    ])
    ?>

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            <?php $_POST['OK']=1;?>

            <?php if(hasCurrentActionPostAccess()) { ?>
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
            <?php } ?>

            <?php if(hasAccess('admin-search-request/control') && in_array($model->validation_status,[\app\models\SearchRequest::VALIDATION_STATUS_AWAITING,\app\models\Offer::VALIDATION_STATUS_AWAITING_LATER])) { ?>

                <?= Html::button(Yii::t('app', 'Annehmen'), [
                    'onclick' => '$("#modal-search-request-accept").modal("show").find("#modal-search-request-accept-content").load($(this).attr("value"));',
                    'value'=>\yii\helpers\Url::to(['admin-search-request/accept', 'id'=>$model->id]),
                    'class' => 'btn btn-success',
                    'style'=>'margin-left:10px;'
                ]); ?>

                <?= Html::button(Yii::t('app', 'Ablehnen'), [
                    'onclick' => '$("#modal-search-request-reject").modal("show").find("#modal-search-request-reject-content").load($(this).attr("value"));',
                    'value'=>\yii\helpers\Url::to(['admin-search-request/reject', 'id'=>$model->id]),
                    'class' => 'btn btn-danger',
                    'style'=>'margin-left:10px;'
                ]); ?>


            <?php } ?>

        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
