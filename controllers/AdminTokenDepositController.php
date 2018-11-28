<?php

namespace app\controllers;

use Yii;
use app\models\TokenDepositSearch;
use app\components\AdminController;

/**
 * AdminUserController implements the CRUD actions for User model.
 */
class AdminTokenDepositController extends AdminController
{
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TokenDepositSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}