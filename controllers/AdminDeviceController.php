<?php

namespace app\controllers;


use app\models\KnownDevice;
use Yii;
use yii\filters\VerbFilter;
use app\models\KnownDeviceSearch;
use yii\web\NotFoundHttpException;


class AdminDeviceController extends \app\components\AdminController {

    public function behaviors() {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    public function actionIndex() {
        $searchModel = new KnownDeviceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionDelete($id) {
        $model =  $this->findModel($id);
        $model->delete();
        return $this->redirect('index');
    }

    protected function findModel($id) {
        if (($model = KnownDevice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
