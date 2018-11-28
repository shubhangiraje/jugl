<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_moved_users_count".
 *
 * @property integer $from_user_id
 * @property integer $to_user_id
 * @property integer $count
 *
 * @property User $fromUser
 * @property User $toUser
 */
class UserMovedUsersCount extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_moved_users_count';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from_user_id', 'to_user_id', 'count'], 'required'],
            [['from_user_id', 'to_user_id', 'count'], 'integer'],
            [['from_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['from_user_id' => 'id']],
            [['to_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['to_user_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'from_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'to_user_id']);
    }
}
