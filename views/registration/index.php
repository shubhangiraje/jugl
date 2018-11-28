<?php
/*
use app\components\ActiveForm;
use app\components\Helper;
use yii\captcha\Captcha;
use app\models\User;
use yii\helpers\Url;

?>

<!-- registration new -->


<div class="content registration-page">
    <div class="container clearfix">

        <div class="registration-title">
            <h1><?= Yii::t('app', 'Herzlich Willkommen!') ?></h1>
            <h2><?= Yii::t('app', 'In nur 3 Schritten hast du es geschafft!') ?></h2>
        </div>

        <div class="reg-stage-box clearfix">
            <div class="reg-stage-item stage-one">
                <div class="reg-stage-item-num">01</div>
                <div class="reg-stage-item-text"><?= Yii::t('app', 'Anmelden') ?></div>
            </div>
            <div class="reg-stage-item stage-two">
                <div class="reg-stage-item-num">02</div>
                <div class="reg-stage-item-text"><?= Yii::t('app', 'App herunterladen') ?></div>
            </div>
            <div class="reg-stage-item stage-three">
                <div class="reg-stage-item-num">03</div>
                <div class="reg-stage-item-text"><?= Yii::t('app', 'Einloggen') ?></div>
            </div>
        </div>

        <div class="registration-text-box">
            <h2><?= Yii::t('app', 'Jugl ist kostenlos!') ?></h2>
        </div>

        <div class="page-form clearfix">
            <?php $form = ActiveForm::begin([
                'id'=>'registration-data',
                'fieldConfig'=>['template'=>'{input}'],
                'labelInPlaceholder'=>true,
                'enableClientValidation'=>false
            ]) ?>

            <div class="page-form-box">
                <?= $form->errorSummary([$model,$modelCode]) ?>
                <div class="field-input-box">
                    <?= $form->field($model,'email'); ?>
                </div>
                <div class="field-input-box">
                    <?=$form->field($model,'password')->passwordInput()?>
                    <div class="field-note"><?= Yii::t('app', 'Das Passwort muss folgende Voraussetzungen haben: mind. 6 Zeichen, davon mind. einen Buchstaben und eine Ziffer.'); ?></div>
                </div>
                <?php if ($modelCode->invId=='' && $modelCode->refId=='') { ?>
                    <div class="field-input-box">
                        <?= $form->field($modelCode,'code')->textInput(['placeholder'=>Yii::t('app','Einladungscode (nicht unbedingt)')]); ?>
                    </div>
                <?php } ?>
            </div>

            <div class="terms-of-service-box">
                <?= Yii::t('app','Mit dem Klick auf "Absenden" akzeptierst Du unsere <a href="{link}">Nutzerbedingungen und unsere Datenschutzrichtlinien</a>.', [
                    'link' =>Url::to(['site/view','view'=>'nutzungsbedingungen'])
                ]) ?>
            </div>

            <div class="registration-form-btn">
                <button type="submit" id="regBtn" class="btn btn-submit"><?= Yii::t('app','Absenden'); ?></button>
            </div>

            <?php ActiveForm::end(); ?>
        </div>


    </div>
</div>
*/ ?>


<?php

use \app\components\ActiveForm;
use yii\helpers\Url;
use app\components\Helper;
use kartik\social\Module;
$social = Yii::$app->getModule('social');
$callback = Url::toRoute(['/registration/register-with-facebook/'],true);
?>

<div class="content become-member-page">
    <div class="container clearfix">

        <div class="page-title">
            <?php if ($model->code!='' || (!$model->refId && !$model->invId)) { ?>
                <h1><?=Yii::t('app','Bitte geben Sie hier den VIP-Code ein')?></h1>
            <?php } else { ?>
                <h1><?=Yii::t('app','Mitglied werden')?></h1>
                <p><?=Yii::t('app','Gib Deine lieblings E-Mail-Adresse und Dein Wunschpasswort an für Deinen persönlichen Jugl Start.')?></p>
            <?php } ?><br>
			<div class="field-input-box">
					<div class="btn btn-submit fb">
						<i class="fa fa-facebook-official fa-2x fb-button-start"></i>
						<span class="fb-link-start">
							<?= \yii\helpers\Html::button(Yii::t('app','Mit Facebook registrieren'), [
								'class'=>'fb-register-button',
								'onclick'=>'
                                    $.post("/registration/get-referral?code="+encodeURIComponent($(\'#code-input\').val()), function(data) {
                                        if (data.username) {
                                            $("#registration-referral-popup-facebook").show();
                                            $("#referral-user-facebook").html(data.username);
                                        }
                                    });
                                '
							]) ?>
						</span>
						
					</div>
			</div>
        </div>
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

            <div class="page-form-box">
                <?= $form->errorSummary([$model,$modelData]) ?>

                <?php if ($model->code!='' || (!$model->refId && !$model->invId)) { ?>
                    <div class="field-input-box">
                        <?= $form->field($model,'code')->textInput(['id'=>'code-input','placeholder'=>Yii::t('app','hier Gutschein-Code eingeben')]); ?>
                    </div>
                <?php } ?>
				<div class="field-input-box">
                    <?=$form->field($modelData,'first_name')?>
                </div>
                <div class="field-input-box">
                    <?=$form->field($modelData,'last_name')?>
                </div>
                <div class="field-input-box">
                    <?php if ($model->code!='' || (!$model->refId && !$model->invId)) { ?>
                        <label><?= Yii::t('app','Bitte geben Sie hier Ihre Email-Adresse ein.') ?></label>
                    <?php } ?>
                    <?= $form->field($modelData,'email'); ?>
                </div>
                <div class="field-input-box">
                    <?php if ($model->code!='' || (!$model->refId && !$model->invId)) { ?>
                        <label><?= Yii::t('app','Bitte geben Sie hier Ihr Wunschpasswort ein.') ?></label>
                    <?php } ?>
                    <?=$form->field($modelData,'password')->passwordInput()?>
                    <div class="field-note"><?= Yii::t('app', 'Das Passwort muss folgende Voraussetzungen haben: mind. 6 Zeichen, davon mind. einen Buchstaben und eine Ziffer.'); ?></div>
                </div>
				 <div class="field-input-box">
                    <?=$form->field($modelData,'password_repeat')->passwordInput()?>
                </div>
				<div class="field-input-box">
                    <?=$form->field($modelData,'sex')->dropDownList([''=>Yii::t('app', 'Geschlecht auswählen'),'M'=>Yii::t('app', 'Männlich'),'F'=>Yii::t('app', 'Weiblich')],['options'=>array($modelData->sex => array('selected'=>true))])?>
                </div>
				
				<div class="field-input-box">
				   <?= $form->field($modelData, 'country_id')->dropDownList(\app\models\Country::getList(),['options'=>array($modelData->country_id => array('selected'=>true))]) ?>
				</div>
				

                <div class="registration-form-btn">
                    <div class="registration-form-btn">
                        <?= \yii\helpers\Html::button(Yii::t('app','Absenden'), [
                            'class'=>'btn btn-submit',
                            'onclick'=>'
                        $.post("/registration/get-referral?code="+encodeURIComponent($(\'#code-input\').val()), function(data) {
                            if (data.username) {
                                $("#registration-referral-popup").show();
                                $("#referral-user").html(data.username);
                            } else {
                                $("#registration-data").submit();
                            }
                        });
                    '
                        ]) ?>
                    </div>
                </div>
				
				<br>
				
				

            </div>

            <?php /*
            <div class="terms-of-service-box">
                <?= Yii::t('app','Mit dem Klick auf "Absenden" akzeptierst Du unsere <a href="{link}">Nutzerbedingungen und unsere Datenschutzrichtlinien</a>.', [
                    'link' =>Url::to(['site/view','view'=>'nutzungsbedingungen'])
                ]) ?>
            </div>
            */ ?>

            <?php ActiveForm::end(); ?>
			<div class="terms-of-service-box">
                <?= Yii::t('app','Mit dem Klick auf "Absenden" akzeptierst Du unsere <a href="{link1}">Nutzerbedingungen</a> und unsere <a href="{link2}">Datenschutzrichtlinien</a>.', [
                    'link1' =>Url::to(['site/view','view'=>'nutzungsbedingungen']),
                    'link2' =>Url::to(['site/view','view'=>'datenschutz'])
                ]) ?>
			</div>
        </div>
		
        <?php if ($model->code!='' || (!$model->refId && !$model->invId)) { ?>
            <div class="vip-reg-text">
                <p><?= Yii::t('app', 'Den VIP-Code können Ihre Freunde oder Geschäftspartner, die bereits Mitglieder bei Jugl.net sind, direkt unter dem Punkt ‘Freunde einladen’ auf www.jugl.net erwerben.') ?></p>
                <p><?= Yii::t('app', 'Mit dem VIP-Code können Sie Geschäftspartnern den Einstieg bei Jugl.net erleichtern - diese sind dann sofort Premiummitglieder.') ?></p>
            </div>
        <?php } ?>

    </div>
</div>


<div class="popup-wrapper" id="registration-referral-popup">
    <div class="popup-content">
        <div class="popup-close-btn popup-close"></div>
        <div class="popup-box">
            <p><?= Yii::t('app','Vielen Dank für Dein Interesse an Jugl.net.<br>Du bist gerade dabei, Dich zu registrieren, über den Einladungslink von') ?></p>
            <p><b id="referral-user"></b></p>
            <p><?= Yii::t('app',' Ist das richtig?') ?></p>
        </div>
        <div class="buttons">
            <a class="btn btn-submit" href="<?=Url::to(['site/become-member'])?>"><?= Yii::t('app', 'Abbrechen') ?></a>
            <div class="ok" id="registration-referral-submit-btn"><?= Yii::t('app', 'Ja, weiter') ?></div>
        </div>
    </div>
</div>
<div class="popup-wrapper" id="registration-referral-popup-facebook">
    <div class="popup-content">
        <div class="popup-close-btn popup-close"></div>
        <div class="popup-box">
            <p><?= Yii::t('app','Vielen Dank für Dein Interesse an Jugl.net.<br>Du bist gerade dabei, Dich zu registrieren, über den Einladungslink von') ?></p>
            <p><b id="referral-user-facebook"></b></p>
            <p><?= Yii::t('app',' Ist das richtig?') ?></p>
        </div>
       <div class="btn btn-submit fb">
						<i class="fa fa-facebook-official fa-2x fb-button-start"></i><span class="fb-link-start"><?php echo $social->getFbLoginLink($callback, ['label'=>Yii::t('app', 'Mit Facebook registrieren'),'class'=>'fb-generated-link'],['email','public_profile','user_location','user_birthday']); ?></span>
		</div>
		<br>
		<br>
		<a class="btn btn-submit" href="<?=Url::to(['site/become-member'])?>"><?= Yii::t('app', 'Abbrechen') ?></a>
		<div class="terms-of-service-box">
                <?= Yii::t('app','Mit dem Klick auf "Mit Facebook registrieren" akzeptierst Du unsere <a href="{link}">Nutzerbedingungen und unsere Datenschutzrichtlinien</a>.', [
                    'link' =>Url::to(['site/view','view'=>'nutzungsbedingungen'])
                ]) ?>
		</div>
    </div>
</div>
