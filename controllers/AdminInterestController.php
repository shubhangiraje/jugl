<?php

namespace app\controllers;

use Yii;
use app\models\Interest;
use app\models\Param;

use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;


class AdminInterestController extends AdminController
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

    public function actionIndex()
    {
        $conditions=['parent_id'=>$_REQUEST['id']];
        if (!$conditions['parent_id']) {
            $type=$_REQUEST['type'];
            $conditions['type']=$_REQUEST['type'];
        } else {
            $type=Interest::findOne($conditions['parent_id'])->type;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Interest::find()->andWhere($conditions)->with(['file']),
            'sort' => [
                'attributes' => ['sort_order'],
                'defaultOrder'=>['sort_order'=>SORT_ASC]
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'type'=>$type
        ]);
    }

    public function actionCreate()
    {
        $model = new Interest();
        $model->parent_id=$_REQUEST['id'];
        $model->type=!$model->parent ? $_REQUEST['type']:$model->parent->type;

        if ($model->load($_POST) && $model->validate()) {
           $model->save();
           return $this->redirect($model->parent_id ? ['update','id'=>$model->parent_id]:['index','type'=>$model->type]);
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
            return $this->redirect($model->parent_id ? ['update','id'=>$model->parent_id]:['index','type'=>$model->type]);
        }

        $interestsDataProvider = new ActiveDataProvider([
            'query' => Interest::find()->andWhere(['parent_id'=>$_REQUEST['id']])->with(['file']),
            'sort' => [
                'attributes' => ['sort_order'],
                'defaultOrder'=>['sort_order'=>SORT_ASC]
            ]
        ]);

        $paramsDataProvider = new ActiveDataProvider([
            'query' => Param::find()->andWhere(['interest_id'=>$_REQUEST['id']]),
            'sort' => [
                'attributes' => ['sort_order'],
                'defaultOrder'=>['sort_order'=>SORT_ASC]
            ]
        ]);

        $interestParamValueDataProvider = new ActiveDataProvider([
            'query' => \app\models\InterestParamValue::find()->andWhere(['interest_id'=>$_REQUEST['id']])->with(['param','paramValue']),
        ]);

        return $this->render('update', [
            'model' => $model,
            'paramsDataProvider' => $paramsDataProvider,
            'interestsDataProvider' => $interestsDataProvider,
            'interestParamValueDataProvider' => $interestParamValueDataProvider
        ]);
    }

    public function actionMove($id,$pos)
    {
        $model=$this->findModel($id);
        $model->move($pos, ['parent_id' => $model->parent_id, 'type'=>$model->type]);
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
        if (($model = Interest::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException();
        }
    }
}