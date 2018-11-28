<?php

namespace app\controllers;

use Yii;
use app\models\OfferRequest;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminSearchRequestController implements the CRUD actions for SearchRequest model.
 */
class AdminOfferRequestController extends AdminController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete-user' => ['post'],
                    'block-user' => ['post'],
                ],
            ],
        ]);
    }

    public function actionDelete($id)
    {
        $model=$this->findModel($id);
        $model->status=OfferRequest::STATUS_DELETED;
        $model->save();
        return $this->pjaxRefresh();
    }

    /**
     * Updates an existing SearchRequest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->save();
                return $this->redirect(['admin-offer/update','id'=>$model->offer_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the SearchRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SearchRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OfferRequest::find()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}