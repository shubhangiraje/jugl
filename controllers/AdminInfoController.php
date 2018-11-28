<?php

namespace app\controllers;

use Yii;
use app\components\AdminController;
use app\models\Info;
use yii\web\NotFoundHttpException;
use app\models\InfoSearch;


class AdminInfoController extends AdminController {

    public function actionIndex() {
        $searchModel = new InfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->save();
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id) {
        if (($model = Info::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
