<?php 

use \app\components\ActiveForm;
use \yii\captcha\Captcha;
use \yii\helpers\Url;
use kartik\social\Module;
$social = Yii::$app->getModule('social');
$callback = Url::toRoute(['/site/login-facebook'],true);
?>


<div class="content">
    <div class="container clearfix">

        <div class="page-title">
            <h1><?=Yii::t('app','Login')?></h1>
            <p><?=Yii::t('app', 'Gib hier Dein Benutzername bzw. E-Mail und Passwort ein, um Dich einzuloggen.')?></p>
        </div>

        <div class="page-form clearfix">
            <div class="page-form-box">
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'fieldConfig'=>['template'=>'{input}'],
                    'labelInPlaceholder'=>true
                ])?>

                <?=$form->errorSummary($model)?>
				
                <div class="field-input-box">

                    <?=$form->field($model,'username')?>
                </div>
                <div class="field-input-box">
                    <?=$form->field($model,'password')->passwordInput()?>
                </div>

                <?php if ($model->getScenario()=='withCaptcha') { ?>
                    <div class="captcha-box">
                        <?=$form->field($model,'verifyCode',[
                            'template'=>'{input}'
                            ])->widget(Captcha::className(), [
                                'template' => '{image}<a onclick="$(this).parent().find(\'img\').click();" class="refrech-captcha">'.Yii::t('app','Captcha-Code neu generieren').'</a><div class="field-input-box">{input}</div>',
                                'options'=> [
                                        'placeholder'=>$model->getAttributeLabel('verifyCode'),
                                        'autocomplete' => 'off',
                                        'autocorrect'  => 'off'
                                    ]
                                ]
                        )?>
                        <?= $this->registerJs('$(\'img[id$=-verifycode-image]\').click();') ?>
                    </div>
                <?php } ?>

                <div class="lost-password-box">
                    <a href="<?=Url::to(['restore-password-step1'])?>"><?=Yii::t('app','Passwort vergessen?')?></a>
                </div>

                <div class="login-form-bottom clearfix">
                    <div class="login-form-remember-me">
                        <div class="field-checkbox-box">
                            <?=$form->field($model,'rememberMe')->checkbox(['label' => Yii::t('app','Angemeldet bleiben')])?>
                        </div>
                    </div>

                    <div class="login-btn-box">
                        <button type="submit" class="btn btn-submit"><?=Yii::t('app','Einloggen')?></button>
                    </div>
                </div>


                <?php ActiveForm::end() ?>
				<br>
				<div class="field-input-box">
					<div class="btn btn-submit fb">
						<i class="fa fa-facebook-official fa-2x fb-button-start"></i><span class="fb-link-start"><?php echo $social->getFbLoginLink($callback, ['label'=>Yii::t('app', 'Mit Facebook anmelden'),'class'=>'fb-generated-link'],['email','public_profile','user_location','user_birthday']); ?></span>
					</div>
				</div>
				
            </div>
        </div>
    </div>
</div>






