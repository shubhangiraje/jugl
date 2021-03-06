<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\models\Bill */
/* @var $form yii\widgets\ActiveForm */
?>


    <div class="search-request-delete-form" style="overflow: hidden">
        <div class="row">
            <div class="col-sm-12">
                <?php $form = ActiveForm::begin([
                    'id'=>$model->formName(),
                ]); ?>

                <?= $form->field($model, 'default_text_id')->widget(\kartik\select2\Select2::classname(), [
                    'data' => \app\models\DefaultText::getDefaultTextList(\app\models\DefaultText::SEARCH_REQUEST_DELETE),
                    'options' => ['placeholder' => ''],
                    'hideSearch' => true,
                ]); ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Ok'), ['class' => 'pull-right btn btn-primary', 'style'=>'margin-left: 10px']) ?>
                    <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'pull-right btn btn-default', 'data-dismiss'=>'modal']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

<?php

$script = <<< JS

$('form#{$model->formName()}').on('beforeSubmit', function(e) {
    var form = $(this);
    $.post(form.attr('action'), form.serialize()).done(function(result) {
        if(!result) {
            $(form).trigger('reset');
        }
    });
    return false;    
});

JS;
$this->registerJs($script);
?>