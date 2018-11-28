<?php

namespace app\controllers;

use Yii;
use app\models\PayOutRequest;
use app\models\PayOutRequestSearch;
use app\components\AdminController;
use yii\web\NotFoundHttpException;


class AdminPayOutRequestController extends AdminController
{
    public function actionIndex()
    {
        $searchModel = new PayOutRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDecline($id,$returl)
    {
        $this->findModel($id)->decline();

        return $this->redirect($returl);
    }

    public function actionAccept($id,$returl)
    {
        $this->findModel($id)->accept();

        return $this->redirect($returl);
    }

    public function actionProcess($id,$returl)
    {
        $this->findModel($id)->process();

        return $this->redirect($returl);
    }

    /**
     * Finds the Admin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Admin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PayOutRequest::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}