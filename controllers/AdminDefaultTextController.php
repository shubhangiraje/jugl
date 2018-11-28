<?php

namespace app\controllers;


use Yii;
use app\components\AdminController;
use yii\bootstrap\ActiveForm;
use yii\filters\VerbFilter;
use app\models\DefaultTextSearch;
use app\models\DefaultText;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AdminDefaultTextController extends AdminController {

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

    public function actionIndex($category) {

        if(!in_array($category, array_keys(DefaultText::getCategoryList()))){
            return $this->redirect('/admin');
        }

        $searchModel = new DefaultTextSearch();
        $dataProvider = $searchModel->search($category, Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate($category) {
        $model = new DefaultText();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->category = $category;
            $model->save();
            return $this->redirect(['index', 'category'=>$model->category]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            return $this->redirect(['index', 'category'=>$model->category]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id) {
        $model =  $this->findModel($id);
        $clone = clone $model;
        $model->delete();
        return $this->redirect(['index', 'category'=>$clone->category]);
    }

    protected function findModel($id) {
        if (($model = DefaultText::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
