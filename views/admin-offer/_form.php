<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\password\PasswordInput;
use kartik\datecontrol\DateControl;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<?php Modal::begin([
    'id'=> 'modal-offer-accept',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Werbung freigegeben').'</h4>',
]); ?>
<div id="modal-offer-accept-content"></div>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id'=> 'modal-offer-reject',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Werbung abgelehnt').'</h4>',
]); ?>
<div id="modal-offer-reject-content"></div>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id'=> 'modal-notify-update-category',
    'header' => '<h4 class="modal-title">'.Yii::t('app', 'Kategorie ändern').'</h4>',
]); ?>
<div id="modal-notify-update-category-content"></div>
<?php Modal::end(); ?>


<div class="user-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>

    <?= $form->field($model, 'validation_status')->dropDownList($model->getValidationStatusList()) ?>


    <div class="form-group">
        <label class="control-label col-sm-3"><?=Yii::t('app','Users')?></label>
        <div class="col-sm-6">
            <div class="control-value"><?=Html::encode($model->user->name)?></div>
        </div>
    </div>
    <?php /*
<?php
$data=[];
if (count($model->offerInterests)>0) {
$data['level1Interest']=strval($model->offerInterests[0]->level1Interest);
$data['level2Interest']=strval($model->offerInterests[0]->level2Interest);

$level3Interests=[];
foreach($model->offerInterests as $sri) {
    $level3Interests[]=$sri->level3Interest;
}
$data['level3Interests']=implode(', ',$level3Interests);
}

?>
    <div class="form-group">
        <label class="control-label col-sm-3"><?=Yii::t('app','Interests')?></label>
        <div class="col-sm-6">
            <div class="control-value"><?=Html::encode($data['level1Interest'])?>
                <?php if ($data['level2Interest']) { ?>
                    &gt; <?=Html::encode($data['level2Interest'])?>
                    <?php if ($data['level3Interests']) { ?>
                        &gt; <?=Html::encode($data['level3Interests'])?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
<?php */ ?>

    <?=$form->field($isModel, 'level1Interest_id')->dropDownList($isModel->getLevel1List(), ['id'=>'level1-id','prompt'=>'']);?>

    <?php
        echo $form->field($isModel, 'level2Interest_id')->widget(\kartik\depdrop\DepDrop::classname(), [
            'options'=>['id'=>'level2-id'],
            'data'=>$isModel->level2Interest_id ? $isModel->getNestedLevelList($isModel->level1Interest_id) : array_merge([''=>''],$isModel->getNestedLevelList($isModel->level1Interest_id)),
            'pluginOptions'=>[
                'depends'=>['level1-id'],
                'placeholder'=>' ',
                'url'=>\yii\helpers\Url::to(['interest-nested-level2'])
            ]
        ]);
    ?>

    <?php
        echo $form->field($isModel, 'level3Interest_ids')->widget(\kartik\depdrop\DepDrop::classname(), [
            'options'=>['id'=>'level3-id','multiple'=>true],
            //'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
            'data'=>$isModel->getNestedLevelList($isModel->level2Interest_id),
            'pluginOptions'=>[
                'depends'=>['level1-id','level2-id'],
                'placeholder'=>' ',
                'url'=>\yii\helpers\Url::to(['interest-nested-level3'])
            ]
        ]);
    ?>
    <?php foreach($model->offerParamValues as $pv) {?>
        <div class="form-group">
            <label class="control-label col-sm-3"><?=Html::encode($pv->param->title)?></label>
            <div class="col-sm-6">
                <div class="control-value"><?=Html::encode($pv->paramValue ? $pv->paramValue->title:$pv->param_value)?></div>
            </div>
        </div>
    <?php } ?>

    <style>
        .type_auction, .type_autosell, .type_ad {
            display: none;
        }
    </style>


    <div class="form-group" style="margin-bottom: 30px">
        <div class="col-sm-6 col-sm-offset-3 text-right">
            <?= Html::button(Yii::t('app', 'User benachrichtigen'), [
                'onclick' => '$("#modal-notify-update-category").modal("show").find("#modal-notify-update-category-content").load($(this).attr("value"));',
                'value'=>\yii\helpers\Url::to(['admin-offer/notify-update-category', 'id'=>$model->id]),
                'class' => 'btn btn-warning',
                'style'=>'margin-left:10px;'
            ]); ?>
        </div>
    </div>


    <?= $form->field($model, 'type')->dropDownList($model->getTypeList(),['id'=>'type','onchange'=>'toggleTypeFields()']) ?>

    <?= $form->field($model, 'allow_contact')->checkbox() ?>

    <?php if ($model->status==\app\models\SearchRequest::STATUS_SCHEDULED) { ?>
        <div class="form-group">
            <label class="col-sm-3 text-right">Datum der Veröffentlichung</label>
            <div class="col-sm-6">
                <?= (new \app\components\EDateTime($model->scheduled_dt))->format('d.m.Y H:i') ?>
            </div>
        </div>
    <?php } ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 200]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 10]) ?>

    <?php foreach($offerFiles as $k=>$offerFile) { ?>
        <?=$form->field($offerFile, "[$k]file_id")->widget(\app\components\ImageIdWidget::className())?>
    <?php } ?>

    <?php /*if (count($model->files)>0) { ?>
        <div class="form-group">
                <div class="col-sm-9 col-sm-offset-3">
                    <?php foreach($model->files as $file) { ?>
                        <a target="_blank" href="<?=$file->url?>"><img style="margin: 0 10px 10px 0" class="img-thumbnail" src="<?=$file->getThumbUrl('searchRequestMobile')?>"/></a>
                    <?php } ?>
                </div>
        </div>
    <?php }*/ ?>

    <div class="type_autosell type_auction">
        <?= $form->field($model, 'price')->textInput() ?>
    </div>
    <div class="type_auction">
        <?= $form->field($model, 'notify_if_price_bigger')->textInput() ?>
    </div>

    <?= $form->field($model, 'without_view_bonus')->checkbox(); ?>

    <?= $form->field($model, 'view_bonus')->textInput() ?>
    <?= $form->field($model, 'view_bonus_used')->textInput() ?>
    <?= $form->field($model, 'view_bonus_total')->textInput() ?>


    <div class="type_autosell type_auction">
        <?= $form->field($model, 'delivery_days')->textInput() ?>
        <?= $form->field($model, 'buy_bonus')->textInput() ?>
        <?= $form->field($model, 'delivery_cost')->textInput() ?>

        <?= $form->field($model, 'amount')->textInput() ?>
        <?= $form->field($model, 'show_amount')->checkbox() ?>

        <?= $form->field($model, 'pay_allow_bank')->checkbox() ?>
        <?= $form->field($model, 'pay_allow_paypal')->checkbox() ?>
        <?= $form->field($model, 'pay_allow_jugl')->checkbox() ?>
        <?= $form->field($model, 'pay_allow_pod')->checkbox() ?>
        <?= $form->field($model, 'country_id')->dropDownList(\app\models\Country::getList()) ?>
        <?= $form->field($model, 'city')->textInput() ?>
        <?= $form->field($model, 'zip')->textInput() ?>
        <?= $form->field($model, 'address')->textInput() ?>
    </div>

    <?=$form->field($model,'active_till',['template'=>"{label}\n<div class=\"col-sm-2\">{input}</div>"])->widget(\kartik\datecontrol\DateControl::classname(), [
        'type'=>\kartik\datecontrol\DateControl::FORMAT_DATE,
        'ajaxConversion'=>false,
        'options' => [
            'pluginOptions' => [
                'autoclose' => true
            ],
            'removeButton'=>false
        ]
    ])
    ?>

    <hr>

    <di id="uf_fields-box">
        <?= $form->field($model, 'uf_enabled')->checkbox(['id'=>'uf_enabled','onchange'=>'toggleUfFields()']) ?>

        <div id="uf_fields" style="display:none;">
            <?= $form->field($model, 'uf_packet')->radioList(\app\models\Offer::getUfPacketList()) ?>
            <?= $form->field($model, 'uf_age_from') ?>
            <?= $form->field($model, 'uf_age_to') ?>
            <?= $form->field($model, 'uf_sex')->radioList(\app\models\Offer::getSexList()) ?>
            <?= $form->field($model, 'uf_offer_request_completed_interest_id')->dropDownList(\app\components\Helper::addEmptyValue(\app\models\Interest::getLevel1List(\app\models\Interest::TYPE_OFFER))) ?>
            <?= $form->field($model, 'uf_member_from') ?>
            <?= $form->field($model, 'uf_member_to') ?>
            <?= $form->field($model, 'uf_offers_view_buy_ratio_from') ?>
            <?= $form->field($model, 'uf_offers_view_buy_ratio_to') ?>
            <?= $form->field($model, 'uf_city') ?>
            <?= $form->field($model, 'uf_zip') ?>
            <?= $form->field($model, 'uf_distance_km') ?>
            <?= $form->field($model, 'uf_country_id')->dropDownList(\app\components\Helper::addEmptyValue(\app\models\Country::getList())) ?>
            <?= $form->field($model, 'uf_offer_year_turnover_from') ?>
            <?= $form->field($model, 'uf_offer_year_turnover_to') ?>
            <?= $form->field($model, 'uf_active_search_requests_from') ?>
            <?= $form->field($model, 'uf_messages_per_day_from') ?>
            <?= $form->field($model, 'uf_messages_per_day_to') ?>
            <?= $form->field($model, 'uf_balance_from') ?>
        </div>
    </div>


    <?php ob_start(); ?>
    <script>

        window.toggleUfFields=function() {
            if ($('#uf_enabled').is(':checked')) {
                $('#uf_fields').show();
            } else {
                $('#uf_fields').hide();
            }
        };

        window.toggleTypeFields=function() {
            if ($('#type option:selected').attr('value')=='AUTOSELL') {
                $('.type_ad').hide();
                $('.type_auction').hide();
                $('.type_autosell').show();
            }

            if ($('#type option:selected').attr('value')=='AUCTION') {
                $('.type_autosell').hide();
                $('.type_ad').hide();
                $('.type_auction').show();
            }

            if ($('#type option:selected').attr('value')=='AD') {
                $('.type_autosell').hide();
                $('.type_auction').hide();
                $('.type_ad').show();
            }

        };

        toggleUfFields();
        toggleTypeFields();

        if($('input:checkbox[name="Offer[without_view_bonus]"]').is(':checked')) {
            $('.field-offer-view_bonus').hide();
            $('.field-offer-view_bonus_total').hide();
        }

        $('input:checkbox[name="Offer[without_view_bonus]"]').on('change', function() {
            if(this.checked) {
                $('.field-offer-view_bonus').hide();
                $('.field-offer-view_bonus_total').hide();
                $('#uf_fields-box').hide();
            } else {
                $('.field-offer-view_bonus').show();
                $('.field-offer-view_bonus_total').show();
                $('#uf_fields-box').show();
            }
        });

    </script>
    <?php $this->registerJs(preg_replace('%</?script>%','',ob_get_clean()))?>

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            <?php if(hasCurrentActionPostAccess()) { ?>
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
            <?php } ?>

            <?php if(hasAccess('admin-offer/control') && in_array($model->validation_status,[\app\models\Offer::VALIDATION_STATUS_AWAITING,\app\models\Offer::VALIDATION_STATUS_AWAITING_LATER])) { ?>

                <?= Html::button(Yii::t('app', 'Annehmen'), [
                    'onclick' => '$("#modal-offer-accept").modal("show").find("#modal-offer-accept-content").load($(this).attr("value"));',
                    'value'=>\yii\helpers\Url::to(['admin-offer/accept', 'id'=>$model->id]),
                    'class' => 'btn btn-success',
                    'style'=>'margin-left:10px;'
                ]); ?>

                <?= Html::button(Yii::t('app', 'Ablehnen'), [
                    'onclick' => '$("#modal-offer-reject").modal("show").find("#modal-offer-reject-content").load($(this).attr("value"));',
                    'value'=>\yii\helpers\Url::to(['admin-offer/reject', 'id'=>$model->id]),
                    'class' => 'btn btn-danger',
                    'style'=>'margin-left:10px;'
                ]); ?>

            <?php } ?>

        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
