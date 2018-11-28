<?php

namespace app\components;

use yii\web\AssetBundle;


class IcoPaymentAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web/static/';

    public $css = [
        'js/fancybox/jquery.fancybox.css',
        'ico-payment/vendor.bundle.css',
        'ico-payment/main.css'
    ];

    public $js = [
        'js/icheck.min.js',
        'js/fancybox/jquery.fancybox.js',
        'ico-payment/script.js',
        'ico-payment/main.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'rmrevin\yii\fontawesome\AssetBundle'
    ];
}