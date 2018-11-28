<?php

namespace app\controllers;

use Yii;
use \app\models\Info;
use \app\models\InfoComment;

class ApiInfoCommentController extends \app\components\ApiController {

    public function actionAdd() {
        $data = Yii::$app->request->getBodyParam('infoComment');
        $infoData = Yii::$app->request->getBodyParam('infoPopupData');

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $model=new InfoComment();
        $model->user_id=Yii::$app->user->id;
        $model->info_id = $infoData['id'];
        $model->dt=(new \app\components\EDateTime())->sqlDateTime();

        $model->load($data,'');
        $model->file_id=\app\models\File::getIdFromProtected($data['file_id']);

        if ($model->validate()) {
            $model->save();
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            return ['infoComment'=>$data];
        }

        $trx->commit();

        return [
            'result'=>true
        ];
    }






}
