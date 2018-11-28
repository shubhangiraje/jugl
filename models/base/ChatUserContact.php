<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "chat_user_contact".
 *
 * @property integer $user_id
 * @property integer $second_user_id
 * @property integer $decision_needed
 *
 * @property ChatUser $user
 * @property ChatUser $secondUser
 */
class ChatUserContact extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_user_contact';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'second_user_id', 'decision_needed'], 'required'],
            [['user_id', 'second_user_id', 'decision_needed'], 'integer']
        ];
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
