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
            <label class="control-label col-sm-3"><?=$model->getEncodedAttributeLabel('status')?></label>
            <div class="col-sm-6">
                <div class="control-value"><?=Html::encode($model->statusLabel)?></div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3"><?=$model->getEncodedAttributeLabel('votes_up')?></label>
            <div class="col-sm-6">
                <div class="control-value"><?=$model->votes_up?></div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3"><?=$model->getEncodedAttributeLabel('votes_down')?></label>
            <div class="col-sm-6">
                <div class="control-value"><?=$model->votes_down?></div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3">Kommentare insgesamt</label>
            <div class="col-sm-6">
                <div class="control-value"><?=intval($model->groupChatUser->group_chat_messages_count)?></div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3">Nutzer</label>
            <div class="col-sm-6">
                <div class="control-value"><?=Html::a($model->user->name,['admin-user/update','id'=>$model->user_id])?></div>
            </div>
        </div>

        <?=$form->field($model, 'file_id')->widget(ImageIdWidget::className())?>

        <!--
        \kartik\date\DatePicker::widget([
        'model'=>$searchModel,
        'attribute'=>'create_dt_from',
        'readonly'=>true,
        'pluginOptions'=>[
        'clearBtn'=>true
        ],
        'options'=>['style'=>'width:100px;background:white;cursor:pointer;'],
        'addon'=>['<span style="display:inline-block;width:35px;">'.Yii::t('app','From').'</span>'],
        ]).
        -->
        <?= $form->field($model, 'text')->textarea(['rows' => 10]) ?>

        <label class="control-label col-sm-3"><b><?= Yii::t('app', 'An wen gepostet') ?></b></label>
        <?= $form->field($model, 'visible_for_all')->checkbox() ?>
        <?= $form->field($model, 'visible_for_followers')->checkbox() ?>
        <?= $form->field($model, 'visible_for_contacts')->checkbox() ?>

        <?= $form->field($model, 'trollbox_category_id')->dropDownList(\app\models\TrollboxCategory::getFrontList(), ['prompt'=>Yii::t('app', 'Alle')]) ?>

        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
