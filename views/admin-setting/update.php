<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\Ckeditor;

$this->title=Yii::t('app','Updating Setting').' "'.$model.'"';

$this->params['breadcrumbs']=[
    [
        'label'=>Yii::t('app','Settings'),
        'url'=>['admin-setting/index']
    ],
    [
        'label'=>$this->title,
    ]
];

?>

<div class="admin-update">

    <h2><?= Html::encode($model->title) ?></h2>
    <br>

    <div class="admin-form">

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

        <?php
            switch ($model->type) {
                case \app\models\Setting::TYPE_BOOL:
                    echo $form->field($model, 'value')->checkbox();
                    break;
                case \app\models\Setting::TYPE_STRING:

                    if(preg_match('%^DASHBOARD_FORUM_TEXT%',$model->name)) {
                        echo $form->field($model, 'value')->widget(Ckeditor::className(), [
                            'clientOptions'=>[
                                'height'=>'200'
                            ]
                        ]);
                    } else {
                        echo $form->field($model, 'value')->textarea(['rows' => 4]);
                    }

                    break;
                default:
                    echo $form->field($model, 'value')->textInput(['maxlength' => 256]);
            }
        ?>

        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>