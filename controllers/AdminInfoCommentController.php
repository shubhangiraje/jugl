<?php

namespace app\controllers;

use app\models\Info;
use Yii;
use app\models\InfoComment;
use app\models\InfoCommentSearch;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class AdminInfoCommentController extends AdminController {

    public function actionList($id) {
        $searchModel = new InfoCommentSearch();
        $dataProvider = $searchModel->search($id, Yii::$app->request->queryParams);
        $info = Info::findOne($id);
        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'info'=>$info
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['list', 'id'=>$model->info->id]);
        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->status = InfoComment::STATUS_DELETED;
        $model->save();
        return $this->redirect(['list', 'id'=>$model->info->id]);
    }


    protected function findModel($id) {
        if (($model = InfoComment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
