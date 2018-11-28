<?php

namespace app\controllers;

use Yii;
use app\components\EDateTime;
use app\models\UserTeamFeedback;


class ExtApiUserTeamFeedbackController extends \app\components\ExtApiController {

    public function actionUpdate() {

        $model=UserTeamFeedback::findOne(['user_id'=>Yii::$app->user->identity->parent_id,'second_user_id'=>Yii::$app->user->id]);
        if (!$model) {
            $model=new UserTeamFeedback();
            $model->feedback='';
        }

        $data=$model->toArray(['rating','feedback']);

        return [
            'feedback'=>$data
        ];
    }

    public function actionResponseUpdate() {

        $model=UserTeamFeedback::findOne(['id'=>Yii::$app->request->getQueryParam('id'),'user_id'=>Yii::$app->user->id]);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException();
        }

        $data=$model->toArray(['id','response']);

        return [
            'feedback'=>$data
        ];
    }

    public function actionResponseSave() {
        $data=Yii::$app->request->getBodyParams()['feedback'];

        $model=UserTeamFeedback::findOne(['id'=>$data['id'],'user_id'=>Yii::$app->user->id]);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException();
        }

        $model->response_dt=(new EDateTime())->sqlDateTime();

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $model->setScenario('response-update');
        $model->load($data,'');

        if ($model->validate()) {
            //\app\models\UserEvent::addTeamFeedback($model);
            $model->save();
            /*
            $events=\app\models\UserEvent::find()->where(['user_id'=>Yii::$app->user->identity,'type'=>\app\models\UserEvent::TYPE_TEAM_CHANGE])
                ->andWhere('text like(:text)',[':text'=>'%[teamleaderFeedback%'])->all();
            foreach($events as $event) {
                $event->text=preg_replace('%\[teamleaderFeedback:\d+\]%',Yii::t('app','Sie haben Teamleading bewertet.'),$event->text);
                $event->save();
            }
            */
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['feedback'=>$data];
        }

        $trx->commit();

        $feedbackData=$model->toArray(['response','id']);
        $feedbackData['response_dt']=(new EDateTime())->js();

        return ['result'=>true,'feedback'=>$feedbackData];
    }

    public function actionNotificationRejected() {
        Yii::$app->user->identity->teamleader_feedback_notification_at=null;
        Yii::$app->user->identity->save();
        return ['result'=>true];
    }

    public function actionSave() {
        $data=Yii::$app->request->getBodyParams()['feedback'];

        $model=UserTeamFeedback::findOne(['user_id'=>Yii::$app->user->identity->parent_id,'second_user_id'=>Yii::$app->user->id]);
        if (!$model) {
            $model=new UserTeamFeedback();
            $model->user_id=Yii::$app->user->identity->parent_id;
            $model->second_user_id=Yii::$app->user->id;
        }
        $model->create_dt=(new EDateTime())->sqlDateTime();

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $model->setScenario('update');
        $model->load($data,'');

        if (!$model->user_id) {
            return ['result'=>true];
        }

        if ($model->validate()) {
            \app\models\UserEvent::addTeamFeedback($model);
            $model->save();
            Yii::$app->user->identity->teamleader_feedback_notification_at=null;
            Yii::$app->user->identity->save();
            $events=\app\models\UserEvent::find()->where(['user_id'=>Yii::$app->user->identity,'type'=>\app\models\UserEvent::TYPE_TEAM_CHANGE])
                ->andWhere('text like(:text)',[':text'=>'%[teamleaderFeedback%'])->all();
            foreach($events as $event) {
                $event->text=preg_replace('%\[teamleaderFeedback:\d+\]%',Yii::t('app','Sie haben Teamleading bewertet.'),$event->text);
                $event->save();
            }
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['feedback'=>$data];
        }

        $trx->commit();

        return ['result'=>true];
    }

}