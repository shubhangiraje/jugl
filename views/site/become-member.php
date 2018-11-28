<?php

use \app\components\ActiveForm;
use yii\helpers\Url;
use app\components\Helper;
use kartik\social\Module;
$social = Yii::$app->getModule('social');
$callback = Url::toRoute(['/site/login-facebook'],true);

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
		
<?php if ($model->facebook_id) { ?>
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
                    <div class="field-note"><?= Yii::t('app', 'Gib hier bitte Dein Passwort für das bereits vorhandene Jugl Konto ein, das Du verknüpfen möchtest.'); ?></div>
                </div>

            <div class="registration-form-btn">
                <button type="submit" id="regBtn" class="btn btn-submit"><?= Yii::t('app','Konten verknüpfen'); ?></button>
            </div> 
		
			</div>
<br>
<br>

<?php } ?>

	<div class="page-title">
			<h1><?=Yii::t('app','Mitglied werden')?></h1>
			<?php if (Yii::$app->session->hasFlash('saved')) { ?>
				<div class="success-sent">
					<p><?=Yii::t('app', 'Danke! Deine Daten wurden gespeichert. Bitte warte bis jemand Dich einlädt.')?></p>
				</div>
			<?php } else { ?>
				<p><?=Yii::t('app', 'Gib Deine Lieblings-Email-Adresse und Dein Wunschpasswort an für Deinen persönlichen Jugl Start.')?></p>
			<?php } ?>
	 </div>

        <br>
		<?php if(!$model->facebook_id){ ?>
			 <br>
				<div class="field-input-box">
						<div class="btn btn-submit fb">
							<i class="fa fa-facebook-official fa-2x fb-button-start"></i><span class="fb-link-start"><?php echo $social->getFbLoginLink($callback, ['label'=>Yii::t('app', 'Mit Facebook registrieren'),'class'=>'fb-generated-link'],['email','public_profile','user_location','user_birthday']); ?></span>
						</div>
				</div>
		<?php }?>
		<br>

            <div class="page-form-box">
                <?php if(!$model->existing_account && !$model->existing_password){ echo $form->errorSummary($model);} ?>
				<div class="field-input-box">
                    <?=$form->field($model,'first_name')?>
                </div>
                <div class="field-input-box">
                    <?=$form->field($model,'last_name')?>
                </div>
				
				<div class="field-input-box">
                    <?= $form->field($model,'email'); ?>
                </div>
                <div class="field-input-box">
                    <?=$form->field($model,'password')->passwordInput()?>
                    <div class="field-note"><?= Yii::t('app', 'Das Passwort muss folgende Voraussetzungen haben: mind. 6 Zeichen, davon mind. einen Buchstaben und eine Ziffer.'); ?></div>
                </div>
                <div class="field-input-box">
                    <?=$form->field($model,'password_repeat')->passwordInput()?>
                </div>
				
				<div class="field-input-box">
                    <?=$form->field($model,'sex')->dropDownList([''=>Yii::t('app', 'Geschlecht auswählen'),'M'=>Yii::t('app', 'Männlich'),'F'=>Yii::t('app', 'Weiblich')],['options'=>array($model->sex => array('selected'=>true))])?>
                </div>
				
				<div class="field-input-box">
				   <?= $form->field($model, 'country_id')->dropDownList(\app\models\Country::getList(),['options'=>array($model->country_id => array('selected'=>true))]) ?>
				</div>
	
				<div class="field-input-box">
					<div class="g-recaptcha" data-sitekey="6LehpzEUAAAAAHGJspecx3GelT-jsVrwbWT1Ryxk"></div>
				</div>
            </div>
			
            <div class="terms-of-service-box">
                <?= Yii::t('app','Mit dem Klick auf "Absenden" akzeptierst Du unsere <a href="{link1}">Nutzerbedingungen</a> und unsere <a href="{link2}">Datenschutzrichtlinien</a>.', [
                    'link1' =>Url::to(['site/view','view'=>'nutzungsbedingungen']),
                    'link2' =>Url::to(['site/view','view'=>'datenschutz'])
                ]) ?>
            </div>

            <div class="registration-form-btn">
                <button type="submit" id="regBtn" class="btn btn-submit"><?= Yii::t('app','Absenden'); ?></button>
            </div>

            <?php ActiveForm::end(); ?>
 
			
        </div>

    </div>
</div>






