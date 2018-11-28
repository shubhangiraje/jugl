<?php

use yii\helpers\Html;

?>

<div class="navbar navbar-expand-lg" id="mainnav">
    <nav class="container">
        <a class="navbar-brand animated fadeInDown" data-animate="fadeInDown" data-delay=".65" href="" style="visibility: visible; animation-delay: 0.65s;">
            <img class="logo logo-light" alt="logo" src="/static/ico-payment/images/logo-white.png" srcset="/static/ico-payment/images/logo-white2x.png 2x">
        </a>
        <!--<div class="language-switcher animated fadeInDown" data-animate="fadeInDown" data-delay=".75" style="visibility: visible; animation-delay: 0.75s;">
            <a href="#" data-toggle="dropdown">EN</a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                <a class="dropdown-item" href="#">DE</a>
                <a class="dropdown-item" href="#">DE-AT</a>
            </div>
        </div>-->

        <?php if (Yii::$app->user->id) { ?>
            <div class="header-balance-token animated fadeInDown" data-animate="fadeInDown" data-delay="1.2" style="visibility: visible; animation-delay: 1.2s;">
                <div>
                    <?= Yii::t('app', 'Tokenstand:') ?> <?= floatval(Yii::$app->user->identity->balance_token) ?>
                </div>
                <div>
                    <?= Yii::t('app', 'Festgelegt:') ?> <?= floatval(Yii::$app->db->createCommand("select sum(`sum`) from token_deposit where user_id=:user_id",[':user_id'=>Yii::$app->user->id])->queryScalar()) ?>
                </div>
            </div>
        <?php } ?>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggle">
            <span class="navbar-toggler-icon">
                <span class="ti ti-align-justify"></span>
            </span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarToggle">
            <ul id="menu-primary" class="navbar-nav animated remove-animation fadeInDown" data-animate="fadeInDown" data-delay=".9" style="visibility: visible; animation-delay: 0.9s;">
                <li class="nav-item"><a href="/" class="nav-link menu-link"><?= Yii::t('app', 'Home') ?></a></li>
                <li class="nav-item"><a href="http://juglcoin.com/#intro" class="nav-link menu-link"><?= Yii::t('app', 'What is Jugl') ?></a></li>
                <li class="nav-item"><a href="http://juglcoin.com/#team" class="nav-link menu-link"><?= Yii::t('app', 'Team') ?></a></li>
                <li class="nav-item"><a href="http://juglcoin.com/#app" class="nav-link menu-link"><?= Yii::t('app', 'Get the app') ?></a></li>
                <li class="nav-item"><a href="http://juglcoin.com/#contact" class="nav-link menu-link"><?= Yii::t('app', 'Contact') ?></a></li>
            </ul>
            <?php if (!Yii::$app->user->id) { ?>
            <ul class="navbar-nav navbar-btns animated remove-animation fadeInDown" data-animate="fadeInDown" data-delay=".9" style="visibility: visible; animation-delay: 0.9s;">
                <li class="nav-item"><a class="nav-link btn btn-sm btn-outline menu-link" href="https://jugl.loc22/become-member"><?= Yii::t('app', 'Sign up') ?></a></li>
                <li class="nav-item"><a class="nav-link btn btn-sm btn-outline menu-link" href="<?= \yii\helpers\Url::to(['ico-site/login']) ?>"><?= Yii::t('app', 'Log in') ?></a></li>
            </ul>
            <?php } else { ?>
                <ul class="navbar-nav navbar-btns animated remove-animation fadeInDown" data-animate="fadeInDown" data-delay=".9" style="visibility: visible; animation-delay: 0.9s;">
                    <li class="nav-item"><a class="nav-link btn btn-sm btn-outline menu-link" href="<?= \yii\helpers\Url::to(['ico-site/logout']) ?>"><?= Yii::t('app', 'Logout') ?></a></li>
                </ul>
            <?php } ?>
        </div>
    </nav>
</div>
