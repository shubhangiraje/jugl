<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>

<div class="admin-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
	
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
          <?= $form->field($model, 'question_de')->textInput(['maxlength' => 256]) ?>
          <?= $form->field($model, 'response_de')->textarea(['rows' => 10]) ?>
        </div>
        <div role="tabpanel" class="tab-pane" id="tab2">
            <?= $form->field($model, 'question_en')->textInput(['maxlength' => 256]) ?>
            <?= $form->field($model, 'response_en')->textarea(['rows' => 10]) ?>
        </div>
        <div role="tabpanel" class="tab-pane" id="tab3">
            <?= $form->field($model, 'question_ru')->textInput(['maxlength' => 256]) ?>
			<?= $form->field($model, 'response_ru')->textarea(['rows' => 10]) ?>
        </div>
    </div>

   

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
