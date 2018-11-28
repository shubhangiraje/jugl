<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "trollbox_message".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $country
 * @property string $dt
 * @property string $text
 * @property integer $file_id
 * @property integer $group_chat_user_id
 * @property integer $votes_up
 * @property integer $votes_down
 * @property string $status
 * @property string $status_changed_dt
 * @property integer $status_changed_user_id
 * @property integer $visible_for_all
 * @property integer $visible_for_followers
 * @property integer $visible_for_contacts
 * @property integer $is_sticky
 * @property integer $trollbox_category_id
 * @property string $device_uuid
 * @property string $type
 *
 * @property User $user
 * @property File $file
 * @property ChatUser $groupChatUser
 * @property User $statusChangedUser
 * @property TrollboxCategory $trollboxCategory
 * @property TrollboxMessageStatusHistory[] $trollboxMessageStatusHistories
 * @property TrollboxMessageVote[] $trollboxMessageVotes
 * @property User[] $users
 */
class TrollboxMessage extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trollbox_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status'], 'required'],
            [['user_id', 'country', 'file_id', 'group_chat_user_id', 'votes_up', 'votes_down', 'status_changed_user_id', 'visible_for_all', 'visible_for_followers', 'visible_for_contacts', 'is_sticky', 'trollbox_category_id'], 'integer'],
            [['dt', 'status_changed_dt'], 'safe'],
            [['text', 'status', 'type'], 'string'],
            [['device_uuid'], 'string', 'max' => 128],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['file_id' => 'id']],
            [['group_chat_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatUser::className(), 'targetAttribute' => ['group_chat_user_id' => 'user_id']],
            [['status_changed_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['status_changed_user_id' => 'id']],
            [['trollbox_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrollboxCategory::className(), 'targetAttribute' => ['trollbox_category_id' => 'id']]
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
    public function getFile()
    {
        return $this->hasOne('\app\models\File', ['id' => 'file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupChatUser()
    {
        return $this->hasOne('\app\models\ChatUser', ['user_id' => 'group_chat_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatusChangedUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'status_changed_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrollboxCategory()
    {
        return $this->hasOne('\app\models\TrollboxCategory', ['id' => 'trollbox_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrollboxMessageStatusHistories()
    {
        return $this->hasMany('\app\models\TrollboxMessageStatusHistory', ['trollbox_message_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrollboxMessageVotes()
    {
        return $this->hasMany('\app\models\TrollboxMessageVote', ['trollbox_message_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany('\app\models\User', ['id' => 'user_id'])->viaTable('trollbox_message_vote', ['trollbox_message_id' => 'id']);
    }
}
