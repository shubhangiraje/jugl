<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;


?>

<div class="admin-default-text-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?php

        $placeholder = '';
        switch ($_REQUEST['category']) {
            case \app\models\DefaultText::SEARCH_REQUEST_DELETE:
                $placeholder = \app\models\DefaultText::PLACEHOLDER_SEARCH_REQUEST_DELETE;
                break;
            case \app\models\DefaultText::OFFER_DELETE:
                $placeholder = \app\models\DefaultText::PLACEHOLDER_OFFER_DELETE;
                break;
        }

    ?>

    <?= $form->field($model, 'text')->textarea(['rows'=>7, 'placeholder'=>$placeholder]) ?>

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
