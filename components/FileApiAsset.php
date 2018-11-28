<?php

namespace app\components;

use yii\web\AssetBundle;


class FileApiAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@vendor/rubaxa/fileapi';
    /**
     * @inheritdoc
     */
    public $js = [
        'FileAPI/FileAPI.min.js',
        'FileAPI/FileAPI.exif.js',
        'jquery.fileapi.min.js'
    ];
    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}