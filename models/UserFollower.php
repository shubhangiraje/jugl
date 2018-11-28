<?php

namespace app\models;

use Yii;

class UserFollower extends \app\models\base\UserFollower {

    protected static $_userFollowing;

    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app','User ID'),
            'follower_user_id' => Yii::t('app','Follower User ID'),
        ];
    }

    public static function subscribe($followerUser, $subscribeUserId) {
        $follower=User::findOne($subscribeUserId);

        if (!$follower) {
            return Yii::t('app','user doesn\'t exists');
        }

        $model = new static();
        $model->user_id = $subscribeUserId;
        $model->follower_user_id = $followerUser->id;
        $model->save();
        return true;
    }


    public static function changeSubscribe($followerUser, $subscribeUserId) {
        $model = static::find()->where(['user_id'=>$subscribeUserId, 'follower_user_id'=>$followerUser->id])->one();
        if($model) {
            $model->delete();
            $isFollow=false;
        } else {
            static::subscribe($followerUser, $subscribeUserId);
            $isFollow=true;
        }
        return [
            'result'=>true,
            'isFollow'=>$isFollow
        ];
    }

    protected static function getUserFollow() {
        if (!isset(static::$_userFollowing)) {
            static::$_userFollowing=Yii::$app->db->createCommand("select user_id from user_follower where follower_user_id=:user_id",[
                ':user_id'=>Yii::$app->user->id
            ])->queryColumn();
        }
        return static::$_userFollowing;
    }

    public static function isUserFollow($userId) {
        return in_array($userId,static::getUserFollow());
    }


}
