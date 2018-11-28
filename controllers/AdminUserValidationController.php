<?php

namespace app\controllers;

use app\components\EDateTime;
use Yii;
use app\models\User;
use app\models\UserValidationSearch;
use app\components\AdminController;
use yii\web\NotFoundHttpException;


class AdminUserValidationController extends AdminController
{
    public function actionIndex()
    {
        $searchModel = new UserValidationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('validation');
        $now = new EDateTime();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {

                if ($model->validation_status==User::VALIDATION_STATUS_SUCCESS) {
                    $model->dt_status_change = $now->sqlDateTime();
                } else {
                    $model->dt_status_change = null;
                }

                $validation_failure_reason = '';
                if($model->validation_status==User::VALIDATION_STATUS_FAILURE && $model->validation_failure_reason) {
                    $validation_failure_reason = ': '.$model->validation_failure_reason;
                }
                $model->validation_changelog .= "\n".'['.$now->format('d.m.Y, H:i:s').']'.' '.Yii::$app->admin->identity->name.', '.$model->getValidationStatusLabel().$validation_failure_reason;

                $model->save();

                if ($model->validation_status==User::VALIDATION_STATUS_SUCCESS) {
                    Yii::$app->mailer->sendEmail($model,'validation-success',['model'=>$model]);
                    \app\models\UserEvent::addDocumentValidationSuccess($model);
                    $model->addRegistrationBonusToParent();
                }

                if ($model->validation_status==User::VALIDATION_STATUS_FAILURE) {
                    Yii::$app->mailer->sendEmail($model,'validation-failure',['model'=>$model]);
                }

                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}