<?php

namespace app\models;

use Yii;

class UserFriend extends \app\models\base\UserFriend
{
    protected static $_loggedUserFriends;

    protected static function getLoggedUserFriends() {
        if (!isset(static::$_loggedUserFriends)) {
            static::$_loggedUserFriends=Yii::$app->db->createCommand("select friend_user_id from user_friend where user_id=:user_id",[
                ':user_id'=>Yii::$app->user->id
            ])->queryColumn();
        }

        return static::$_loggedUserFriends;
    }

    public static function isLoggedUserFriend($userId) {
        return in_array($userId,static::getLoggedUserFriends());
    }

    public function afterSave($insert, $changedAttributes) {
        static::$_loggedUserFriends=null;
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete() {
        static::$_loggedUserFriends=null;
        parent::afterDelete();
    }

    public static function changeFriend($user, $friendUserId) {
        $model = static::find()->where(['user_id'=>$user->id, 'friend_user_id'=>$friendUserId])->one();
        if($model) {
            $user->deleteFriend($friendUserId, true);
            $isFriend=false;
        } else {
            $user->addFriend($friendUserId, true);
            $isFriend=true;
        }
        return [
            'result'=>true,
            'isFriend'=>$isFriend
        ];
    }

    public function attributeLabels() {
        return [
            'user_id' => Yii::t('app','User ID'),
            'friend_user_id' => Yii::t('app','Friend User ID'),
        ];
    }
}
