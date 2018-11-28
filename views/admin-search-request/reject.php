<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Offer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <div class="col-sm-12">
        <?php if($searchRequest->user->status==\app\models\User::STATUS_BLOCKED) { ?>
            <label class="text-danger"><?= Yii::t('app', 'Nutzer Blockiert')?>: <?= $searchRequest->user->name ?></label>
        <?php } else { ?>
            <label><?= Yii::t('app', 'Nutzer')?>: <?= $searchRequest->user->name ?></label>
            <?= Html::button(Yii::t('app', 'Vorrübergehend deaktivieren'), [
                'onclick' => '
                 var thisBtn = $(this);
                 thisBtn.attr("disabled", "disabled");      
                 $.post("/admin-user/modal-block-user?id='.$searchRequest->user->id.'", function(data){
                    thisBtn.attr("disabled", "disabled");
                    thisBtn.text("Blockiert");  
                 });
            ',
                'class' => 'pull-right btn btn-danger
        ']) ?>
        <?php }?>
    </div>

    <div class="col-sm-12">
        <?php $form = ActiveForm::begin([
            'id'=>'search-request-validation-reject-from'
        ]); ?>

        <?= $form->field($model, 'default_text_id')->widget(\kartik\select2\Select2::classname(), [
            'data' => \app\models\DefaultText::getDefaultTextList(\app\models\DefaultText::SEARCH_REQUEST_VALIDATION_REJECTED),
            'options' => [
                'placeholder' => '',
                'id'=>'search-request-validation-reject-select',
                'onchange'=>'
                    var data = $(this).select2("data");
                    var text = data[0].text;                   
                    $("#search-request-validation-reject-from").find("#defaulttext-default_text_edit").val(text);
                '
            ],
            'hideSearch' => true,
        ]) ?>

        <?= $form->field($model, 'default_text_edit')->textarea(['rows'=>5]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Ok'), ['class' => 'pull-right btn btn-primary', 'style'=>'margin-left: 10px']) ?>
            <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'pull-right btn btn-default', 'data-dismiss'=>'modal']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>


<?php

$script = <<< JS

$('#search-request-validation-reject-from').on('beforeSubmit', function(e) {
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