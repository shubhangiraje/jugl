<?php

namespace app\controllers;

use Yii;


class AdminRegistrationIpController extends \app\components\AdminController {
    public function actionIndex() {
        $searchModel=new \app\models\RegistrationIpStatsSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }
}