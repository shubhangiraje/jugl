<?php

namespace app\controllers;

use app\models\OfferRequest;
use app\models\SearchRequestOffer;
use Yii;
use app\models\UserFeedback;
use app\models\UserFeedbackSearch;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminUserFeedbackController implements the CRUD actions for UserFeedback model.
 */
class AdminUserFeedbackController extends AdminController {

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->setScenario('admin-update');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin-user/update', 'id' => $model->user->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserFeedback model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {

        $trx=Yii::$app->db->beginTransaction();
        $model = $this->findModel($id);
        OfferRequest::deleteUserRating($id);
        SearchRequestOffer::deleteUserRating($id);
        $model->secondUser->updateStatAwaitingFeedbacks();
        $model->delete();
        $model->updateUserRating();
        $trx->commit();

        return $this->pjaxRefresh();
    }

    /**
     * Finds the UserFeedback model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserFeedback the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = UserFeedback::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
