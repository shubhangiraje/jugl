<?php

namespace app\controllers;

use Yii;
use app\models\UserTeamFeedback;
use app\models\UserTeamFeedbackSearch;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminUserTeamFeedbackController implements the CRUD actions for UserTeamFeedback model.
 */
class AdminUserTeamFeedbackController extends AdminController {

    /**
     * Updates an existing UserTeamFeedback model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/admin-user/update', 'id' => $model->user->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserTeamFeedback model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {

        $trx=Yii::$app->db->beginTransaction();
        $model = $this->findModel($id);
        $model->delete();
        $model->updateUserRating();
        $trx->commit();

        return $this->pjaxRefresh();
    }

    /**
     * Finds the UserTeamFeedback model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserTeamFeedback the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserTeamFeedback::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
