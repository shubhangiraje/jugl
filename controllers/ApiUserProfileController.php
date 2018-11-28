<?php

namespace app\controllers;

use app\components\EDateTime;
//use app\models\base\OfferRequest;
use app\models\Offer;
use app\models\OfferRequest;
use app\models\SearchRequest;
use app\models\SearchRequestOffer;
use app\models\TrollboxMessage;
use app\models\UserActivityLog;
use app\models\UserBecomeMemberInvitation;
use app\models\UserFollower;
use app\models\UserFriend;
use app\models\UserFriendRequest;
use app\models\UserFeedback;
use app\models\UserPhoto;
use app\models\UserTeamFeedback;
use app\models\Country;
use Yii;
use app\models\User;
use yii\db\Query;
use yii\web\ForbiddenHttpException;
use app\models\ChatUserIgnore;


class ApiUserProfileController extends \app\components\ApiController {

    private function _getFriends($userId,$pageNum=1,$pageCount=1) {
        $perPage=16;

        $user=User::findOne($userId);
        if (!$user) {
            throw new ForbiddenHttpException();
        }

        $friendsQuery=$user->hasMany('\app\models\UserFriend', ['user_id' => 'id'])
            ->select(['user_friend.*',"TRIM(CONCAT_WS(' ',nick_name,first_name,last_name)) as name","IF(user.status='DELETED',1,0) as is_deleted"])
            ->innerJoin('user','user_friend.friend_user_id=user.id')
            ->leftJoin('chat_user','user_friend.friend_user_id=chat_user.user_id')
            ->orderBy("is_deleted asc,chat_user.online desc,chat_user.online_mobile desc, name asc")
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage*$pageCount+1);

        $friends=$friendsQuery->with('friendUser','friendUser.chatUser','friendUser.avatarFile')->all();
        $data=['hasMore'=>count($friends)>$perPage*$pageCount,'users'=>[]];
		
		$flagAry = Country::getListShort();
		
        foreach(array_slice($friends,0,$perPage*$pageCount) as $friendkey=>$friend) {
            $data['users'][$friendkey]=$friend->friendUser->getShortData(['online','country_id']);
			$data['users'][$friendkey]['flag']=$flagAry[$data['users'][$friendkey]['country_id']];
        }

        return ['friends'=>$data];
    }

    private function getFriends($userId,$pageNum=1,$pageCount=1) {

        $perPage=12;

        $user=User::findOne($userId);
        if (!$user) {
            throw new ForbiddenHttpException();
        }

        $squery =$user->hasMany('\app\models\UserFriend', ['user_id' => 'id'])
            ->select([
                'user_friend.friend_user_id as user_id',
                "TRIM(CONCAT_WS(' ',nick_name,first_name,last_name)) as name",
                "IF(user.status='DELETED',1,0) as is_deleted",
                'chat_user.online as online',
                'chat_user.online_mobile as online_mobile'
            ])
            ->innerJoin('user','user_friend.friend_user_id=user.id')
            ->leftJoin('chat_user','user_friend.friend_user_id=chat_user.user_id')
            ->union(
                $user->hasMany('\app\models\UserFollower', ['follower_user_id' => 'id'])
                    ->select([
                        'user_follower.user_id as user_id',
                        "TRIM(CONCAT_WS(' ',nick_name,first_name,last_name)) as name",
                        "IF(user.status='DELETED',1,0) as is_deleted",
                        'chat_user.online as online',
                        'chat_user.online_mobile as online_mobile'
                    ])
                    ->innerJoin('user','user_follower.user_id=user.id')
                    ->leftJoin('chat_user','user_follower.user_id=chat_user.user_id')
            );

        $query2 = clone $squery;

        $query=(new Query())
            ->from(['items'=>$squery])
            ->orderBy("is_deleted asc, online desc, online_mobile desc, name asc")
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $friends=$query->all();
        $hasMore=count($friends)>$perPage;
        $friends=array_slice($friends,0,$perPage);

        $idsUsers=[];
        foreach($friends as $friend) {
            $idsUsers[]=$friend['user_id'];
        }

        $usersInfo = $this->getUsersData($idsUsers);

        $data=[];
        foreach(array_slice($friends,0,$perPage*$pageCount) as $friend) {
            $data[]=$usersInfo[$friend['user_id']];
        }

        return [
            'friends'=>[
                'users'=>$data,
                'hasMore'=>$hasMore,
                'count'=>$query2->count()
            ]
        ];

    }

    private function getUsersData($ids) {
        if (!is_array($ids) || empty($ids)) return;
        $users=User::find()->andWhere(['id'=>$ids])->with(['avatarFile'])->all();
        $data=[];
        foreach($users as $user) {
            $data[$user->id]=$user->getShortData(['rating','online','feedback_count','packet','country_id']);
        }
        return $data;
    }

    public function getUserInfo($id) {
        $user=User::findOne($id);
        if (!$user) {
            throw new ForbiddenHttpException();
        }

        $data=$user->toArray(['id','first_name','last_name','nick_name','is_company_name','company_name','network_levels', 'validation_status', 'validation_phone_status', 'country_id', 'registered_by_become_member','video_identification_status']);

        $data['avatar']=[
            'image'=>$user->avatarFile ? $user->avatarFile->getThumbUrl('avatarBig'):$user->getAvatarUrl(),
            'fancybox'=>$user->avatarFile ? $user->avatarFile->getThumbUrl('fancybox'):$user->getAvatarUrl(),
        ];
		
		/* NVII-MEDIA - Output Flag */
		$flagAry = Country::getListShort();
		$data['flag'] = $flagAry[$user->country_id];
		/* NVII-MEDIA - Output Flag */
		
        $data['isOnline']=$user->chatUser->online ? 2:($user->chatUser->online_mobile ? 1:0);

        $isMyFriend=UserFriend::find()->andWhere(['user_id'=>Yii::$app->user->id,'friend_user_id'=>$user->id])->exists();
        $data['isMyFriend']=$isMyFriend;

        $isMyFriendBlocked=ChatUserIgnore::find()->andWhere(['ignore_user_id'=>Yii::$app->user->id, 'user_id'=>$user->id])->exists();
        $data['isMyFriendBlocked']=$isMyFriendBlocked;

        $isMyFollow=UserFollower::find()->andWhere(['user_id'=>$user->id,'follower_user_id'=>Yii::$app->user->id])->exists();
        $data['isMyFollow']=$isMyFollow;

        $userActivityLog=UserActivityLog::find()->andWhere(['user_id'=>$user->id])->orderBy('dt desc')->one();
        $lastTimeWasOnline=$userActivityLog ? $userActivityLog->dt_full:$user->registration_dt;
        $data['lastTimeWasOnline']=(new EDateTime($lastTimeWasOnline))->js();

        $data['sex']=$user->sexLabel;
        $data['block_parent_team_requests']=$user->block_parent_team_requests;

        $data['teamChangeFinishTime']=$user->getTeamChangeFinishTime(true);
        $data['request_sent2']=boolval(\app\models\UserTeamRequest::findOne(['user_id'=>Yii::$app->user->id,'second_user_id'=>$user->id,'type'=>\app\models\UserTeamRequest::TYPE_PARENT_TO_REFERRAL]));
        $data['request_sent']=boolval(\app\models\UserTeamRequest::findOne(['user_id'=>Yii::$app->user->id,'second_user_id'=>$user->id,'type'=>\app\models\UserTeamRequest::TYPE_REFERRAL_TO_PARENT]));

        $data['canCreateStickRequest']=Yii::$app->user->identity->canCreateStickRequest($user);

        $isMe=Yii::$app->user->id==$user->id;

        if ($user->visibility_marital_status==User::VISIBILITY_ALL ||
            $user->visibility_marital_status==User::VISIBILITY_FRIENDS && $isMyFriend ||
            $user->visibility_marital_status==User::VISIBILITY_NONE && $isMe) {
            $data['marital_status']=$user->maritalStatusLabel;
        }

        if ($user->visibility_address1==User::VISIBILITY_ALL ||
            $user->visibility_address1==User::VISIBILITY_FRIENDS && $isMyFriend ||
            $user->visibility_address1==User::VISIBILITY_NONE && $isMe) {
            $data['street_house_number']=trim($user->street.' '.$user->house_number);
        }

        if ($user->visibility_address2==User::VISIBILITY_ALL ||
            $user->visibility_address2==User::VISIBILITY_FRIENDS && $isMyFriend ||
            $user->visibility_address2==User::VISIBILITY_NONE && $isMe) {
            $data['zip_city']=trim($user->zip.' / '.$user->city);
            if ($data['zip_city']=='/') $data['zip_city']='';
        }

        if ($user->visibility_profession==User::VISIBILITY_ALL ||
            $user->visibility_profession==User::VISIBILITY_FRIENDS && $isMyFriend ||
            $user->visibility_profession==User::VISIBILITY_NONE && $isMe) {
            $data['profession']=$user->profession;
        }

        if ($user->visibility_about==User::VISIBILITY_ALL ||
            $user->visibility_about==User::VISIBILITY_FRIENDS && $isMyFriend ||
            $user->visibility_about==User::VISIBILITY_NONE && $isMe) {
            $data['about']=$user->about;
        }


        if($user->birthday!=NULL) {

            $birthday=new EDateTime($user->birthday);
            $now=new EDateTime();

            if ($user->visibility_birthday==User::VISIBILITY_ALL ||
                $user->visibility_birthday==User::VISIBILITY_FRIENDS && $isMyFriend ||
                $user->visibility_birthday==User::VISIBILITY_NONE && $isMe) {
                $data['birthday']=(new EDateTime($user->birthday))->format('d.m.Y');
                $data['age']=$now->diff($birthday)->format('%Y');
            } else {
                $data['birthday']=(new EDateTime($user->birthday))->format('d.m');
            }


        }

        $friendRequest=UserFriendRequest::findOne(['user_id'=>Yii::$app->user->id,'friend_user_id'=>$user->id]);
        if ($friendRequest->status == 'AWAITING')
            $friendRequestSend = $friendRequest->status;
        else
            $friendRequestSend = false;

        $data['friendRequestSend'] = $friendRequestSend;
        $data['ignored']=boolval(ChatUserIgnore::find()->andWhere(['user_id'=>Yii::$app->user->id,'ignore_user_id'=>$id])->one());

        $data['rating']=$user->rating;
        $data['feedback_count'] = $user->feedback_count;
        $data['team_feedback_count'] = $user->team_feedback_count;
        $data['network_size'] = $user->network_size;
        $data['invitations'] = $user->invitations;
        $data['packet'] = $user->packet;
        $data['team_rating'] = $user->team_rating;
        $data['registration_dt']=(new EDateTime($user->registration_dt))->js();

        $dealsOfferCompleted = Offer::find()
            ->where(['user_id'=>$id, 'status'=>Offer::STATUS_CLOSED])
            ->count();

        $dealsOfferRequestCompleted = OfferRequest::find()
            ->where(['user_id'=>$id, 'status'=>OfferRequest::STATUS_ACCEPTED])
            ->count();

        $dealsSearchRequestCompleted = SearchRequest::find()
            ->where(['user_id'=>$id, 'status'=>SearchRequest::STATUS_CLOSED])
            ->count();

        $dealsSearchRequestOfferCompleted = SearchRequestOffer::find()
            ->where(['user_id'=>$id, 'status'=>SearchRequestOffer::STATUS_ACCEPTED])
            ->count();

        $data['dealsCompleted'] = $dealsOfferCompleted + $dealsOfferRequestCompleted + $dealsSearchRequestCompleted + $dealsSearchRequestOfferCompleted;

        if ($user->parent) {
            $data['parent']=$user->parent->getShortData(['rating','feedback_count', 'packet', 'country_id']);
			$data['parent']['flag'] = $flagAry[$user->parent->country_id];
            $data['parent']['avatarUrl']=$user->parent->avatarFile ? $user->parent->avatarFile->getThumbUrl('avatarBig'):$user->parent->getAvatarUrl();
        }

        $photos = UserPhoto::find()
            ->where(['user_id'=>$user->id])
            ->with('file')
            ->orderBy(['sort_order'=>SORT_ASC])
            ->all();

        foreach ($photos as $itemPhoto) {
            $data['photos'][] = $itemPhoto->file->getThumbUrl('fancybox');
        }

        if ($user->video_identification_status == User::VIDEO_IDENTIFICATION_STATUS_AWAITING) {
            $trollboxMesage = TrollboxMessage::find()
                ->where([
                    'type'=>TrollboxMessage::TYPE_VIDEO_IDENTIFICATION,
                    'status'=>TrollboxMessage::STATUS_ACTIVE,
                    'user_id'=>$user->id
                ])->one();

            if ($trollboxMesage) {
                $data['avatar']['image']=$trollboxMesage->file->getThumbUrl('avatarBig');
            }
        }

        return [
            'userInfo'=>$data
        ];
    }



    public function getFeedback($userId, $pageNumFeedback=1,$pageCount=1) {
        $perPage = 16;
		/* NVII-MEDIA - Output Flag */
		$flagAry = Country::getListShort();
		/* NVII-MEDIA - Output Flag */
		
        $feedback = UserFeedback::find()
            ->where(['user_id' => $userId])
            ->with(['secondUser'])
            ->offset(($pageNumFeedback-1)*$perPage)
            ->orderBy('id desc')
            ->limit($perPage*$pageCount+1)
            ->all();

        $data=['hasMore'=>count($feedback)>$perPage*$pageCount,'items'=>[]];
        foreach (array_slice($feedback,0,$perPage*$pageCount) as $itemKey => $itemVal) {
            $data['items'][] = [
                'id' => $itemVal->id,
                'user'=> $itemVal->secondUser->getShortData(['rating','feedback_count', 'packet', 'country_id']),
                'feedback' => $itemVal->feedback,
                'rating' => $itemVal->rating,
                'response' => $itemVal->response,
                'response_dt' =>(new EDateTime($itemVal->response_dt))->js(),
                'create_dt' => (new EDateTime($itemVal->create_dt))->js()
            ];
			$data['items'][$itemKey]['user']['flag'] = $flagAry[$data['items'][$itemKey]['user']['country_id']];
        }

        return [
            'feedback' => $data
        ];
    }

    public function getTeamFeedback($userId, $pageNumTeamFeedback=1,$pageCount=1) {
        $perPage = 16;

        $feedback = UserTeamFeedback::find()
            ->where(['user_id' => $userId])
            ->with(['secondUser'])
            ->offset(($pageNumTeamFeedback-1)*$perPage)
            ->limit($perPage*$pageCount+1)
            ->orderBy('id desc')
            ->all();

        $data=['hasMore'=>count($feedback)>$perPage*$pageCount,'items'=>[]];
        foreach (array_slice($feedback,0,$perPage*$pageCount) as $item) {
            $data['items'][] = [
                'id' => $item->id,
                'user'=> $item->secondUser->getShortData(['rating','feedback_count', 'packet']),
                'feedback' => $item->feedback,
                'rating' => $item->rating,
                'response' => $item->response,
                'response_dt' =>(new EDateTime($item->response_dt))->js(),
                'create_dt' => (new EDateTime($item->create_dt))->js()
            ];
        }

        return [
            'teamFeedback' => $data
        ];
    }



    public function actionAddToIgnoreList() {
        $friendId=Yii::$app->request->getBodyParams()['friendId'];

        $ignored=ChatUserIgnore::find()->andWhere(['user_id'=>Yii::$app->user->id,'ignore_user_id'=>$friendId])->one();
        if (!$ignored) {
            $cui=new ChatUserIgnore();
            $cui->user_id=Yii::$app->user->id;
            $cui->ignore_user_id=$friendId;
            $cui->save();
        }
        \app\components\ChatServer::updateInitInfo([Yii::$app->user->id,$friendId]);

        return $this->getUserInfo($friendId);
    }

    public function actionDelFromIgnoreList() {
        $friendId=Yii::$app->request->getBodyParams()['friendId'];

        ChatUserIgnore::deleteAll(['user_id'=>Yii::$app->user->id,'ignore_user_id'=>$friendId]);
        \app\components\ChatServer::updateInitInfo([Yii::$app->user->id,$friendId]);

        return $this->getUserInfo($friendId);
    }

    public function actionOpenConversation($userId) {
        return \app\components\ChatServer::openConversation($userId);
    }

    public function actionDeleteFriend() {
        $params=Yii::$app->request->getBodyParams();
        Yii::$app->user->identity->deleteFriend($params['friendId']);

        return $this->actionIndex($params['friendId']);
    }

    public function actionAddFriend() {
        $friendId=Yii::$app->request->getBodyParams()['friendId'];
        $res=UserFriendRequest::add(Yii::$app->user->identity,$friendId);
        return ['result'=>$res];
    }

    public function actionChangeFriend() {
        $friendUserId=Yii::$app->request->getBodyParams()['friendUserId'];
        $res=UserFriend::changeFriend(Yii::$app->user->identity, $friendUserId);
        return $res;
    }

    public function actionChangeSubscribe() {
        $subscribeUserId=Yii::$app->request->getBodyParams()['subscribeUserId'];
        $res=UserFollower::changeSubscribe(Yii::$app->user->identity, $subscribeUserId);
        return $res;
    }

    public function actionFeedback($userId,$pageNumFeedback) {
        return $this->getFeedback($userId,$pageNumFeedback);
    }

    public function actionTeamFeedback($userId,$pageNumTeamFeedback) {
        return $this->getTeamFeedback($userId,$pageNumTeamFeedback);
    }

    public function actionFriends($userId,$pageNum) {
        return $this->getFriends($userId,$pageNum);
    }

    public function actionIndex($id) {
        return array_merge(
            $this->getUserInfo($id),
            $this->getFriends($id,1),
            $this->getFeedback($id,1),
            $this->getTeamFeedback($id,1),
            $this->getFollowers($id,1),
            $this->getTrollboxMessages($id,1)
        );
    }

    public function actionSettings() {
        $user=Yii::$app->user->identity;
        $userInfo=$user->toArray([
            'setting_off_send_email',
            'setting_notification_likes',
            'setting_notification_comments'
        ]);
        return [
            'settings'=>$userInfo,
        ];
    }

    public function actionUpdateSettings() {
        $data = Yii::$app->request->getBodyParams()['data'];
        if (!is_array($data)) {
            return [];
        }

        if (isset($data['setting_off_send_email'])) {
            Yii::$app->db->createCommand("
              UPDATE user 
              SET setting_off_send_email=:setting_off_send_email, 
                  setting_notification_likes=:setting_notification_likes,
                  setting_notification_comments=:setting_notification_comments
              WHERE id=:id", [
                ':id'=>Yii::$app->user->identity->id,
                ':setting_off_send_email'=>intval($data['setting_off_send_email']),
                ':setting_notification_likes'=>intval($data['setting_notification_likes']),
                ':setting_notification_comments'=>intval($data['setting_notification_comments']),
            ])->execute();
        }

        return [];
    }

    private function getChatUserContact($userId) {
        return \app\models\ChatUserContact::findOne([
            'user_id'=>Yii::$app->user->id,
            'second_user_id'=>$userId,
            'decision_needed'=>1
        ]);
    }

    private function decision($type) {
        $chatUserContact=$this->getChatUserContact(Yii::$app->request->getBodyParams()['userId']);

        if ($chatUserContact) {
            return $chatUserContact->$type();
        } else {
            return false;
        }
    }

    public function actionDeleteContactHistory() {
        Yii::$app->user->identity->deleteContactChatHistory(Yii::$app->request->getBodyParams()['userId']);
        return [];
    }

    public function actionDecisionSkip() {
        return $this->decision('decisionSkip');
    }

    public function actionDecisionAddToFriends() {
        return $this->decision('decisionAddToFriends');
    }

    public function actionDecisionSpam() {
        return $this->decision('decisionSpam');
    }

    private function getFollowers($userId,$pageNum=1,$pageCount=1) {
        $perPage = 12;

        $myFollowers = UserFollower::find()
            ->where(['user_id' => $userId])
            ->joinWith(['followerUser'])
            ->with(['followerUser','followerUser.avatarFile'])
            ->offset(($pageNum-1)*$perPage)
            ->orderBy(['user.last_name'=>SORT_ASC, 'user.first_name'=>SORT_ASC])
            ->limit($perPage*$pageCount+1)
            ->all();

        $count = UserFollower::find()->where(['user_id' => $userId])->count();
        $data=['hasMore'=>count($myFollowers)>$perPage*$pageCount,'users'=>[], 'count'=>$count];
        foreach (array_slice($myFollowers,0,$perPage*$pageCount) as $item) {
            $data['users'][] = $item->followerUser->getShortData(['rating','online','feedback_count', 'packet']);
        }

        return [
            'followers' => $data
        ];
    }


    public function actionFollowers($userId, $pageNum) {
        return $this->getFollowers($userId, $pageNum);
    }

    public function actionSaveStickRequest() {
        $data=Yii::$app->request->getBodyParams()['stickUserRequest'];

        $model=new \app\models\StickUserRequestForm;

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $model->load($data,'');
        if ($model->validate()) {
            $model->save();
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['stickUserRequest'=>$data];
        }

        $trx->commit();

        return ['result'=>Yii::t('app','Du hast eine Anfrage an diesen Nutzer versendet. Bitte warte bis er/sie antwortet')];

    }

    public function actionStickParentAccept() {
        $trx=Yii::$app->db->beginTransaction();

        Yii::$app->user->identity->is_stick_to_parent=1;
        Yii::$app->user->identity->save();

        $model=\app\models\UserStickToParentRequest::findOne([
            'user_id'=>Yii::$app->user->identity->parent_id,
            'referral_user_id'=>Yii::$app->user->id
        ]);

        if ($model) {
            $model->completed=1;
            $model->save();
        }

        $data['result']=true;

        $eventModels=\app\models\UserEvent::addStickParentAccept();
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

    public function actionStickParentReject() {
        $trx=Yii::$app->db->beginTransaction();

        $model=\app\models\UserStickToParentRequest::findOne([
            'user_id'=>Yii::$app->user->identity->parent_id,
            'referral_user_id'=>Yii::$app->user->id
        ]);

        if ($model) {
            $model->completed=1;
            $model->save();
        }

        $data['result']=true;

        $eventModels=\app\models\UserEvent::addStickParentReject();
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

    private function getTrollboxMessages($userId,$pageNum=1,$pageCount=1) {
        $perPage = 12;

        $query = TrollboxMessage::find()
            ->where(['user_id' => $userId, 'type'=>TrollboxMessage::TYPE_FORUM])
            ->with(['user','user.avatarFile', 'trollboxCategory']);

        if (!Yii::$app->user->identity->is_moderator && Yii::$app->user->id!=$userId) {
            $query->andWhere(['status'=>TrollboxMessage::STATUS_ACTIVE]);
        }

        $query2 = clone $query;

        $trollboxMessages = $query->offset(($pageNum-1)*$perPage)
            ->orderBy(['is_sticky'=>SORT_DESC,'dt'=>SORT_DESC])
            ->limit($perPage*$pageCount+1)
            ->all();


        $count = $query2->count();
        $data=['hasMore'=>count($trollboxMessages)>$perPage*$pageCount,'items'=>[], 'count'=>$count];
        foreach (array_slice($trollboxMessages,0,$perPage*$pageCount) as $item) {
            $data['items'][] = $item->getFrontInfo();
        }

        return [
            'trollboxMessages' => $data
        ];
    }

    public function actionTrollboxMessages($userId, $pageNum) {
        return $this->getTrollboxMessages($userId, $pageNum);
    }

}
