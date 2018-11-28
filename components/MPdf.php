<?php

namespace app\components;

use Yii;
use kartik\mpdf\Pdf;
use yii\base\ViewContextInterface;


define('_MPDF_TTFONTDATAPATH',Yii::getAlias('@runtime/mpdf/ttfontdatapath/'));
define('_MPDF_TEMP_PATH',Yii::getAlias('@runtime/mpdf/temp/'));

if (!file_exists(_MPDF_TTFONTDATAPATH)) {
    mkdir(_MPDF_TTFONTDATAPATH,0777,true);
}

if (!file_exists(_MPDF_TEMP_PATH)) {
    mkdir(_MPDF_TEMP_PATH,0777,true);
}

class MPdf extends Pdf implements ViewContextInterface {

    public function getViewPath()
    {
        return '@app/views/documents';
    }

    private static function getInstance($options,$mpdfConfigurationCallback) {

        $timezone=date_default_timezone_get();

        $instance=new self(array_merge([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'defaultFont' => 'dejavusanscondensed'
        ],$options));

        if ($mpdfConfigurationCallback) {
            call_user_func($mpdfConfigurationCallback,$instance);
        }

        date_default_timezone_set($timezone);

        return $instance;
    }

    public function setHtmlContentHelper($m) {
        $this->cssInline.=$m[1];
        return '';
    }

    private function setHtmlContent($template,$data) {
        $view=Yii::createObject([
            'class'=>\yii\web\View::className()
        ]);

        $html=$view->render($template,$data,$this);

        $this->content=preg_replace_callback('%<style>(.*?)</style>%si',[$this,'setHtmlContentHelper'],$html);
    }

    public static function download($template,$data,$filename,$options=[],$mpdfConfigurationCallback=null) {
        $pdf=static::getInstance(array_merge($options,[
            'destination' => static::DEST_FILE,
            'filename' => $filename,
        ]),$mpdfConfigurationCallback);

        $pdf->setHtmlContent($template,$data);

        $pdf->render();
    }

    public static function saveAsFile($template,$data,$filename,$options=[]) {
        $pdf=static::getInstance(array_merge($options,[
            'destination' => static::DEST_STRING,
            'filename' => $filename,
        ]));

        $pdf->setHtmlContent($template,$data);

        $document=$pdf->render();

        $file=new \app\models\File();
        $file->dt=(new EDateTime())->sqlDateTime();
        $file->size=strlen($document);

        preg_match("%([^/]*)\\.(.{1,5})$%",$filename,$m);
        $file->name=$m[0];
        $file->ext=$m[2];
        $file->link='fake';
        $file->save();

        $dir =  $file->calculateDir();
        if (!file_exists($dir)) {
            if (!@mkdir($dir, 0755, true)) {
                die("Can't create dir '$dir'\n");
            }
        }

        $fn = $file->id . '_' . $file->getProtectionCode() . '.' . $file->ext;

        $fullFn = $dir.$fn;

        if (file_put_contents($fullFn,$document)!=$file->size) {
            die("Can't save file $fullFn\n");
        }

        $file->link = $file->calculateUrlDir() . $fn;
        $file->save();

        return $file;
    }
}