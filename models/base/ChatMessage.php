<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "chat_message".
 *
 * @property integer $id
 * @property string $dt
 * @property integer $user_id
 * @property integer $second_user_id
 * @property integer $sender_user_id
 * @property integer $outgoing_chat_message_id
 * @property string $type
 * @property string $content_type
 * @property string $text
 * @property string $extra
 * @property integer $deleted
 *
 * @property ChatConversation[] $chatConversations
 * @property ChatFile[] $chatFiles
 * @property ChatMessage $outgoingChatMessage
 * @property ChatMessage[] $chatMessages
 * @property ChatUser $user
 * @property ChatUser $secondUser
 */
class ChatMessage extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dt'], 'safe'],
            [['user_id', 'second_user_id', 'type', 'content_type'], 'required'],
            [['user_id', 'second_user_id', 'sender_user_id', 'outgoing_chat_message_id', 'deleted'], 'integer'],
            [['type', 'content_type'], 'string'],
            [['text', 'extra'], 'string', 'max' => 4096],
            [['outgoing_chat_message_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatMessage::className(), 'targetAttribute' => ['outgoing_chat_message_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatUser::className(), 'targetAttribute' => ['user_id' => 'user_id']],
            [['second_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => ChatUser::className(), 'targetAttribute' => ['second_user_id' => 'user_id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatConversations()
    {
        return $this->hasMany('\app\models\ChatConversation', ['last_chat_message_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatFiles()
    {
        return $this->hasMany('\app\models\ChatFile', ['chat_message_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutgoingChatMessage()
    {
        return $this->hasOne('\app\models\ChatMessage', ['id' => 'outgoing_chat_message_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatMessages()
    {
        return $this->hasMany('\app\models\ChatMessage', ['outgoing_chat_message_id' => 'id']);
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
    public function getSecondUser()
    {
        return $this->hasOne('\app\models\ChatUser', ['user_id' => 'second_user_id']);
    }
}
