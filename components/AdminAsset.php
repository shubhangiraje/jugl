<?php

namespace app\components;

use yii\web\AssetBundle;


class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web/static/';

    public $css = [
        'js/fancybox/jquery.fancybox.css',
        'admin/style.css'
    ];

    public $js = [
        'js/fancybox/jquery.fancybox.js',
        'admin/bootbox.min.js',
        'admin/script.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'rmrevin\yii\fontawesome\AssetBundle'
    ];
}