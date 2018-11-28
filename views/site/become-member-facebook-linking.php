<?php

use \app\components\ActiveForm;
use yii\helpers\Url;
use app\components\Helper;
?>
<script src='https://www.google.com/recaptcha/api.js'></script>
<div class="content become-member-page">
    <div class="container clearfix">

<?php /*
    <?php if (!Yii::$app->session->hasFlash('saved')) { ?>
        <div class="page-form clearfix">
            <div class="page-form-box">

                <?php $form = ActiveForm::begin([
                    'fieldConfig'=>['template'=>'{input}'],
                    'labelInPlaceholder'=>true
                ])?>

                <?=$form->errorSummary($model)?>

                <div class="field-input-box">
                    <?=$form->field($model,'first_name')?>
                </div>
                <div class="field-input-box">
                    <?=$form->field($model,'last_name')?>
                </div>

                <div class="field-input-box">
                    <?=$form->field($model,'email')?>
                </div>

                <div class="field-input-box">
                    <?=$form->field($model,'phone')->textInput(['placeholder' => 'z.B. 016012345678'])?>
                </div>

                <div class="btn-box">
                    <button type="submit" class="btn btn-submit"><?=Yii::t('app','Absenden')?></button>
                </div>

                <?php ActiveForm::end() ?>

            </div>
        </div>
    <?php } ?>
*/ ?>

<div class="page-form clearfix">
            <?php $form = ActiveForm::begin([
                'id'=>'registration-data',
                'fieldConfig'=>['template'=>'{input}'],
                'labelInPlaceholder'=>true,
                'enableClientValidation'=>false
            ]) ?>
		

	<div class="page-title">
        <h1><?=Yii::t('app','Bereits Mitglied?')?></h1>
        <?php if (Yii::$app->session->hasFlash('saved')) { ?>
            <div class="success-sent">
                <p><?=Yii::t('app', 'Danke! Deine Daten wurden gespeichert. Bitte warte bis jemand Dich einlädt.')?></p>
            </div>
        <?php } else { ?>
            <p><?=Yii::t('app', 'Gib Deine vorhandene E-Mail-Adresse ein <br> und Verknüpfe Dein Facebook Konto mit einem Schritt!')?></p>
        <?php } ?>
    </div>
	<br>
			 <div class="page-form-box">
			 <?php if($model->existing_account ||$model->existing_password){ echo $form->errorSummary($model);} ?>
				<div class="field-input-box">
                    <?=$form->field($model,'existing_account')?>
					  <div class="field-note"><?= Yii::t('app', '<strong>Du besitzt bereits ein Jugl Konto?</strong>
					  Dann gib gier Deine Email ein.'); ?></div>
                </div>

                <div class="field-input-box">
                    <?=$form->field($model,'existing_password')->passwordInput()?>
                    <div class="field-note"><?= Yii::t('app', 'Gib hier bitte dein Passwort für das bereits vorhandene Jugl Konto ein, das du verknüpfen möchtest.'); ?></div>
                </div>
				<div class="lost-password-box">
                    <a href="<?=Url::to(['restore-password-step1'])?>"><?=Yii::t('app','Passwort vergessen?')?></a>
                </div>

            <div class="registration-form-btn">
                <button type="submit" id="regBtn" class="btn btn-submit"><?= Yii::t('app','Konten verknüpfen'); ?></button>
            </div> 
		
			</div>
<br>

            <?php ActiveForm::end(); ?>
 
			
   

    </div>
</div>
</div>






