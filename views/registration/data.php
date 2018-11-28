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

?>

<div class="content become-member-page">
    <div class="container clearfix">

        <div class="page-title">
            <h1><?=Yii::t('app','Mitglied werden')?></h1>
            <?php if (Yii::$app->session->hasFlash('saved')) { ?>
                <div class="success-sent">
                    <p><?=Yii::t('app', 'Danke! Deine Daten wurden gespeichert. Bitte warte bis jemand Dich einlädt.')?></p>
                </div>
            <?php } else { ?>
                <p><?=Yii::t('app','Gib Deine lieblings E-Mail-Adresse und Dein Wunschpasswort an für Deinen persönlichen Jugl Start.')?></p>
            <?php } ?>
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
                <?= $form->errorSummary([$model]) ?>
                <div class="field-input-box">
                    <?= $form->field($model,'email'); ?>
                </div>
                <div class="field-input-box">
                    <?=$form->field($model,'password')->passwordInput()?>
                    <div class="field-note"><?= Yii::t('app', 'Das Passwort muss folgende Voraussetzungen haben: mind. 6 Zeichen, davon mind. einen Buchstaben und eine Ziffer.'); ?></div>
                </div>
            </div>

            <div class="terms-of-service-box">
                <?= Yii::t('app','Mit dem Klick auf "Absenden" akzeptierst Du unsere <a href="{link}">Nutzerbedingungen und unsere Datenschutzrichtlinien</a>.', [
                    'link' =>Url::to(['site/view','view'=>'nutzungsbedingungen'])
                ]) ?>
            </div>

            <div class="registration-form-btn">
                <?= \yii\helpers\Html::button('Absenden', [
                    'class'=>'btn btn-submit',
                    'onclick'=>'
                        $.post("/registration/get-referral", function(data) {
                            console.log(data);
                            $("#registration-referral-popup").show();
                            $("#referral-user").html(data.username);                                                                    
                        });
                    '
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

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
            <div class="cancel popup-close"><?= Yii::t('app', 'Abbrechen') ?></div>
            <div class="ok" id="registration-referral-submit-btn"><?= Yii::t('app', 'Ja, weiter') ?></div>
        </div>
    </div>
</div>








