<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\ImageIdWidget;

?>

    <div class="admin-form">

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<?php /*
        <?=$form->field($model, 'image_file_id')->widget(ImageIdWidget::className())?>

        <?=$form->field($model, 'dt')->widget(kartik\datecontrol\DateControl::classname(),['options'=>['options'=>['style'=>'width:100px']]])?>
*/ ?>
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

        <?= $form->field($model, 'show')->checkbox() ?>

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
				<?= $form->field($model, 'title_de')->textInput(['maxlength' => 256]) ?>
				<?= $form->field($model, 'description_de')->textarea(['rows' => 10]) ?>
			</div>
			<div role="tabpanel" class="tab-pane" id="tab2">
				<?= $form->field($model, 'title_en')->textInput(['maxlength' => 256]) ?>
				<?= $form->field($model, 'description_en')->textarea(['rows' => 10]) ?>
			</div>
			<div role="tabpanel" class="tab-pane" id="tab3">
				<?= $form->field($model, 'title_ru')->textInput(['maxlength' => 256]) ?>
				<?= $form->field($model, 'description_ru')->textarea(['rows' => 10]) ?>
			</div>
		</div>

        <div class="form-group">
            <label class="control-label col-sm-3"><?=\app\models\TokenDepositGuarantee::getEncodedAttributeLabel('sum')?></label>
            <div class="col-sm-6">
                <div class="control-value"><?=Html::encode($model->sum)?></div>
            </div>
        </div>

        <?= $form->field($model, 'sum_cost')->textInput() ?>

        <?php foreach($TDGFiles as $k=>$TDGFile) { ?>
            <?=$form->field($TDGFile, "[$k]file_id")->widget(\app\components\ImageIdWidget::className())?>
        <?php } ?>

        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
