<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "trollbox_message_status_history".
 *
 * @property integer $id
 * @property integer $trollbox_message_id
 * @property string $status
 * @property string $dt
 * @property integer $user_id
 *
 * @property User $user
 * @property TrollboxMessage $trollboxMessage
 */
class TrollboxMessageStatusHistory extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trollbox_message_status_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['trollbox_message_id', 'status', 'user_id'], 'required'],
            [['trollbox_message_id', 'user_id'], 'integer'],
            [['status'], 'string'],
            [['dt'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['trollbox_message_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrollboxMessage::className(), 'targetAttribute' => ['trollbox_message_id' => 'id']]
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
    public function getTrollboxMessage()
    {
        return $this->hasOne('\app\models\TrollboxMessage', ['id' => 'trollbox_message_id']);
    }
}
