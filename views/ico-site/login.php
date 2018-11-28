<?php 

use \app\components\ActiveForm;
use \yii\captcha\Captcha;
use \yii\helpers\Url;
use kartik\social\Module;
$social = Yii::$app->getModule('social');
$callback = Url::toRoute(['/ico-site/login-facebook'],true);
?>

<header class="site-header is-sticky">
    <?= $this->render('../layouts/ico-nav') ?>
    <div class="header-bottom-box">
        <h1><?=Yii::t('app','Login')?></h1>
    </div>
</header>

<div class="section">
    <div class="container clearfix">

        <div class="login-form clearfix">

            <p><?=Yii::t('app', 'Gib hier Dein Benutzername bzw. E-Mail und Passwort ein, um Dich einzuloggen.')?></p>
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'labelInPlaceholder'=>true
            ])?>

            <?=$form->field($model,'username')->textInput()->label(false) ?>
            <?=$form->field($model,'password')->passwordInput()->label(false)?>

            <?php if ($model->getScenario()=='withCaptcha') { ?>
                <div class="captcha-box">
                    <?=$form->field($model,'verifyCode',[
                        'template'=>'{input}'
                        ])->widget(Captcha::className(), [
                            'template' => '{image}<a onclick="$(this).parent().find(\'img\').click();" class="refrech-captcha">'.Yii::t('app','Captcha-Code neu generieren').'</a><div class="field-input-box">{input}</div>',
                            'captchaAction' =>'ico-site/captcha',
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
                <a href="https://jugl.net<?=Url::to(['site/restore-password-step1'])?>"><?=Yii::t('app','Passwort vergessen?')?></a>
            </div>

            <div class="login-form-remember-me">
                <div class="field-checkbox">
                    <?=$form->field($model,'rememberMe')->checkbox(['label' => Yii::t('app','Angemeldet bleiben')])?>
                </div>
            </div>

            <div class="login-btns-box">
                <button type="submit" class="btn btn-submit"><?=Yii::t('app','Einloggen')?></button>
                <div class="btn btn-fb fb">
                    <i class="fa fa-facebook-official fa-2x fb-button-start"></i><span class="fb-link-start"><?php echo $social->getFbLoginLink($callback, ['label'=>Yii::t('app', 'Mit Facebook anmelden'),'class'=>'fb-generated-link'],['email','public_profile','user_location','user_birthday']); ?></span>
                </div>
            </div>



            <?php ActiveForm::end() ?>
				

        </div>
    </div>
</div>






