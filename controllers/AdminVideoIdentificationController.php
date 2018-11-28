<?php

namespace app\controllers;

use app\models\TrollboxMessageVote;
use Yii;
use app\models\TrollboxMessage;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

/**
 * AdminUserController implements the CRUD actions for User model.
 */
class AdminVideoIdentificationController extends AdminController
{
    public function actionIndex()
    {
        $searchModel = new \app\models\TrollboxMessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, TrollboxMessage::TYPE_VIDEO_IDENTIFICATION);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionVoteUsers($id, $vote) {
        $searchModel = new \app\models\TrollboxMessageVoteSearch();
        $dataProvider = $searchModel->search($id, Yii::$app->request->queryParams, $vote);

        return $this->render('vote-users', [
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
            'vote'=>$vote,
            'trollboxMessage'=>$this->findModel($id)
        ]);

    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if (Yii::$app->request->post()) {
            $video_identification_status = Yii::$app->request->post()['video_identification_status'];
            $model->setVideoIdentStatus($video_identification_status);
            //$model->user->video_identification_status = $video_identification_status;
            //$model->user->save();
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    public function actionVote($id,$vote) {
        $model = $this->findModel($id);
        $model->setVideoIdentStatus($vote==1 ? \app\models\User::VIDEO_IDENTIFICATION_STATUS_ACCEPTED_MANUAL:\app\models\User::VIDEO_IDENTIFICATION_STATUS_REJECTED);

        return $this->redirect(['index']);
    }


    protected function findModel($id) {
        if (($model = TrollboxMessage::findOne(['id'=>$id, 'type'=>TrollboxMessage::TYPE_VIDEO_IDENTIFICATION])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}