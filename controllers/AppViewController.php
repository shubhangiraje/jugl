<?php

namespace app\controllers;

use app\components\Language;
use yii\web\Controller;
use Yii;
use yii\helpers\FileHelper;

class AppViewController extends Controller
{
    public function actionView($view)
    {
        // prevent template caching for easy updating
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        return $this->renderPartial($view);
    }

    public function actionAll() {

        Language::setLanguage();

        $files=FileHelper::findFiles(Yii::getAlias('@app/views/app-view'));

        $code='angular.module("templates",[]).run(["$templateCache",function($templateCache){'."\n";
        foreach($files as $fullFileName) {
            $fileName=basename($fullFileName);
            $fileContent=$this->renderPartial($fileName);

            // minify code
            $fileContent=preg_replace('/\s+/',' ',$fileContent);
            $fileContent=preg_replace('/> </','><',$fileContent);

            $code.='$templateCache.put(\'/app-view/'.preg_replace('/\.[^.]*$/','',$fileName).'\',\''.
                str_replace("'","\\'",$fileContent)."');\n";
        }

        $code.="}]);\n";

        // prevent template caching for easy updating
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // set content type
        Yii::$app->response->format=\yii\web\Response::FORMAT_RAW;
        Yii::$app->response->getHeaders()->set('Content-type','application/x-javascript');
        Yii::$app->response->getHeaders()->set('Content-length',strlen($code));

        return $code;
    }

}
