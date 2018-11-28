<?php

use app\components\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\social\Module;
$social = Yii::$app->getModule('social');
$callback = Url::toRoute(['/site/login-facebook'],true);
?>


<div id="splash-header" class="clearfix">
    <div id="splash-slider">
        <div class="slide show">
            <video autoplay="autoplay" loop="loop" preload="auto" muted poster="/static/images/site/poster_homepage.jpg">
                <source src="/static/video/homepage.webm" type="video/webm">
                <source src="/static/video/homepage.mp4" type="video/mp4">
            </video>
        </div>
        <div class="slide" style="background-image: url('/static/images/site/slide1.jpg')"></div>
    </div>

    <div id="top-header" class="container clearfix">
        <div class="icon-nav-splash-menu"><span class="strip"></span></div>
        <div class="header-lang"><div class="lang <?= Yii::$app->session['language'] ?> open-popup" data-open="lang-popup"></div></div>
        <div id="splash-menu" class="nav-menu">
            <ul>
                <li><a href="<?=Url::to(['/'])?>"><?=Yii::t('app', 'Home')?></a></li>
                <li><a href="<?=Url::to(['site/view','view'=>'wie-funktioniert'])?>"><?=Yii::t('app', 'Wie funktioniert jugl.net')?></a></li>
                <?php /* <li><a href="<?=Url::to(['registration/index'])?>"><?=Yii::t('app', 'Registrierung')?></a></li> */ ?>
                <li class="btn-menu-login">
                    <?php if (Yii::$app->user->isGuest) { ?>
                        <a href="<?=Url::to(['site/login'])?>"><?=Yii::t('app', 'Login')?></a>
                    <?php } else { ?>
                        <a href="<?=Url::to(['site/logout'])?>"><?=Yii::t('app', 'Logout')?></a>
                    <?php } ?>
					 
                </li>
				<?php /* <li><a href="<?=Url::to(['site/become-member'])?>"><?=Yii::t('app', 'Mitglied werden')?></a></li> */ ?>
            </ul>
        </div>

        <div id="splash-logo"> 
            <a href="<?=Url::to(['/'])?>"><?= Html::img('/static/images/site/logo.png', ['alt'=>'logo']) ?></a>
        </div>
    </div>

    <div id="content-header">
        <div class="container clearfix">

            <div id="header-text-box">
				<div id="header-form" class="front-heading">
				<h2><?=Yii::t('app', 'Wir sind 90.000 HELFER stark.');?><br><?=Yii::t('app', 'Jede erdenkliche Hilfe & DIenstleistung ist möglich!');?></h2><br />
				</div>
			<p><?=Yii::t('app', 'Die großen Internetfirmen machen Milliardengewinne.');?><br />
<?=Yii::t('app', 'Du hast sie groß gemacht!'); ?><br />
<?=Yii::t('app', 'Doch was hast Du dafür von ihnen bekommen?'); ?><br /><br />

<?=Yii::t('app', 'Wir gründen jetzt unser eigenes Netzwerk bei dem jeder profitiert!'); ?><br /><br />

<?=Yii::t('app', 'Ob geschäftlich oder privat - Jugl.net ist allumfassend:'); ?><br />
<?=Yii::t('app', 'Alltagshilfe - Networking - Geschäftskontakte - Soziales Netzwerk'); ?><br /><br />

<?=Yii::t('app', 'Die Jugler sind weltweit 24 Stunden an 7 Tagen der Woche für Dich da.'); ?><br /><br />

<?=Yii::t('app', 'Annonciere kostenlos und erreiche ein neues Publikum, welches Du so nie erreicht hättest. Die Jugler tragen die Botschaft in die Welt!'); ?><br /><br />

<?=Yii::t('app', 'Mit uns bist Du der Konkurrenz immer eine Nasenlänge voraus!'); ?>
				</p> 
                
                <div class="header-text-btn-box">
                    <a href="<?=Url::to(['site/view','view'=>'wie-funktioniert'])?>" class="btn btn-blue"><?=Yii::t('app', 'Mehr erfahren')?></a>
                    <a href="<?=Url::to(['registration/index'])?>" class="btn btn-orange"><?=Yii::t('app', 'VIP - Registrierung')?></a>
                </div>

            </div>

            <div id="header-form-box">
                <?php /*
                <div class="header-form-top-text"><?= Yii::t('app', 'Jugl.net ist kostenlos!') ?></div>
                <div class="header-form-top-text"><?= Yii::t('app', 'Jetzt Mitglied werden!') ?></div>
                */ ?>

                <div id="header-form">
                    <?php $form = ActiveForm::begin([
                        'id'=>'login-form',
                        'fieldConfig'=>['template'=>'{input}'],
                        'labelInPlaceholder'=>true,
                        'action'=>['site/login']
                    ]) ?>

                    <h2><?= Yii::t('app', 'Jugl.net ist kostenlos!') ?></h2>
                    <h2 style="margin-top: 10px"><?= Yii::t('app', 'Jetzt Mitglied werden!') ?></h2>
                    <a href="<?=Url::to(['site/become-member'])?>" class="btn btn-green btn-become-member"><?= Yii::t('app', 'Mitglied werden') ?></a>

                    <h2><?= Yii::t('app', 'Bereits Mitglied?') ?></h2>

                    <div class="header-form-input-box">
                        <div class="header-form-input"><?=$form->field($model,'username')->textInput(['placeholder' => Yii::t('app','Name oder Email')]) ?></div>
                        <div class="header-form-input"><?=$form->field($model,'password')->passwordInput(['placeholder' => Yii::t('app', 'Passwort')])?></div>
                    </div>

                    <button type="submit" class="btn btn-submit"><?= Yii::t('app', 'Einloggen') ?></button>

                    <div class="restore-password-btn-box">
                        <a href="<?=Url::to(['site/restore-password-step1'])?>"><?= Yii::t('app', 'Passwort vergessen?') ?></a>
                    </div>                    
                    <?php /*
                    <p style="margin: 15px 0 0 0;">Wenn Du einen Einladungsgutschein-Code hast, dann gib diesen bitte hier ein.</p>
                    <p style="margin: 5px 0 15px 0;"> Wenn nicht, lass das Feld einfach frei.</p>

                    <p style="margin: 0 0 30px 0"><?= Yii::t('app','Gib hier bitte Deinen Einladungsgutschein-Code zur Anmeldung als Premium-Mitglied ein.') ?></p>

                    <div class="header-form-input"><?=$form->field($modelCode,'code')->textInput(['placeholder' => Yii::t('app','hier Gutschein-Code eingeben')]) ?></div>
                    <p style="">
                        <?= Yii::t('app', 'Mit dem Klick auf “Mitglied werden” akzeptierst Du unsere <a href="{link}">AGB</a>\'s.', [
                            'link'=>Url::to(['site/index'])
                        ]) ?>
                    </p>

                    <div class="header-form-btn">
                        <button type="button" data-open="registration-popup" class="btn-green open-popup"><?=Yii::t('app','mitglied werden')?></button>
                    </div>
                    */ ?>

                    <?php ActiveForm::end() ?>
					<br>
					 <div class="btn btn-submit fb">
						<i class="fa fa-facebook-official fa-2x fb-button-start"></i><span class="fb-link-start"><?php echo $social->getFbLoginLink($callback, ['label'=>Yii::t('app', 'Mit Facebook anmelden'),'class'=>'fb-generated-link'],['email','public_profile','user_location','user_birthday']); ?></span>
					</div>
                </div>
			</div>
		<br><br>	
        <div class="index-youtube-cnt">
				<div class="btn btn-violet">
				<a href="https://youtu.be/E4ORePMCacI" title="<?= Yii::t('app', 'Du bist Dir noch nicht sicher? Hier kannst Du Dir Videos über Jugl.net auf YouTube ansehen und Dich überzeugen!') ?>" target="_blank"><?= Yii::t('app', 'Jugl auf Youtube') ?></a>
				</div>
				<div class="index-youtube-desc">
				<?= Yii::t('app', 'Du bist Dir noch nicht sicher? Hier kannst Du Dir Videos über Jugl.net auf YouTube ansehen und Dich überzeugen!') ?>
				</div>
			</div>
        </div>
        <div class="container">
            <div class="greeting-text"><?= Yii::t('app', '<span class="greeting-big">JUGL.NET - </span><span class="greeting-small">Du kannst nur gewinnen!</span></h1>') ?></div>
        </div>
    </div>

</div>

<div id="content-splash">
    <div id="top-content">
        <div class="container">
            <h1><?=Yii::t('app', 'Willkommen bei JUGL.NET')?></h1>

            <div class="top-content-box clearfix">
                <div class="top-content-image">
                    <img src="/static/images/site/jugl_splash.jpg" alt="jugl_splash"/>
                </div>

                <div class="top-content-text">
                    <h3><?=Yii::t('app', 'JUGL.NET beinhaltet drei Themenbereiche mit denen Du Geld verdienen kannst:')?></h3>
                    <ul class="top-content-list">
                        <li><?=Yii::t('app', 'Du lädst Deine Freunde ein und baust Dir damit Dein Netzwerk auf. Für jede erfolgreiche Einladung erhältst Du Geld.')?></li>
                        <li><?=Yii::t('app', 'Du gibst Deine Interessen an (von der Zahnbürste bis zum Luxuswagen), bleibst dabei anonym und bekommst von Händlern und Privatpersonen Produktangebote, die auf Deinen Interessen basieren. Allein dafür, dass Du, Deine Freunde oder deren Freundesfreunde diese Werbung lesen, erhältst Du Geld.')?></li>
                        <li><?=Yii::t('app', 'Assistenz, Recherche, Vermittlung... egal ob die günstigste Reise, die schönste Veranstaltung oder die Traumwohnung - bei Jugl.net helfen sich die Mitglieder gegenseitig, das perfekte Angebot zu finden.')?></li>
                    </ul>
                    <h3><?=Yii::t('app', 'Egal ob Du selbst, Deine Freunde oder deren Freundesfreunde etwas kaufen oder verkaufen,<br>Du verdienst immer an allen Aktivitäten in Deinem Netzwerk mit. Du selbst bist zu keinem Umsatz verpflichtet.')?></h3>
                    <div class="btn-box btn-two">
                        <a href="<?=Url::to(['site/view','view'=>'wie-funktioniert'])?>" class="btn btn-submit"><?=Yii::t('app', 'Mehr erfahren')?></a>
                        <a href="<?=Url::to(['site/become-member'])?>" class="btn btn-green"><?=Yii::t('app', 'Mitglied werden')?></a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="bottom-content" >
        <div class="container clearfix">
            <h1><?=Yii::t('app', 'Download')?></h1>
            <ul class="bottom-apps-box">
                <li>
                    <div class="app-name"><?=Yii::t('app', 'JuglApp für')?></div>
                    <a href="https://itunes.apple.com/app/id978284701" target="_blank"><img src="/static/images/site/app_store.png" alt="app_store"/></a>
                </li>
                <li>
                    <div class="app-name"><?=Yii::t('app', 'JuglApp für')?></div>
                    <a href="https://play.google.com/store/apps/details?id=com.kreado.jugl2&hl=de" target="_blank"><img src="/static/images/site/google_play.png" alt="google_play"/></a>
                </li>
                <?php /*
                <li>
                    <div class="app-name">{Yii::t('app', 'JuglApp für')}</div>
                    <a href="#"><img src="/static/images/site/win_store.png" alt="win_store"/></a>
                </li>
                */ ?>
            </ul>
        </div>
    </div>

</div>



<div class="popup-wrapper registration-popup">
    <div class="popup-content">
        <div class="popup-close-btn popup-close"></div>
        <div class="popup-box">
            <p><?= Yii::t('app', 'Du bist gerade dabei, Dich über die Seite www.jugl.net anzumelden.'); ?></p>
            <p><?= Yii::t('app', 'Wenn Du von einem Freund eingeladen wurdest, dann brich diesen Vorgang bitte ab und melde Dich über seinen Einladungslink an.'); ?></p>
            <p><?= Yii::t('app', 'Sonst bist Du nicht in seinem Netzwerk.'); ?></p>
        </div>
        <div class="buttons no-line">
            <div class="cancel popup-close"><?= Yii::t('app', 'Ich wurde eingeladen - Vorgang abbrechen') ?></div>
            <div class="ok registration-submit-btn"><?= Yii::t('app', 'Ich wurde nicht eingeladen - über Jugl.net anmelden') ?></div>
        </div>
    </div>
</div>

<?php
    if (Yii::$app->session->hasFlash('validation-popup')) {
        $this->registerJs('$(\'.validation-popup\').show()',\yii\web\View::POS_READY);
    }
?>

<div class="popup-wrapper validation-popup">
    <div class="popup-content" style="padding-bottom:32px;">
        <div class="popup-close-btn popup-close"></div>
        <div class="popup-box">
		<p><?= Yii::t('app', 'Vielen Dank für Deine Registrierung! Wir wünschen Dir viel Spaß und Erfolg  bei Jugl.net.'); ?></p>
         <!-- <p>Yii::t('app', 'Vielen Dank! Du erhältst nun einen Bestätigungslink an Deine Email-Adresse. Bitte klicke auf diesen Link, um Deine Registrierung abzuschließen.');</p>-->
        </div>
        <div class="buttons">
            <div class="ok popup-close"><?= Yii::t('app', 'OK') ?></div>
        </div>
    </div>
</div>
