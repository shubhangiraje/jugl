<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "chat_user_ignore".
 *
 * @property integer $user_id
 * @property integer $ignore_user_id
 * @property integer $moderator_user_id
 * @property string $dt
 *
 * @property User $moderatorUser
 * @property ChatUser $user
 * @property ChatUser $ignoreUser
 */
class ChatUserIgnore extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_user_ignore';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'ignore_user_id'], 'required'],
            [['user_id', 'ignore_user_id', 'moderator_user_id'], 'integer'],
            [['dt'], 'safe'],
            [['moderator_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['moderator_user_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatUser::className(), 'targetAttribute' => ['user_id' => 'user_id']],
            [['ignore_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatUser::className(), 'targetAttribute' => ['ignore_user_id' => 'user_id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModeratorUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'moderator_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\ChatUser', ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIgnoreUser()
    {
        return $this->hasOne('\app\models\ChatUser', ['user_id' => 'ignore_user_id']);
    }
}
