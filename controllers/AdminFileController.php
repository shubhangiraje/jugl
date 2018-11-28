<?php

namespace app\controllers;

use app\components\AdminController;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\db\Expression;
use app\components\Thumb;
use app\models\File;
use Yii;

class AdminFileController extends AdminController
{
    // needed for uploading images by ckeditor
    public $enableCsrfValidation=false;

    private function returnError($msg)
    {
        $data = array(array('error' => $msg));
        $this->returnData($data);
    }

    private function returnData($data)
    {
        echo json_encode($data);
        exit;
    }

    public function actionUpload()
    {
        $data = array();

        $file = UploadedFile::getInstanceByName('filedata');

        $model=File::upload($file);

        if (is_string($model)) {
            $this->returnError($model);
        } else {
            $data['id']=$model->id;
            $data['test']=$_REQUEST;
            if (preg_match('/^[a-zA-Z0-9_-]+$/', $_REQUEST['tpl'])) {
                $tplData=$_REQUEST['tplData'];
                $tplData['file']=$model;
                $data['tpl']=$this->renderPartial($_REQUEST['tpl'], $tplData);
            }
            $this->returnData($data);
        }
    }

    public function actionCkupload() {
        $data = array();

        $file = UploadedFile::getInstanceByName('upload');

        $model=File::upload($file);

        $msg='';
        $url='';
        if (is_string($model)) {
            $msg=$model;
        } else {
            $url=$model->url;//Thumb::createUrl($model->getUrl(),'ckimage');
        }

        echo "<script>window.parent.CKEDITOR.tools.callFunction('{$_REQUEST['CKEditorFuncNum']}','$url','$msg');</script>";
        exit;
    }
}
