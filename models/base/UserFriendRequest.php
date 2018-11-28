<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_friend_request".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $friend_user_id
 * @property string $status
 *
 * @property User $user
 * @property User $friendUser
 */
class UserFriendRequest extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_friend_request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'friend_user_id', 'status'], 'required'],
            [['user_id', 'friend_user_id'], 'integer'],
            [['status'], 'string']
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
    public function getFriendUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'friend_user_id']);
    }
}
