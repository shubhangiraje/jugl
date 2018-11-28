
<?php

use app\components\AppAsset;
use yii\helpers\Url;
use yii\helpers\Html;

AppAsset::register($this);

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" prefix="og: http://ogp.me/ns#" >
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <?= $this->registerMetaTag(['property' => 'og:url', 'content' => 'http://jugl.loc22/']) ?>
    <?= $this->registerMetaTag(['property' => 'og:type', 'content' => 'website']) ?>
    <?= $this->registerMetaTag(['property' => 'og:site_name', 'content' => 'Jugl.net']) ?>
    <?= $this->registerMetaTag(['property' => 'og:title', 'content' => Yii::t('app', 'Jugl.net - Noch nie war Geld verdienen so einfach!')]) ?>
    <?= $this->registerMetaTag(['property' => 'og:description', 'content' => Yii::t('app', 'Der folgende Link ist Dein persönlicher Einladungslink für jugl.net. Nutze diesen Link in Emails, Foren, auf Facebook, Twitter etc. und Du wirst schneller als Du denkst ein beträchtliches Guthaben besitzen.')]) ?>
    <?= $this->registerMetaTag(['property' => 'og:image', 'content' => 'http://jugl.loc22/static/images/site/logo-jugl.png']) ?>

    <?= $this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']) ?>
    <?= $this->registerMetaTag(['name' => 'twitter:site', 'content' => '@JuglNet']) ?>
    <?= $this->registerMetaTag(['name' => 'twitter:title', 'content' => Yii::t('app', 'Jugl.net - Noch nie war Geld verdienen so einfach!')]) ?>
    <?= $this->registerMetaTag(['name' => 'twitter:description', 'content' => Yii::t('app', 'Der folgende Link ist Dein persönlicher Einladungslink für jugl.net. Nutze diesen Link in Emails, Foren, auf Facebook, Twitter etc. und Du wirst schneller als Du denkst ein beträchtliches Guthaben besitzen.')]) ?>
    <?= $this->registerMetaTag(['name' => 'twitter:image', 'content' => 'http://jugl.loc22/static/images/site/logo-jugl.png']) ?>

    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "ca-pub-5908295137597899",
            enable_page_level_ads: true
        });
    </script>

    <?php $this->head() ?>
	<meta name="dailymotion-domain-verification" content="dm3ub8q4mk1sppgb2" />
	<meta name="dailymotion-domain-verification" content="dmuqk10od4g84ih5x" />
	<meta name="viewport" content="width=1200, initial-scale=1.0">
</head>

<body>
    <?php $this->beginBody() ?>

    <div id="wrapper">
        <?php if($this->context->route!='site/index') { ?>
            <div>
                <div id="header">
                    <div class="container clearfix">
                        <div class="icon-nav-menu">
                            <span class="strip"></span>
                        </div>
                        <div class="header-lang"><div class="lang <?= Yii::$app->session['language'] ?> open-popup" data-open="lang-popup"></div></div>
                        <div id="menu" class="nav-menu">
                            <ul>
                                <li><a href="<?=Url::to(['/'])?>"><?= Yii::t('app', 'Home') ?></a></li>
                                <li><a href="<?=Url::to(['site/view', 'view'=>'wie-funktioniert'])?>"><?= Yii::t('app', 'Wie funktioniert jugl.net') ?></a></li>

                                <?php /* <li><a href="<?=Url::to(['registration/index'])?>"><?= Yii::t('app', 'Registrierung') ?></a></li> */ ?>

                                <?php if(Yii::$app->user->isGuest) { ?>
                                    <li><a href="<?=Url::to(['site/login'])?>"><?= Yii::t('app', 'Login') ?></a></li>
                                <?php } else { ?>
                                    <li><a href="<?=Url::to(['site/logout'])?>"><?= Yii::t('app', 'Logout') ?></a></li>
                                <?php } ?>

                                <?php /* <li><a href="<?=Url::to(['site/become-member'])?>"><?=Yii::t('app', 'Mitglied werden')?></a></li> */ ?>

                            </ul>
                        </div>

                        <div id="logo">
                            <a href="<?=Url::to(['/'])?>"><?= Html::img('/static/images/account/account-small-logo.png', ['alt'=>'logo']) ?></a>
                        </div>

                    </div>

                </div>
            </div>
        <?php } ?>

        <?= $content ?>

        <?= $this->context->renderPartial("/layouts/_footer") ?>

    </div>


    <div class="popup-wrapper lang-popup">
        <div class="popup-content">
            <div class="popup-close-btn popup-close"></div>
            <div class="popup-box">
                <form action="<?=Url::to(['site/set-language'])?>" method="post">
                    <div class="field-lang">
                        <input class="lang en" name="language" type="submit" value="en"/>
                        <label>en</label>
                    </div>
                    <div class="field-lang">
                        <input class="lang de" name="language" type="submit" value="de"/>
                        <label>de</label>
                    </div>
                    <div class="field-lang">
                        <input class="lang ru" name="language" type="submit" value="ru"/>
                        <label>ru</label>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="popup-wrapper cookies-popup">
        <div class="popup-content">
            <div class="popup-close-btn popup-close"></div>
            <div class="popup-box">
                <?= Yii::t('app', 'In Deinem Browser sind Cookies deaktiviert. Um jugl.net nutzen zu können solltest Du Cookies in Deinem Browser erlauben.') ?>
            </div>
            <div class="buttons">
                <div class="ok popup-close"><?= Yii::t('app', 'Ok') ?></div>
            </div>
        </div>
    </div>

<?php $this->endBody() ?>

</body>
</html>

<?php $this->endPage() ?>
