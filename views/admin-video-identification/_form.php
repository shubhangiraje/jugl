<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\ImageIdWidget;

?>

<div class="admin-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <div class="form-group">
        <label class="control-label col-sm-3"><?=$model->getEncodedAttributeLabel('dt')?></label>
        <div class="col-sm-6">
            <div class="control-value"><?=Html::encode((new \app\components\EDateTime($model->dt)))?></div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3"><?= Yii::t('app', 'Status') ?></label>
        <div class="col-sm-6">
            <?= Html::dropDownList('video_identification_status', $model->user->video_identification_status, \app\models\User::getVideoIdentificationStatusList(), [
                'class'=>'form-control'
            ]) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3"><?=Yii::t('app', 'Echt')?></label>
        <div class="col-sm-6">
            <div class="control-value"><?=$model->votes_up?></div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3"><?=Yii::t('app', 'Nicht Echt')?></label>
        <div class="col-sm-6">
            <div class="control-value"><?=$model->votes_down?></div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3"><?=Yii::t('app', 'Kommentare insgesamt')?></label>
        <div class="col-sm-6">
            <div class="control-value"><?=intval($model->groupChatUser->group_chat_messages_count)?></div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3"><?= Yii::t('app', 'Nutzer') ?></label>
        <div class="col-sm-6">
            <div class="control-value"><?=Html::a($model->user->name,['admin-user/update','id'=>$model->user_id])?></div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3"><?= Yii::t('app', 'Video') ?></label>
        <div class="col-sm-6">
            <div class="control-value">
                <?= Html::a(Html::img($model->file->getThumbUrl('adminImagePreview')), $model->file->url, ['target'=>'_blank', 'class'=>'video-link']) ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
