<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_follower".
 *
 * @property integer $user_id
 * @property integer $follower_user_id
 *
 * @property User $user
 * @property User $followerUser
 */
class UserFollower extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_follower';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'follower_user_id'], 'required'],
            [['user_id', 'follower_user_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['follower_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['follower_user_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowerUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'follower_user_id']);
    }
}
