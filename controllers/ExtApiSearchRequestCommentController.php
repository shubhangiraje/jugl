<?php

namespace app\controllers;

use Yii;
use app\components\EDateTime;
use app\models\SearchRequestComment;



class ExtApiSearchRequestCommentController extends \app\components\ExtApiController {

    public function actionSave() {
        $data=Yii::$app->request->getBodyParams()['comment'];

        $model=new SearchRequestComment();
        $model->create_dt=(new EDateTime())->sqlDateTime();
        $model->user_id = Yii::$app->user->id;

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $model->load($data,'');

        if ($model->validate()) {
            $model->save();
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['comment'=>$data];
        }

        $trx->commit();

        $commentData=$model->toArray(['id', 'comment']);
        $commentData['user']=$model->user->getShortData(['rating','feedback_count', 'packet']);
        $commentData['create_dt']=(new EDateTime())->js();

        return [
            'result'=>true,
            'comments'=>SearchRequestComment::getComments($data['search_request_id'])
        ];

    }


    public function actionResponseUpdate() {
        $model=SearchRequestComment::findOne(['id'=>Yii::$app->request->getQueryParam('id')]);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException();
        }

        $data=$model->toArray(['id','response']);
        return [
            'comment'=>$data
        ];
    }

    public function actionResponseSave() {
        $data=Yii::$app->request->getBodyParams()['comment'];

        $model=SearchRequestComment::findOne(['id'=>$data['id']]);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException();
        }

        $model->response_dt=(new EDateTime())->sqlDateTime();

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $model->setScenario('response-update');
        $model->load($data,'');

        if ($model->validate()) {
            $model->save();
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['comment'=>$data];
        }

        $trx->commit();

        $commentData=$model->toArray(['response','id']);
        $commentData['response_dt']=(new EDateTime())->js();

        return ['result'=>true,'comment'=>$commentData];
    }

}
