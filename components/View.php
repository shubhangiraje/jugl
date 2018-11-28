<?php

namespace app\components;

class View extends \yii\web\View {

    public $defaultExtension='tpl';

    private function addModifyTimeToUrl($url) {
        $filename='.'.$url;
        if (file_exists($filename)) {
            $url.='?v='.filemtime($filename);
        }

        return $url;
    }

    public function registerCssFile($url, $depends = [], $options = [], $key = null) {
        parent::registerCssFile($this->addModifyTimeToUrl($url),$depends,$options,$key);
    }

    public function registerJsFile($url, $depends = [], $options = [], $key = null) {
        parent::registerJsFile($this->addModifyTimeToUrl($url),$depends,$options,$key);
    }
}
