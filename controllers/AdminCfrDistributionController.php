<?php

namespace app\controllers;

use app\models\base\CfrDistribution;
use app\models\CfrDistributionSearch;
use app\models\CfrDistributionUserSearch;
use Yii;
use app\components\AdminController;
use app\models\DefaultText;
use yii\web\NotFoundHttpException;


class AdminCfrDistributionController extends AdminController {

    public function actionIndex() {
        $searchModel = new CfrDistributionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'=>$searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {

        $searchModel = new CfrDistributionUserSearch();
        $dataProvider = $searchModel->search($id, Yii::$app->request->queryParams);

        return $this->render('view', [
            'searchModel'=>$searchModel,
            'dataProvider' => $dataProvider,
            'cfrDistribution'=>$this->findModel($id)
        ]);
    }

    protected function findModel($id) {
        if (($model = CfrDistribution::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
