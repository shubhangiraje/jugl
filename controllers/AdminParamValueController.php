<?php

namespace app\controllers;

use Yii;
use app\models\ParamValue;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;


class AdminParamValueController extends AdminController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    public function actionCreate()
    {
        $model = new ParamValue();
        $model->param_id=$_REQUEST['id'];

        if ($model->load($_POST) && $model->validate()) {
           $model->save();
           return $this->redirect(['admin-param/update','id'=>$model->param_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load($_POST) && $model->validate()) {
            $model->save();
            return $this->redirect(['admin-param/update','id'=>$model->param_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionMove($id,$pos)
    {
        $model=$this->findModel($id);
        $model->move($pos, ['param_id' => $model->param_id]);
        return $this->pjaxRefresh();
    }

    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
        } catch (\yii\base\Exception $e) {
            return $this->pjaxRefreshAlert(Yii::t('app',"Can't delete this item, it is use by another item(s)"));
        }
        return $this->pjaxRefresh();
    }

    protected function findModel($id)
    {
        if (($model = ParamValue::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException();
        }
    }
}