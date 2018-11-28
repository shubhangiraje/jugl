<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "group_chat_moderator_last_visit".
 *
 * @property integer $group_chat_id
 * @property integer $moderator_user_id
 * @property string $dt
 *
 * @property ChatUser $groupChat
 * @property User $moderatorUser
 */
class GroupChatModeratorLastVisit extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_chat_moderator_last_visit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_chat_id', 'moderator_user_id'], 'required'],
            [['group_chat_id', 'moderator_user_id'], 'integer'],
            [['dt'], 'safe'],
            [['group_chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatUser::className(), 'targetAttribute' => ['group_chat_id' => 'user_id']],
            [['moderator_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['moderator_user_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupChat()
    {
        return $this->hasOne('\app\models\ChatUser', ['user_id' => 'group_chat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModeratorUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'moderator_user_id']);
    }
}
