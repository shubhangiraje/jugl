<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\db\Expression;
use app\models\File;
use Yii;

class ApiFileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user' => 'user',
                'only' => ['upload'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'access2' => [
                'class' => AccessControl::className(),
                'user' => 'admin',
                'only' => ['ck-upload'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    private function returnError($msg)
    {
        $data = array(array('error' => $msg));
        $this->returnData($data);
    }

    private function returnData($data)
    {
        echo function_exists('json_encode') ? json_encode($data) : CJSON::encode($data);
        exit;
    }

    private function upload($file, $thumb = 'upload') {
        if ($file && ! $file->getHasError()) {
            $trx=Yii::$app->db->beginTransaction();

            $model=new File;
            $model->dt=new Expression('NOW()');
            $model->link='fake';
            $model->ext=strtolower(preg_replace('%^.*\.%','',$file->name));
            $model->name=$file->name;
            $model->size=$file->size;

            $model->save();


            $dir = $model->calculateDir();
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    $this->returnError(Yii::t('app', "Can't create dir %dir%", array('%dir%' => $dir)));
                }
            }

            $fn = $model->id . '_' . $model->getProtectionCode() . '.' . $file->extension;

            $fullFn = $dir . $fn;
/*
            if (!Thumb::process($file->tempName, $fullFn, $thumb)) {
                $this->returnError(Yii::t('app', "Can't resize and/or save file"));
            }
*/
            if (!$file->saveAs($fullFn)) {
                $this->returnError(Yii::t('app', "Can't save file"));
            }

            $model->link = $model->calculateUrlDir() . $fn;
            $model->save();

            $trx->commit();

            return $model;
        } else {
            return Yii::t('app', "Error while file upload");
        }
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        return json_encode($result);
    }

    public function actionUpload()
    {
        $data = array();

        $file = UploadedFile::getInstanceByName('file');
        //$model->filename = $file->getHasError();

        try {
            $model=$this->upload($file);
        } catch (Exception $e) {
            return ['error'=>$e->getMessage()];
        }

        if (is_string($model)) {
            $this->returnError($model);
        } else {
            $data=$model->toArray(['name','size','ext','link']);
            $data['id']=File::getProtectedId($model->id);
            foreach(explode(',',$_REQUEST['thumbs']) as $thumb) {
                $data['thumbs'][$thumb]=$model->getThumbUrl($thumb);
            }
            return $data;
        }
    }


    public function actionCkUpload() {
        $data = array();

        $file = UploadedFile::getInstanceByName('upload');

        try {
            $model=$this->upload($file);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        $msg='';
        $url='';
        if (is_string($model)) {
            $msg=$model;
        } else {
            $url=$model->url;//Thumb::createUrl($model->getUrl(),'ckimage');
        }

        echo "<script>window.parent.CKEDITOR.tools.callFunction('{$_REQUEST['CKEditorFuncNum']}','$url','$msg');</script>";
        Yii::$app->end();
    }

}
