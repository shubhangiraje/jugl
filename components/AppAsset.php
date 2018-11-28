<?php

namespace app\components;

use yii\web\AssetBundle;


class AppAsset extends AssetBundle
{
    public $basePath = '@webroot/static';
    public $baseUrl = '@web/static';
    public $css = [
        'css/reset.css',
        'css/style.css',
        'css/footer.css',
		'css/css/font-awesome.min.css',
        '//fonts.googleapis.com/css?family=Ubuntu:300,300i,400,400i,500,700'
    ];
    public $js = [
        'js/main.js',
        'js/icheck.min.js',
        'js/jquery.selectric.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
