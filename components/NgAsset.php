<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\components;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class NgAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web/static/';

    public $css = [
        '//fonts.googleapis.com/css?family=Ubuntu:400,500,300,700',
        'css/reset.css',
        'css/footer.css',
        'css/account.css',
        'css/messenger.css',
        'css/profile.css',
        'css/smiles.css',
        'css/jquery.jscrollpane.css',
		//'css/flag-icon.css',
		'css/sprite-flags-32x32.css',
        'build/fancybox/jquery.fancybox.css'
    ];

    public $js = [
        'build/bower_components.js',
        'js/ZeroClipboard.js',
        'js/angular-bootstrap-multiselect.js',
        'js/jquery.restable.js',
        'js/ui-bootstrap-custom-tpls-0.12.0.min.js',
        'build/app.js',
        'build/translations.js',
		'js/glomex_embed.js',
		'js/glomex.js',
        '../app-view/all'
    ];
	
	
    public $jsOptions = [
        'position'=>\yii\web\View::POS_BEGIN,
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
