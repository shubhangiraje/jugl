<?php

namespace app\models;

use Yii;

class ChatMessage extends \app\models\base\ChatMessage
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'dt' => Yii::t('app','Dt'),
            'user_id' => Yii::t('app','User ID'),
            'second_user_id' => Yii::t('app','Second User ID'),
            'sender_user_id' => Yii::t('app','Sender User ID'),
            'outgoing_chat_message_id' => Yii::t('app','Outgoing Chat Message ID'),
            'type' => Yii::t('app','Type'),
            'content_type' => Yii::t('app','Content Type'),
            'text' => Yii::t('app','Text'),
            'extra' => Yii::t('app','Extra'),
            'deleted' => Yii::t('app','Deleted'),
        ];
    }

    public function getSenderUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'sender_user_id']);
    }

}
