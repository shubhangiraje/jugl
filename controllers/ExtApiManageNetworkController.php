<?php

namespace app\controllers;

use Yii;
use app\models\User;

class ExtApiManageNetworkController extends \app\components\ExtApiController {

    private function addTermsConditions($query,$field,$search) {
        $terms=preg_split('/[\s.,]+/',$search,-1,PREG_SPLIT_NO_EMPTY);
        foreach($terms as $term) {
            $query->andFilterWhere(['like', $field, $term]);
        }

        return count($terms);
    }

    private function getUsers($filter = [], $pageNum=1) {
        $perPage=20;

        $query=User::find()->with(['avatarFile','users'])
            ->where('user.id!=:user_id and status=:status_active and parent_id=:user_id',[
                ':user_id'=>Yii::$app->user->id,
                ':status_active'=>User::STATUS_ACTIVE
            ])->leftJoin('chat_user','user.id=chat_user.user_id')
            ->orderBy('chat_user.online desc, chat_user.online_mobile desc, first_name asc, last_name asc');


        $filterName = $filter['name'];
        if(trim($filterName)!='') {
            $this->addTermsConditions($query,"CONCAT_WS(' ',user.nick_name,user.first_name,user.last_name,user.company_name)",$filterName);
        }

        $users=$query->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1)->all();
        $hasMore=count($users)>$perPage;

        $data=[];
        foreach(array_slice($users,0,$perPage) as $user) {
            $data[] = $user->getShortData(['country_id']);
        }

        return [
            'items'=>$data,
            'hasMore'=>$hasMore
        ];
    }

    public function actionList() {
        $filter = Yii::$app->request->getBodyParams()['filter'];
        $pageNum = Yii::$app->request->getBodyParams()['pageNum'];
        return $this->getUsers($filter, $pageNum);
    }

    public function actionMoveDestinationList($move_id, $id=null, $pageNum) {
        if(!$id) {
            $id = Yii::$app->user->id;
        }
        $model = User::findOne($move_id);
        $user = $model->getShortData();
        return [
            'user'=>$user,
            'users'=>$this->getHierarchy($id, $move_id, $pageNum)
        ];
    }

    private function getHierarchy($id, $move_user_id, $pageNum, $filter=[]) {
        $perPage=20;

        $query=User::find()->with(['avatarFile','users'])
            ->leftJoin('chat_user','user.id=chat_user.user_id')
            ->andWhere('user.status!=:user_status1 and user.status!=:user_status2 and user.status!=:user_status3',[
                ':user_status1'=>User::STATUS_REGISTERED,
                ':user_status2'=>User::STATUS_LOGINED,
                ':user_status3'=>User::STATUS_EMAIL_VALIDATION
            ])
            ->orderBy('chat_user.online desc, chat_user.online_mobile desc, user.first_name asc, user.last_name asc');

        $filterName = $filter['name'];

        if(trim($filterName)!='') {
            $query->select(['user.*','ur.level as _ur_level','chat_user.online','chat_user.online_mobile'])->distinct();
            $query->innerJoin('user_referral ur','ur.referral_user_id=user.id and ur.user_id=:logged_user_id',[':logged_user_id'=>Yii::$app->user->id]);
            $query->leftJoin('user_referral ur2','ur2.referral_user_id=user.id and ur2.user_id=:move_user_id',[':move_user_id'=>$move_user_id])->andWhere('ur2.user_id is null');
            $query->andWhere('user.id!=:move_user_id');

            $this->addTermsConditions($query,"CONCAT_WS(' ',user.nick_name,user.first_name,user.last_name,user.company_name)",$filterName);
        } else {
            $query->andWhere('user.parent_id=:user_id and user.id!=:move_user_id',[
                ':user_id'=>$id,
                ':move_user_id'=>$move_user_id
            ]);
        }

        $users=$query->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1)->all();
        $hasMore=count($users)>$perPage;

        $data=[];
        foreach(array_slice($users,0,$perPage) as $user) {
            $idata = $user->getShortData(['country_id']);
            $idata['level']=$user->_ur_level;
            $idata['hasChildren'] = count($user->users)>0;
            $data[]=$idata;
        }

        return [
            'items'=>$data,
            'hasMore'=>$hasMore
        ];
    }

    public function actionHierarchyList() {
        $id = Yii::$app->request->getBodyParams()['id'] ? Yii::$app->request->getBodyParams()['id']:Yii::$app->user->id;
        $move_id = Yii::$app->request->getBodyParams()['move_id'];
        $pageNum = Yii::$app->request->getBodyParams()['pageNum'];
        $filter = Yii::$app->request->getBodyParams()['filter'];
        return $this->getHierarchy($id, $move_id, $pageNum, $filter);
    }

    public function actionSave() {
        $moveUser=\app\models\User::findOne(Yii::$app->request->getBodyParam('moveId'));
        $dstUser=\app\models\User::findOne(Yii::$app->request->getBodyParam('dstId'));

        if (!$moveUser || !$dstUser) {
            throw new \yii\web\ForbiddenHttpException();
        }

        if (!Yii::$app->user->identity->canDoNetworkMove($moveUser,$dstUser)) {
            return [
                'result'=>Yii::t('app','Du hast diesem Nutzer bereits die maximal mögliche Anzahl an User übergeben.')
            ];
        }

        \app\models\UserEvent::addNetworkMoveRequest($moveUser,$dstUser);

        return [
            'result'=>Yii::t('app','Es wurde eine Anfrage bezgl. der Übernahme des zu verschiebenden Users an den Nutzer abgeschickt. Bitte warte bis er die Übernahme bestätigt.')
        ];
    }

    public function actionRejectMoving() {
        $trx=Yii::$app->db->beginTransaction();

        $fromUser=\app\models\User::findOne(Yii::$app->request->getBodyparam('fromId'));
        $toUser=\app\models\User::findOne(Yii::$app->request->getBodyparam('toId'));
        $user=\app\models\User::findOne(Yii::$app->request->getBodyparam('userId'));

        if (!$fromUser || !$toUser || !$user || $toUser->id!=Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException();
        }

        $data['result']=true;


        $eventModels=\app\models\UserEvent::addNetworkMoveReject($fromUser,$toUser,$user);
        foreach($eventModels as $eventModel) {
            $data['events'][]=[
                'id'=>$eventModel->id,
                'type'=>$eventModel->type,
                'text'=>$eventModel->text,
            ];
        }

        $trx->commit();

        return $data;
    }

    public function actionAcceptMoving() {
        $trx=Yii::$app->db->beginTransaction();

        $fromUser=\app\models\User::findOne(Yii::$app->request->getBodyparam('fromId'));
        $toUser=\app\models\User::findOne(Yii::$app->request->getBodyparam('toId'));
        $user=\app\models\User::findOne(Yii::$app->request->getBodyparam('userId'));

        if (!$fromUser || !$toUser || !$user || $toUser->id!=Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException();
        }

        if (!$fromUser->canDoNetworkMove($user,$toUser)) {
            return ['result'=>Yii::t('app','Du hast bereits die maximal mögliche Anzahl an User von deinem Teamleader erhalten.')];
        }

        $data['result']=true;

        $eventModels=\app\models\UserEvent::addNetworkMoveAccept($fromUser,$toUser,$user);
        foreach($eventModels as $eventModel) {
            $data['events'][]=[
                'id'=>$eventModel->id,
                'type'=>$eventModel->type,
                'text'=>$eventModel->text,
            ];
        }

        \app\models\UserModifyLog::saveLogAddReferralToParent($user, $fromUser, $toUser);

        $user->parent_id=$toUser->id;
        $user->save();
        $fromUser->increaseMovedUsersCount($toUser);

        $fromUser->recalcHierarchyNetworkStats();
        $toUser->recalcHierarchyNetworkStats();

        $trx->commit();

        return $data;
    }

}