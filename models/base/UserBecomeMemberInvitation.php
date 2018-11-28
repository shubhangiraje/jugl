<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_become_member_invitation".
 *
 * @property integer $user_id
 * @property integer $second_user_id
 * @property string $dt
 * @property integer $ms
 *
 * @property User $user
 * @property User $secondUser
 */
class UserBecomeMemberInvitation extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_become_member_invitation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'second_user_id', 'ms'], 'required'],
            [['user_id', 'second_user_id', 'ms'], 'integer'],
            [['dt'], 'safe'],
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
