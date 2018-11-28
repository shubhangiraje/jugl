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

        <?php foreach($model->searchRequest->searchRequestParamValues as $pv) {
                $match=false;
                foreach($model->searchRequestOfferParamValues as $pvv) {
                    if ($pvv->param_id==$pv->param_id and $pvv->match) $match=true;
                }
            ?>

            <div class="form-group">
                <label class="control-label col-sm-3"><?=Html::encode($pv->param->title)?></label>
                <div class="col-sm-6">
                    <div class="control-value"><?=Html::encode($pv->paramValue ? $pv->paramValue->title:$pv->param_value)?> <b><?=$match ? 'Ja':'Nein'?></b> </div>
                </div>
            </div>
        <?php } ?>

        <div class="form-group">
            <label class="control-label col-sm-3"><?=\app\models\SearchRequestOffer::getEncodedAttributeLabel('relevancy')?></label>
            <div class="col-sm-6">
                <div class="control-value"><?=$model->relevancy?>%</div>
            </div>
        </div>

        <?= $form->field($model, 'description')->textarea(['rows' => 10]) ?>

        <?php if (count($model->files)>0) { ?>
            <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?php foreach($model->files as $file) { ?>
                            <a target="_blank" href="<?=$file->url?>"><img style="margin: 0 10px 10px 0" class="img-thumbnail" src="<?=$file->getThumbUrl('searchRequestMobile')?>"/></a>
                        <?php } ?>
                    </div>
            </div>
        <?php } ?>

        <?= $form->field($model, 'price_from')->textInput() ?>
        <?= $form->field($model, 'price_to')->textInput() ?>

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
