<?php
use yii\helpers\Html;
use app\components\AppAsset;

/**
 * @var \yii\web\View $this
 * @var string $content
 */
//AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" ng-app="Jugl" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=1200, initial-scale=1.0">
    <?= Html::csrfMetaTags() ?>

    <?= $this->registerMetaTag(['property' => 'og:url', 'content' => 'http://jugl.loc22/']) ?>
    <?= $this->registerMetaTag(['property' => 'og:type', 'content' => 'website']) ?>
    <?= $this->registerMetaTag(['property' => 'og:site_name', 'content' => 'Jugl.net']) ?>
    <?= $this->registerMetaTag(['property' => 'og:title', 'content' => Yii::t('app', 'Jugl.net - Noch nie war Geld verdienen so einfach!')]) ?>
    <?= $this->registerMetaTag(['property' => 'og:description', 'content' => Yii::t('app', 'Der folgende Link ist Dein persönlicher Einladungslink für jugl.net. Nutze diesen Link in Emails, Foren, auf Facebook, Twitter etc. und Du wirst schneller als Du denkst ein beträchtliches Guthaben besitzen.')]) ?>
    <?= $this->registerMetaTag(['property' => 'og:image', 'content' => 'http://jugl.net/static/images/site/logo-jugl.png']) ?>

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
</head>
<body ng-class="{'fullscreen': modalService.isShowInfo}">
<div ng-cloak id="wrapper" class="account-page">
    <?php $this->beginBody() ?>
    <?= $content ?>
    <?php $this->endBody() ?>
    <?php include('_footer.php'); ?>
</div>
</body>
</html>
<?php $this->endPage() ?>
