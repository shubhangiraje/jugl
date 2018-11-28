<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_team_feedback".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $second_user_id
 * @property string $feedback
 * @property integer $rating
 * @property string $create_dt
 *
 * @property User $user
 * @property User $secondUser
 */
class UserTeamFeedback extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_team_feedback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'second_user_id', 'feedback', 'rating'], 'required'],
            [['user_id', 'second_user_id', 'rating'], 'integer'],
            [['create_dt'], 'safe'],
            [['feedback'], 'string', 'max' => 4096],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['second_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['second_user_id' => 'id']]
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
}
