<?php

namespace app\controllers;

use Yii;
use app\models\SearchRequestComment;
use app\models\SearchRequestCommentSearch;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class AdminSearchRequestCommentController extends AdminController {

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->save();
                return $this->redirect(['admin-search-request/update','id'=>$model->search_request_id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->delete();
        return $this->pjaxRefresh();
    }

    protected function findModel($id) {
        if (($model = SearchRequestComment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
