<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\ImageIdWidget;

$this->title=Yii::t('app','Profilbild').' "'.$model->name.'"';

$this->params['breadcrumbs']=[
    ['label'=>Yii::t('app','Users'), 'url'=>['admin-user/index']],
    ['label'=>Yii::t('app',$model->name), 'url'=>['admin-user/update', 'id'=>$model->id]],
    ['label'=>$this->title]
];

?>

<div class="admin-update-photo">

    <h1><?php echo Html::encode($this->title); ?></h1>

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <?=$form->field($model, 'avatar_file_id')->widget(ImageIdWidget::className())?>

    <hr>

    <?php foreach($userPhotos as $k=>$userPhoto) {
        echo $form->field($userPhoto, "[$k]file_id")->widget(\app\components\ImageIdWidget::className());
    } ?>

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>


</div>
