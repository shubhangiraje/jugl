<?php

namespace app\controllers;

use app\models\base\User;
use Yii;
use app\models\UserInviteMeSearch;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminUserController implements the CRUD actions for User model.
 */
class AdminInviteMeController extends AdminController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete-request' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserInviteMeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

/*
    public function actionDeleteInviteMe($id)
    {
        $this->findModel($id)->delete();
        return $this->pjaxRefresh();
    }
*/

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = \app\models\InviteMe::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}