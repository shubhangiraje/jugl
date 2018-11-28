<?php



namespace app\controllers;

use app\components\EDateTime;
use app\models\UserEvent;
use Yii;
use app\models\User;
use app\models\UserSearch;
use app\components\AdminController;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\BalanceTokenLog;
/**
 * AdminUserController implements the CRUD actions for User model.
 */

class AdminUserController extends AdminController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete-user' => ['post'],
                    'block-user' => ['post'],
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
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDeleted()
    {
        $searchModel = new \app\models\UserDeletedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('deleted', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUndeleteUser($id)
    {
        $this->findModel($id)->undelete();
        return $this->pjaxRefresh();
    }

    public function actionDeleteUser($id)
    {
        $this->findModel($id)->delete();
        \app\components\ChatServer::statusUpdate([$id]);
        return $this->pjaxRefresh();
    }

    public function actionUnblockUser($id)
    {
        $this->findModel($id)->unblock();
        return $this->pjaxRefresh();
    }

    public function actionBlockUser($id)
    {
        $this->findModel($id)->block();
        \app\components\ChatServer::statusUpdate([$id]);
        return $this->pjaxRefresh();
    }

    public function actionModalBlockUser($id) {
        $this->findModel($id)->block();
        \app\components\ChatServer::statusUpdate([$id]);
        return true;
    }


    public function actionActivateSpamReport($id) {
        $model=\app\models\UserSpamReport::findOne($id);
        $model->is_active=1;
        $model->save();

        return $this->pjaxRefresh();
    }

    public function actionDeactivateSpamReport($id) {
        $model=\app\models\UserSpamReport::findOne($id);
        $model->is_active=0;
        $model->save();

        return $this->pjaxRefresh();
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $model = $this->findModel($id);
        $model->setScenario('update');
        $comment_data=BalanceTokenLog::commentData();
        //$typelist=BalanceTokenLog::getTypeList();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                // save bank data
                $bankDataIdx=0;
                if (is_array(Yii::$app->request->post()['UserBankData'])) {
                    foreach (Yii::$app->request->post()['UserBankData'] as $bankData) {
                        if (trim(implode('', array_values($bankData))) == '') continue;

                        $bankDataModel = count($model->userBankDatas) <= $bankDataIdx ? (new \app\models\UserBankData) : $model->userBankDatas[$bankDataIdx];
                        $bankDataModel->sort_order = $bankDataIdx;
                        $bankDataModel->user_id = $model->id;
                        $bankDataModel->load($bankData, '');
                        $bankDataModel->save();
                        $bankDataIdx++;
                    }
                }

                for(;$bankDataIdx<count($model->userBankDatas);$bankDataIdx++) {
                    $model->userBankDatas[$bankDataIdx]->delete();
                }

                if($model->oldAttributes['validation_status']!=$model->validation_status) {

                    $now = new EDateTime();

                    if ($model->validation_status==User::VALIDATION_STATUS_SUCCESS) {
                        $model->dt_status_change = $now->sqlDateTime();
                        Yii::$app->mailer->sendEmail($model,'validation-success',['model'=>$model]);
                        \app\models\UserEvent::addDocumentValidationSuccess($model);
                        $model->addRegistrationBonusToParent();
                    } else {
                        $model->dt_status_change = null;
                    }

                    if ($model->validation_status==User::VALIDATION_STATUS_FAILURE) {
                        Yii::$app->mailer->sendEmail($model,'validation-failure',['model'=>$model]);
                    }

                    $validation_failure_reason = '';
                    if($model->validation_status==User::VALIDATION_STATUS_FAILURE && $model->validation_failure_reason) {
                        $validation_failure_reason = ': '.$model->validation_failure_reason;
                    }

                    $model->validation_changelog .= "\n".'['.$now->format('d.m.Y, H:i:s').']'.' '.Yii::$app->admin->identity->name.', '.$model->getValidationStatusLabel().$validation_failure_reason;

                }

                if($model->oldAttributes['is_moderator']!=$model->is_moderator) {
                    if($model->is_moderator) {
                        \app\models\UserEvent::addBroadcastMessage([$model->id], Yii::t('app','Du bist jetzt Moderator für die Gruppenchats.'));
                        Yii::$app->mailer->sendEmail($model, 'default-text', [
                            'subject'=>Yii::t('app','Du bist jetzt Moderator für die Gruppenchats.'),
                            'text'=>Yii::t('app', 'Du bist jetzt Moderator für die Gruppenchats.')
                        ]);
                    } else {
                        \app\models\UserEvent::addBroadcastMessage([$model->id], Yii::t('app','Du bist jetzt kein Moderator mehr für die Gruppenchats.'));
                        Yii::$app->mailer->sendEmail($model, 'default-text', [
                            'subject'=>Yii::t('app','Du bist jetzt kein Moderator mehr für die Gruppenchats.'),
                            'text'=>Yii::t('app','Du bist jetzt kein Moderator mehr für die Gruppenchats.')
                        ]);
                    }
                }

                $model->save();

                return $this->redirect(['index']);
            }
        }

        $userBalanceModProvider = new \yii\data\ActiveDataProvider([
            'query' =>
                \app\models\BalanceLogMod::find()
                    ->where(['user_id'=>$model->id])
                    ->joinWith(['balanceLog','admin'],true)
                    ->orderBy('balance_log.dt desc'),
            'sort' => [
                'attributes' => ['balance_log.dt'],
                'defaultOrder'=>['balance_log.dt'=>SORT_DESC]
            ]
        ]);

        $userBalanceTokenModProvider = new \yii\data\ActiveDataProvider([
            'query' =>
                \app\models\BalanceTokenLogMod::find()
                    ->where(['user_id'=>$model->id])
                    ->joinWith(['balanceTokenLog','admin'],true)
                    ->orderBy('balance_token_log.dt desc'),
            'sort' => [
                'attributes' => ['balance_token_log.dt'],
                'defaultOrder'=>['balance_token_log.dt'=>SORT_DESC]
            ]
        ]);

        $spamReportsProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\UserSpamReport::find()->with(['user'])->where(['second_user_id'=>$model->id]),
            'sort' => [
                'attributes' => ['dt'],
                'defaultOrder'=>['dt'=>SORT_DESC]
            ]
        ]);


        $searchModelBalanceLog = new \app\models\BalanceLogSearch();
        $balanceLogProvider = $searchModelBalanceLog->search($model, Yii::$app->request->queryParams);
        $balanceLogProvider->setTotalCount(100000);

        $searchModelBalanceTokenLog = new \app\models\BalanceTokenLogSearch();
        $balanceTokenLogProvider = $searchModelBalanceTokenLog->search($model, Yii::$app->request->queryParams);
        //$balanceTokenLogProvider->setTotalCount(100000);

        $offersProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\Offer::find()->with(['offerInterests.level1Interest'])->where(['user_id'=>$model->id,'created_by_admin'=>1]),
            'sort' => [
                'attributes' => ['id'],
                'defaultOrder'=>['id'=>SORT_DESC]
            ]
        ]);

        $searchModelUserModifyLog = new \app\models\UserModifyLogSearch();
        $userModifyLogProvider = $searchModelUserModifyLog->search($model, Yii::$app->request->queryParams);


        $userFeedbackSearchModel = new \app\models\UserFeedbackSearch();
        $userFeedbackDataProvider = $userFeedbackSearchModel->search($id, Yii::$app->request->queryParams);

        $userTeamFeedbackSearchModel = new \app\models\UserTeamFeedbackSearch();
        $userTeamFeedbackDataProvider = $userTeamFeedbackSearchModel->search($id, Yii::$app->request->queryParams);

		$userMemberInvitedTodayModel = new \app\models\UserBecomeMemberInvitation();
		$model->get_user_invited_today = $userMemberInvitedTodayModel->getUserInvitedToday($id);


        $devicesProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\UserDevice::find()->with(['user'])->where(['user_id'=>$model->id]),
            'sort' => [
                'defaultOrder'=>['id'=>SORT_DESC]
            ]
        ]);

		return $this->render('update', [
            'model' => $model,
            'modelAddUserBalance' => new \app\models\AddUserBalanceForm(),
            'modelAddUserTokenBalance' => new \app\models\AddUserTokenBalanceForm(),
            'userBalanceModProvider' => $userBalanceModProvider,
            'userBalanceTokenModProvider' => $userBalanceTokenModProvider,
            'spamReportsProvider' => $spamReportsProvider,
            'balanceLogProvider' => $balanceLogProvider,
            'balanceTokenLogProvider' => $balanceTokenLogProvider,
            'searchModelBalanceLog' => $searchModelBalanceLog,
            'searchModelBalanceTokenLog' => $searchModelBalanceTokenLog,
            'offersProvider'=>$offersProvider,
            'userModifyLogProvider' => $userModifyLogProvider,
            'userFeedbackDataProvider'=>$userFeedbackDataProvider,
            'userTeamFeedbackDataProvider'=>$userTeamFeedbackDataProvider,
            'commentdata'=>$comment_data,
          //  'type'=>$typelist
        ]);
    }

    public function actionAddUserBalance($id) {
        $model=new \app\models\AddUserBalanceForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {

                $trx=Yii::$app->db->beginTransaction();

                $user=$this->findModel($id);

                UserEvent::addChangeBalanceAdministration(Yii::$app->admin->identity, $user, $model->sum);

                $comment = Yii::t('app','Überweisung durch den Administrator {admin}',['admin'=>Yii::$app->admin->identity->first_name.' '.Yii::$app->admin->identity->last_name]);
                if (!$model->distribute) {
                    $balanceLog=$user->addBalanceLogItem($model->sum>=0 ? \app\models\BalanceLog::TYPE_IN:\app\models\BalanceLog::TYPE_OUT,$model->sum,$user,$comment);
                } else {
                    $balanceLog=$user->distributeReferralPayment(
                        $model->sum,
                        $user,
                        \app\models\BalanceLog::TYPE_IN_REG_REF,
                        \app\models\BalanceLog::TYPE_IN_REG_REF_REF,
                        \app\models\BalanceLog::TYPE_IN_REG_REF_REF,
                        $model->comments, false
                    );

                }

                $balanceLogMod=new \app\models\BalanceLogMod();
                $balanceLogMod->admin_id=Yii::$app->admin->id;
                $balanceLogMod->balance_log_id=$balanceLog->id;
                $balanceLogMod->comments=$model->comments;
                $balanceLogMod->save();

                $trx->commit();


                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'modelAddUserBalance' => $model,
            'model'=>$this->findModel($id)
        ]);

    }

    public function actionAddUserTokenBalance($id) {
        $model=new \app\models\AddUserTokenBalanceForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {

                $trx=Yii::$app->db->beginTransaction();

                $user=$this->findModel($id);

                UserEvent::addChangeBalanceTokenAdministration(Yii::$app->admin->identity, $user, $model->sum);

                $comment = Yii::t('app','Überweisung durch den Administrator {admin}',['admin'=>Yii::$app->admin->identity->first_name.' '.Yii::$app->admin->identity->last_name]);
                if (!$model->distribute) {
                    $balanceLog=$user->addBalanceTokenLogItem($model->sum>=0 ? \app\models\BalanceTokenLog::TYPE_IN:\app\models\BalanceTokenLog::TYPE_OUT,$model->sum,$user,$comment);
                } else {
                    $balanceLog=$user->distributeTokenReferralPayment(
                        $model->sum,
                        $user,
                        \app\models\BalanceLog::TYPE_IN_REG_REF,
                        \app\models\BalanceLog::TYPE_IN_REG_REF_REF,
                        \app\models\BalanceLog::TYPE_IN_REG_REF_REF,
                        $model->comments, false
                    );

                }

                $balanceLogMod=new \app\models\BalanceTokenLogMod();
                $balanceLogMod->admin_id=Yii::$app->admin->id;
                $balanceLogMod->balance_token_log_id=$balanceLog->id;
                $balanceLogMod->comments=$model->comments;
                $balanceLogMod->save();

                $this->addAdminActionLogComment(Yii::t('app',"Token Konto aufladung: {sum} Tokens für nutzer {email}. Kommentar:\n{comment}",[
                    'sum'=>$model->sum,
                    'email'=>$user->email,
                    'comment'=>$model->comments]));
                $trx->commit();


                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'modelAddUserTokenBalance' => $model,
            'model'=>$this->findModel($id)
        ]);

    }

    public function actionUpdatePhoto($id) {
        $model = $this->findModel($id);

        $model->setScenario('photos');

        if ($_REQUEST['UserPhoto']) {
            $userPhotos=[];
            foreach($_REQUEST['UserPhoto'] as $k=>$file) {
                $file_id=$file['file_id'];
                if ($file_id) {
                    $userPhoto=new \app\models\UserPhoto();
                    $userPhoto->sort_order=$k;
                    $userPhoto->file_id=$file_id;
                    $userPhotos[]=$userPhoto;
                }
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()) {
                $trx=Yii::$app->db->beginTransaction();
                $model->save();

                \app\models\UserPhoto::deleteAll(['user_id'=>$model->id]);
                foreach($userPhotos as $userPhoto) {
                    $userPhoto->user_id=$model->id;
                    $userPhoto->save();
                }
                $trx->commit();
                return $this->redirect(['update', 'id'=>$model->id]);
            }
        }

        $userPhotos=$model->userPhotos;
        while(count($userPhotos)<30) {
            $userPhotos[]=new \app\models\UserPhoto();
        }

        return $this->render('update-photo', [
            'model'=>$model,
            'userPhotos'=>$userPhotos
        ]);
    }



    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}