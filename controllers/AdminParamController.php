<?php

namespace app\controllers;

use Yii;
use app\models\Param;
use app\models\ParamValue;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;


class AdminParamController extends AdminController
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
        $model = new Param();
        $model->interest_id=$_REQUEST['id'];

        if ($model->load($_POST) && $model->validate()) {
           $model->save();
           return $this->redirect(['admin-interest/update','id'=>$model->interest_id]);
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
            return $this->redirect(['admin-interest/update','id'=>$model->interest_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => ParamValue::find()->andWhere(['param_id'=>$_REQUEST['id']]),
            'sort' => [
                'attributes' => ['sort_order'],
                'defaultOrder'=>['sort_order'=>SORT_ASC]
            ]
        ]);

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMove($id,$pos)
    {
        $model=$this->findModel($id);
        $model->move($pos, ['interest_id' => $model->interest_id]);
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
        if (($model = Param::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException();
        }
    }
}