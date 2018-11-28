<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\modules\foms\models\Admin */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="admin-form">

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

        <?= $form->field($model, 'param_id',['wrapperOptions'=>['class'=>'col-sm-2']])->dropDownList($model->interest->getParentsParams(),['id'=>'param_id','prompt'=>Yii::t('app','')]) ?>

        <?=$form->field($model, 'param_value_id',['wrapperOptions'=>['class'=>'col-sm-2']])->widget(\kartik\depdrop\DepDrop::classname(), [
            'data'=>$model->param ? $model->param->getParamValuesList():[],
            'pluginOptions'=>[
                'depends'=>['param_id'],
                'placeholder'=>Yii::t('app',''),
                'url'=>Url::to(['param-values'])
            ]
        ]);
        ?>
        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
