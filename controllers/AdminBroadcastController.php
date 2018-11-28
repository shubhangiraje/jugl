<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\components\AdminController;

/**
 * AdminSpammerController implements the CRUD actions for User model.
 */
class AdminBroadcastController extends AdminController
{
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new \app\models\AdminBroadcastMessageForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            foreach(User::find()->where(
			    'status=:STATUS_ACTIVE',[
                ':STATUS_ACTIVE'=>User::STATUS_ACTIVE])->batch(500) as $usersBatch) {

				switch($model->type) {
                    case \app\models\AdminBroadcastMessageForm::TYPE_EVENT:
                        $trx=Yii::$app->db->beginTransaction();
                        $user_ids=[];
                        foreach($usersBatch as $user) {
							
                            $user_ids[]=$user->id;
							
                        }
                        if (!empty($user_ids)) {
                            \app\models\UserEvent::addBroadcastMessage($user_ids,$model->text);
                        }

                        $trx->commit();
                        break;
                    case \app\models\AdminBroadcastMessageForm::TYPE_EMAIL:
                        foreach($usersBatch as $user) {
							if($user->allow_send_message_to_all_users == 0){
								$decline_broadcast_emails_link=\yii\helpers\Url::to(['site/sign-out-mail/','user'=>$user->id,'authkey'=>$user->auth_key], true);
								$model->decline=$decline_broadcast_emails_link;
								Yii::$app->mailer->sendEmail($user,'broadcast',['user'=>$user,'model'=>$model]);
							}
                        }
                        break;
                }
            }

            Yii::$app->session->setFlash('result',Yii::t('app','Message sent'));
           // return $this->redirect(['index']);
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}