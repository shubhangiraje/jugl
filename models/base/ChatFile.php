<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "chat_file".
 *
 * @property integer $id
 * @property string $dt
 * @property integer $user_id
 * @property integer $chat_message_id
 * @property string $link
 * @property integer $size
 * @property string $name
 * @property string $ext
 *
 * @property User $user
 * @property ChatMessage $chatMessage
 */
class ChatFile extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dt'], 'safe'],
            [['user_id', 'link', 'size', 'name', 'ext'], 'required'],
            [['user_id', 'chat_message_id', 'size'], 'integer'],
            [['link', 'name', 'ext'], 'string', 'max' => 256]
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
    public function getChatMessage()
    {
        return $this->hasOne('\app\models\ChatMessage', ['id' => 'chat_message_id']);
    }
}
