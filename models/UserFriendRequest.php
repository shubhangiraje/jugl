<?php

namespace app\models;

use Yii;
use app\models\UserFriend;



class UserFriendRequest extends \app\models\base\UserFriendRequest
{
    const STATUS_AWAITING='AWAITING';
    const STATUS_ACCEPTED='ACCEPTED';
    const STATUS_DECLINED='DECLINED';

    public static function add($user,$friendId) {
        $friend=User::findOne($friendId);

        if (!$friend) {
            return Yii::t('app','user doesn\'t exists');
        }

        if (UserFriend::find()->where(['user_id'=>$user->id,'friend_user_id'=>$friendId])->exists()) {
            return Yii::t('app','this user is already your friend');
        }

        $request=UserFriendRequest::findOne(['user_id'=>$user->id,'friend_user_id'=>$friendId]);
        if ($request && $request->status!=UserFriendRequest::STATUS_AWAITING) {
            return Yii::t('app', 'friendship request to this user was already sent and user declined it');
        }

        if (!$request) {
            $request = new UserFriendRequest();
            $request->user_id = $user->id;
            $request->friend_user_id = $friend->id;
            $request->status = UserFriendRequest::STATUS_AWAITING;
            $request->save();
            \app\models\UserEvent::addFriendshipRequest($friend,$user);
        }

        Yii::$app->mailer->sendEmail($request->friendUser, 'friend-request', ['user' => $request->user, 'friend'=>$request->friendUser, 'request'=>$request]);

        return Yii::t('app','Freundschaftsanfrage versendet');
    }

    public function checkStatus() {
        if ($this->status==static::STATUS_ACCEPTED) {
            return Yii::t('app','You already accepted this friendship request');
        }
        if ($this->status==static::STATUS_DECLINED) {
            return Yii::t('app','You already rejected this friendship request');
        }

        return true;
    }

    public function accept() {
        $result=$this->checkStatus();
        if ($result!==true) return $result;

        $trx=Yii::$app->db->beginTransaction();

        $user=$this->user;
        $friend=$this->friendUser;

        $this->user->addFriend($this->friend_user_id);

        $this->delete();

        $trx->commit();

        Yii::$app->mailer->sendEmail($user, 'friend-request-accepted', ['user' => $user, 'friend'=>$friend]);

        \app\components\ChatServer::sendTextMessage($this->friend_user_id,$this->user_id,'Hi, vielen Dank für Deine Freundschaftsanfrage. Ich habe diese soeben akzeptiert.');
        \app\models\UserEvent::addFriendshipRequestAccepted($user,$this->friendUser);

        return true;
    }

    public function decline() {
        $result=$this->checkStatus();
        if ($result!==true) return $result;

        $this->status=static::STATUS_DECLINED;
        $this->save();

        Yii::$app->mailer->sendEmail($this->user, 'friend-request-declined', ['user' => $this->user, 'friend'=>$this->friendUser]);

        return Yii::t('app','friendship request declined');
    }

}
