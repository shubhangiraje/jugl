<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "trollbox_message_vote".
 *
 * @property integer $trollbox_message_id
 * @property integer $user_id
 * @property integer $vote
 *
 * @property TrollboxMessage $trollboxMessage
 * @property User $user
 */
class TrollboxMessageVote extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trollbox_message_vote';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['trollbox_message_id', 'user_id', 'vote'], 'required'],
            [['trollbox_message_id', 'user_id', 'vote'], 'integer'],
            [['trollbox_message_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrollboxMessage::className(), 'targetAttribute' => ['trollbox_message_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrollboxMessage()
    {
        return $this->hasOne('\app\models\TrollboxMessage', ['id' => 'trollbox_message_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }
}
