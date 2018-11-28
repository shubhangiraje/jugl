<?php
use yii\helpers\Html;
use app\components\IcoPaymentAsset;

/* @var $this \yii\web\View */
/* @var $content string */

IcoPaymentAsset::register($this);
?>

<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
        <?php $this->head() ?>
    </head>
    <body>

    <?php $this->beginBody() ?>

    <?=$content?>

    <div class="section footer-section footer-particle section-pad-sm section-bg-dark">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-xl-4 res-l-bttm">
                    <a class="footer-brand animated fadeInUp" data-animate="fadeInUp" data-delay="0" href="" style="visibility: visible;">
                        <img class="logo logo-light" alt="logo" src="/static/ico-payment/images/logo-white.png" srcset="/static/ico-payment/images/logo-white2x.png 2x">
                    </a>
                    <ul class="social">
                        <li class="animated fadeInUp" data-animate="fadeInUp" data-delay=".1" style="visibility: visible; animation-delay: 0.1s;">
                            <a href=""><em class="fa fa-facebook"></em></a>
                        </li>
                        <li class="animated fadeInUp" data-animate="fadeInUp" data-delay=".2" style="visibility: visible; animation-delay: 0.2s;">
                            <a href=""><em class="fa fa-twitter"></em></a>
                        </li>
                        <li class="animated fadeInUp" data-animate="fadeInUp" data-delay=".3" style="visibility: visible; animation-delay: 0.3s;">
                            <a href=""><em class="fa fa-youtube-play"></em></a>
                        </li>
                        <li class="animated fadeInUp" data-animate="fadeInUp" data-delay=".5" style="visibility: visible; animation-delay: 0.5s;">
                            <a href=""><em class="fa fa-bitcoin"></em></a>
                        </li>

                    </ul>
                </div>
                <div class="col-sm-6 col-xl-4 res-l-bttm">
                    <div class="footer-subscription">
                        <h6 class="animated fadeInUp" data-animate="fadeInUp" data-delay=".6" style="visibility: visible; animation-delay: 0.6s;">Subscribe to our newsleter</h6>
                        <form id="subscribe-form" action="#" method="post" class="subscription-form animated fadeInUp" data-animate="fadeInUp" data-delay=".7" novalidate="novalidate" style="visibility: visible; animation-delay: 0.7s;">
                            <input type="text" name="youremail" class="input-round required email" placeholder="Enter your email" aria-required="true">
                            <input type="text" class="d-none" name="form-anti-honeypot" value="">
                            <button type="button" class="btn btn-plane">Subscribe</button>
                            <div class="subscribe-results"></div>
                        </form>
                    </div>
                </div>
                <div class="col-xl-4">
                    <ul class="link-widget animated fadeInUp" data-animate="fadeInUp" data-delay=".8" style="visibility: visible; animation-delay: 0.8s;">
                        <li><a href="" class="menu-link"><?= Yii::t('app', 'What is Jugl') ?></a></li>
                        <li><a href="" class="menu-link"><?= Yii::t('app', 'Get the App') ?></a></li>
                        <li><a href="" class="menu-link"><?= Yii::t('app', 'Join Us') ?></a></li>
                        <li><a href="" class="menu-link"><?= Yii::t('app', 'Tokens') ?></a></li>
                        <li><a href="" class="menu-link"><?= Yii::t('app', 'Whitepaper') ?></a></li>
                        <li><a href="" class="menu-link"><?= Yii::t('app', 'Contact') ?></a></li>
                        <li><a href="" class="menu-link"><?= Yii::t('app', 'News') ?></a></li>
                        <li><a href="" class="menu-link"><?= Yii::t('app', 'Teams') ?></a></li>
                        <li><a href="" class="menu-link"><?= Yii::t('app', 'FAQ') ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="gaps size-2x"></div>
            <div class="row">
                <div class="col-md-7">
                <span class="copyright-text animated fadeInUp" data-animate="fadeInUp" data-delay=".9" style="visibility: visible; animation-delay: 0.9s;">
                    Jugl © 2018. <?= Yii::t('app', 'Made with') ?><a href="http://pimentagroup.de" target="_blank"> <i class="fa fa-heart" aria-hidden="true" style="color:#C51317;"></i> </a>by Pimenta Group.
                    <span><?= Yii::t('app', 'All trademarks and copyrights belong to their respective owners.') ?></span>
                </span>
                </div>
                <div class="col-md-5 text-right mobile-left">
                    <ul class="footer-links animated fadeInUp" data-animate="fadeInUp" data-delay="1" style="visibility: visible; animation-delay: 1s;">
                        <li><a href=""><?= Yii::t('app', 'Privacy Policy') ?></a></li>
                        <li><a href=""><?= Yii::t('app', 'Terms &amp; Conditions') ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php $this->registerJsFile('/static/ico-payment/jquery.bundle.js'); ?>


    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>