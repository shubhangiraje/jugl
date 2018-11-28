<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\db\Expression;
use app\models\ChatFile;
use Yii;

class ApiChatFileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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

    private function upload($file) {
        if ($file && ! $file->getHasError()) {
            $trx=Yii::$app->db->beginTransaction();

            $model=new ChatFile;
            $model->user_id=Yii::$app->user->id;
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
        $file = UploadedFile::getInstanceByName('file');

        try {
            $model=$this->upload($file);
        } catch (Exception $e) {
            return ['error'=>$e->getMessage()];
        }

        if (is_string($model)) {
            $this->returnError($model);
        } else {
            $data=$model->toArray(['name','size','ext','link']);
            $data['id']=ChatFile::getProtectedId($model->id);
            $data['thumbs']['chat']=$model->getThumbUrl('chat');

            return $data;
        }
    }
}
