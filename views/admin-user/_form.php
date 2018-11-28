<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\password\PasswordInput;
use kartik\datecontrol\DateControl;
use app\models\User;
use app\models\Country;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

$hasUpdateAccess=hasAccess(Yii::$app->controller->route,'POST');

?>

    <div class="user-form">

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>


        <div class="row">
            <div class="col-sm-7">
                <?php if (Yii::$app->admin->identity->type==\app\models\Admin::TYPE_SUPERVISOR) { ?>

                    <div class="form-group">
                        <label class="control-label col-sm-7"><?=User::getEncodedAttributeLabel('balance')?></label>
                        <div class="col-sm-5">
                            <div class="control-value"><?=Html::encode($model->balance)?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-7"><?=User::getEncodedAttributeLabel('balance_buyed')?></label>
                        <div class="col-sm-5">
                            <div class="control-value"><?=Html::encode($model->balance_buyed)?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-7"><?=User::getEncodedAttributeLabel('balance_earned')?></label>
                        <div class="col-sm-5">
                            <div class="control-value"><?=Html::encode($model->balance_earned)?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-7"><?=User::getEncodedAttributeLabel('balance_token_deposit_percent')?></label>
                        <div class="col-sm-5">
                            <div class="control-value"><?=Html::encode($model->balance_token_deposit_percent)?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-7"><?=User::getEncodedAttributeLabel('balance_token')?></label>
                        <div class="col-sm-5">
                            <div class="control-value"><?=Html::encode($model->balance_token)?></div>
                        </div>
                    </div>
<?php /*
                    <div class="form-group">
                        <label class="control-label col-sm-7"><?=User::getEncodedAttributeLabel('balance_token_buyed')?></label>
                        <div class="col-sm-5">
                            <div class="control-value"><?=Html::encode($model->balance_buyed)?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-7"><?=User::getEncodedAttributeLabel('balance_token_earned')?></label>
                        <div class="col-sm-5">
                            <div class="control-value"><?=Html::encode($model->balance_earned)?></div>
                        </div>
                    </div>
*/ ?>
                     <div class="form-group">
                        <label class="control-label col-sm-7"><?=User::getEncodedAttributeLabel('payment_complaints')?></label>
                        <div class="col-sm-5">
                            <div class="control-value"><?=Html::encode($model->payment_complaints)?></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-7"><?=User::getEncodedAttributeLabel('stat_offer_year_turnover')?></label>
                        <div class="col-sm-5">
                            <div class="control-value"><?=Html::encode($model->stat_offer_year_turnover)?></div>
                        </div>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label class="control-label col-sm-7"><?=User::getEncodedAttributeLabel('stat_messages_per_day')?></label>
                    <div class="col-sm-5">
                        <div class="control-value"><?=Html::encode($model->stat_messages_per_day)?></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-7"><?=User::getEncodedAttributeLabel('stat_active_search_requests')?></label>
                    <div class="col-sm-5">
                        <div class="control-value"><?=Html::encode($model->stat_active_search_requests)?></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-7"><?=User::getEncodedAttributeLabel('stat_offers_view_buy_ratio')?></label>
                    <div class="col-sm-5">
                        <div class="control-value"><?=Html::encode($model->stat_offers_view_buy_ratio)?></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-7"><?= Yii::t('app','Telefonnummer SMS Verifikation') ?></label>
                    <div class="col-sm-5">
                        <div class="control-value"><?= $model->validation_phone_status==User::VALIDATION_PHONE_STATUS_VALIDATED ? Html::encode($model->validation_phone):'-'?></div>
                    </div>
                </div>
				
				<div class="form-group">
                    <label class="control-label col-sm-7"><?= Yii::t('app','Spam-Punkte') ?></label>
                    <div class="col-sm-5">
                        <div class="control-value"><?= $model->spam_points?></div>
                    </div>
                </div>
				<div class="form-group">
                    <label class="control-label col-sm-7"><?= Yii::t('app','Zeitpause') ?></label>
                    <div class="col-sm-5">
                        <div class="control-value"><?= $model->delay_invited_member?> Sekunden</div>
                    </div>
					<label class="control-label col-sm-7"><?= Yii::t('app','Mitglieder eingeladen (heute)') ?></label>
                    <div class="col-sm-5">
                        <div class="control-value"><?= $model->get_user_invited_today; ?> </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-7"><?= Yii::t('app','Punkte durch Abstimmungen') ?></label>
                    <div class="col-sm-5">
                        <div class="control-value"><?= $model->video_identification_score; ?> </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-5">
                <div class="user-avatar-box">
                    <?php if(count($model->userPhotos) > 0) { ?>
                        <div class="count-user-photo label label-primary">+<?= count($model->userPhotos) ?></div>
                    <?php } ?>

                    <?= Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['admin-user/update-photo', 'id'=>$model->id], ['class'=>'btn btn-default btn-sm user-photo-update']) ?>
                    <?= Html::a(Html::img($model->getAvatarThumbUrl('avatarBig'), ['class'=>'thumbnail']), $model->getAvatarThumbUrl('fancybox'), ['class'=>'fancybox', 'rel'=>$model->id]) ?>
                </div>
                <div class="user-photo-box">
                    <?php if(count($model->userPhotos) > 0) {
                        foreach ($model->userPhotos as $photo) {
                            echo Html::a(Html::img($photo->file->getThumbUrl('avatarBig'), ['class'=>'thumbnail']), $photo->file->getThumbUrl('fancybox'), ['class'=>'fancybox', 'rel'=>$model->id]);
                        }
                    } ?>
                </div>
            </div>

        </div>


        <hr/>

        <p class="text-center"><?= Yii::t('app', 'Team') ?> <?= $model->parent->name ?> (<?= (new \app\components\EDateTime($model->dt_parent_change)).' '.Yii::t('app', 'Uhr'); ?>) </p><br>

        <?= $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>

        <?= $form->field($model, 'packet')->dropDownList($model->getPacketList(),['prompt'=>'', 'onclick'=>'window.processVipFields()','id'=>'input-packet']) ?>

        <div id="packet-vip-controls">
            <div id="packet-vip-till">
                <?= $form->field($model, 'vip_active_till')->widget(DateControl::classname(),['type'=>DateControl::FORMAT_DATETIME]); ?>
            </div>
            <?= $form->field($model, 'vip_lifetime')->checkbox(['onclick'=>'window.processVipFields()','id'=>'input-vip-lifetime']); ?>
        </div>

        <?= $form->field($model, 'email')->textInput(['maxlength' => 256]) ?>

        <?= $form->field($model, 'sex')->dropDownList($model->getSexList()) ?>

        <?= $form->field($model, 'first_name')->textInput(['maxlength' => 256]) ?>

        <?= $form->field($model, 'last_name')->textInput(['maxlength' => 256]) ?>

        <?= $form->field($model, 'nick_name')->textInput(['maxlength' => 256]) ?>
		
		<?= $form->field($model, 'country_id')->dropDownList(Country::getList(),['options'=>array($model->country_id => array('selected'=>true))]) ?>
		
		<?= $form->field($model, 'is_company_name')->checkbox() ?>
		<?= $form->field($model, 'company_name')->textInput(['maxlength' => 256]) ?>																	
		<?= $form->field($model, 'company_manager')->textInput(['maxlength' => 256]) ?>																			   
		<?= $form->field($model, 'impressum')->textarea(['rows' => 6]) ?>																 
		<?= $form->field($model, 'agb')->textarea(['rows' => 6]) ?>														   
        <?= $form->field($model, 'birthday')->widget(DateControl::classname(),['type'=>DateControl::FORMAT_DATE]); ?>
 
        <?= $form->field($model, 'phone')->textInput(['maxlength' => 64]) ?>

        <?php if ($model->validation_phone_status==User::VALIDATION_PHONE_STATUS_VALIDATED) { ?>
		<?= $form->field($model, 'validation_phone')->label(Yii::t('app','Telefonnummer SMS Verifikation')) ?>
        <?php } ?>
        <?= $form->field($model, 'plainPassword')->widget(PasswordInput::classname()); ?>

        <?= $form->field($model, 'free_registrations_limit')->textInput() ?>

        <hr/>

        <?= $form->field($model, 'street')->textInput(['maxlength' => 64]) ?>

        <?= $form->field($model, 'house_number')->textInput(['maxlength' => 64]) ?>

        <?= $form->field($model, 'visibility_address1')->radioList($model->getVisibilityList(),['class'=>'horizontal']) ?>

        <hr/>

        <?= $form->field($model, 'zip')->textInput(['maxlength' => 64]) ?>

        <?= $form->field($model, 'city')->textInput(['maxlength' => 64]) ?>

        <?= $form->field($model, 'visibility_address2')->radioList($model->getVisibilityList(),['class'=>'horizontal']) ?>

        <hr/>

        <?= $form->field($model, 'profession')->textInput(['maxlength' => 64]) ?>

        <?= $form->field($model, 'visibility_profession')->radioList($model->getVisibilityList(),['class'=>'horizontal']) ?>

        <hr/>

        <?= $form->field($model, 'marital_status')->dropDownList($model->getMaritalStatusList(),['prompt'=>Yii::t('app','-- Please select --')]) ?>

        <?= $form->field($model, 'visibility_marital_status')->radioList($model->getVisibilityList(),['class'=>'horizontal']) ?>

        <hr/>

        <?= $form->field($model, 'about')->textarea() ?>

        <?= $form->field($model, 'visibility_about')->radioList($model->getVisibilityList(),['class'=>'horizontal']) ?>

        <hr/>


        <?= $form->field($model, 'validation_type')->dropDownList($model->getValidationTypeList(),['prompt'=>Yii::t('app','-- Please select --')]) ?>

        <?= $form->field($model, 'validation_status')->dropDownList($model->getValidationStatusList(),['onclick'=>'toggleValidationFailureReasonField()','id'=>'validation-status']) ?>



        <div id="validation-failure-reason" style="display:none">
            <?= $form->field($model, 'validation_failure_reason')->textarea(['rows'=>10, 'maxlength'=>2000]) ?>
        </div>

        <?php if ($model->validationPhoto1File || $model->validationPhoto2File || $model->validationPhoto3File) { ?>
            <div class="form-group">
                <div class="col-sm-9 col-sm-offset-3">
                    <?php if ($model->validationPhoto1File) { ?>
                        <a style="margin-right:15px;" target="_blank" href="<?=$model->validationPhoto1File->url?>"><img class="img-thumbnail" src="<?=$model->validationPhoto1File->getThumbUrl('validationSmall')?>"/></a>
                    <?php } ?>
                    <?php if ($model->validationPhoto2File) { ?>
                        <a target="_blank" href="<?=$model->validationPhoto2File->url?>"><img class="img-thumbnail" src="<?=$model->validationPhoto2File->getThumbUrl('validationSmall')?>"/></a>
                    <?php } ?>
                    <?php if ($model->validationPhoto3File) { ?>
                        <a style="margin-left:15px;" target="_blank" href="<?=$model->validationPhoto3File->url?>"><img class="img-thumbnail" src="<?=$model->validationPhoto3File->getThumbUrl('validationSmall')?>"/></a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <?php if(!empty($model->validation_changelog)) { ?>
            <?= $form->field($model, 'validation_changelog')->textarea([
                'rows'=>10,
                'readonly'=>true
            ]) ?>
        <?php } ?>

        <?= $form->field($model, 'validation_details')->textarea() ?>


        <?php ob_start(); ?>
        <script>
            window.toggleValidationFailureReasonField=function() {
                if ($('#validation-status option:selected').val()=='FAILURE') {
                    $('#validation-failure-reason').show();
                } else {
                    $('#validation-failure-reason').hide();
                }
            };

            toggleValidationFailureReasonField();

            window.processVipFields=function() {
                if ($('#input-packet option:selected').val()=='VIP') {
                    $('#packet-vip-controls').show();
                    if ($('#input-vip-lifetime').is(':checked')) {
                        $('#packet-vip-till').hide();
                    } else {
                        $('#packet-vip-till').show();
                    }
                } else {
                    $('#packet-vip-controls').hide();
                }
            };

            processVipFields();

            window.toggleIsModerator = function() {
                if($('#user-is_moderator').is(':checked')) {
                    $(".field-user-allow_moderator_country_change").show();
                } else {
                    $(".field-user-allow_moderator_country_change").hide();
                }
            };

            toggleIsModerator();

        </script>
        <?php $this->registerJs(preg_replace('%</?script>%','',ob_get_clean()))?>

        <hr>

        <?php foreach($model->userBankDatas as $k=>$bankData) { ?>
            <?= $form->field($bankData, "[$k]iban") ?>
            <?= $form->field($bankData, "[$k]bic") ?>
            <?= $form->field($bankData, "[$k]owner") ?>
            <hr>
        <?php } ?>

        <?= $form->field($model, 'paypal_email') ?>
        <?= $form->field($model, 'is_moderator')->checkbox(['onclick'=>'window.toggleIsModerator()']) ?>
        <?= $form->field($model, 'allow_moderator_country_change')->checkbox(); ?>
        <?= $form->field($model, 'is_blocked_in_trollbox')->checkbox() ?>
        <?= $form->field($model, 'publish_offer_wo_validation')->checkbox() ?>
        <?= $form->field($model, 'publish_search_request_wo_validation')->checkbox() ?>
        <?= $form->field($model, 'allow_country_change')->checkbox() ?>
        <?= $form->field($model, 'trollbox_messages_limit_per_day')->textInput() ?>

		<hr />
		<?= $form->field($model, 'ad_status_auto')->dropDownList($model->getAdStatusAutoList()) ?>
        <?php if ($hasUpdateAccess) { ?>
        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-3">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default','style'=>'margin-left:10px;','onclick'=>'history.back();']) ?>
            </div>
        </div>
        <?php } ?>

        <?php ActiveForm::end(); ?>

    </div>



<?php if ($hasUpdateAccess) { ?>

<h1><?=Yii::t('app','Konto aufladen')?></h1>
<?php $form = ActiveForm::begin(['layout' => 'horizontal','action'=>['add-user-balance','id'=>$model->id]]); ?>

<?= $form->field($modelAddUserBalance, 'distribute')->checkbox() ?>
<?= $form->field($modelAddUserBalance, 'sum') ?>
<?= $form->field($modelAddUserBalance, 'comments')->textarea(['rows'=>5]) ?>

<div class="form-group">
    <div class="col-sm-6 col-sm-offset-3">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php } ?>

<?php if ($hasUpdateAccess) { ?>

    <h1><?=Yii::t('app','Token Konto aufladen')?></h1>
    <?php $form = ActiveForm::begin(['layout' => 'horizontal','action'=>['add-user-token-balance','id'=>$model->id]]); ?>

    <?= $form->field($modelAddUserTokenBalance, 'distribute')->checkbox() ?>
    <?= $form->field($modelAddUserTokenBalance, 'sum') ?>
    <?= $form->field($modelAddUserTokenBalance, 'comments')->textarea(['rows'=>5]) ?>

    <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

<?php } ?>

<h1><?=Yii::t('app','Weitere Dienste')?></h1>
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
	
	<?= $form->field($model, 'access_translator',[
			'template'=>"{label}\n<div class=\"col-sm-6\" style='padding-top:7px;'>{input}</div>",
			'labelOptions'=>['class'=>'col-sm-3 control-label'],
		])->checkbox([],false) ?>

<div class="form-group">
    <div class="col-sm-6 col-sm-offset-3">
		<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		
    </div>
</div>

<?php ActiveForm::end(); ?>
