<?php

namespace app\controllers;

use Yii;
use app\models\InterestParamValue;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


class AdminInterestParamValueController extends AdminController
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

    public function actionParamValues() {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $param_id = $parents[0];
                $data=[];
                foreach(\app\models\Param::findOne($param_id)->paramValues as $pv) {
                    $data[]=['id'=>$pv->id,'name'=>strval($pv)];
                }
                echo \yii\helpers\Json::encode(['output'=>$data, 'selected'=>'']);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }
    public function actionCreate()
    {
        $model = new InterestParamValue();
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

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionMove($id,$pos)
    {
        $model=$this->findModel($id);
        $model->move($pos, ['interest_id' => $model->param_id]);
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
        if (($model = InterestParamValue::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException();
        }
    }
}