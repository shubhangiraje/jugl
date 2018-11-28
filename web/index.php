<?php
//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(1);

$GLOBALS['startRequestTime']=microtime(true);

$siteConfigFile = require(__DIR__ . '/../config/sites.php');

defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');
require(__DIR__ . '/../config/'.$siteConfigFile);

function hasAccess($route,$trueValue=true,$falseValue=false) {
    return Yii::$app->admin->identity->hasAccess($route) ? $trueValue:$falseValue;
}

function menuItemWithAccessCheck($item) {
    return hasAccess($item['url'][0]) ? [$item]:[];
}

function hasCurrentActionPostAccess() {
    return Yii::$app->admin->identity->hasAccess(Yii::$app->controller->route,'POST');
}

(new yii\web\Application($config))->run();
