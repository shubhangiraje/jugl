<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_team_request".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $second_user_id
 * @property string $dt
 * @property integer $user_event_id
 * @property string $text
 * @property string $type
 *
 * @property User $user
 * @property User $secondUser
 * @property UserEvent $userEvent
 */
class UserTeamRequest extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_team_request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'second_user_id', 'user_event_id', 'text', 'type'], 'required'],
            [['user_id', 'second_user_id', 'user_event_id'], 'integer'],
            [['dt'], 'safe'],
            [['text', 'type'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['second_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['second_user_id' => 'id']],
            [['user_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserEvent::className(), 'targetAttribute' => ['user_event_id' => 'id']]
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
    public function getSecondUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'second_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserEvent()
    {
        return $this->hasOne('\app\models\UserEvent', ['id' => 'user_event_id']);
    }
}
