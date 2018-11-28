<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "registration_code".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $dt
 * @property string $code
 * @property integer $referral_user_id
 *
 * @property User $referralUser
 * @property User $user
 * @property User[] $users
 */
class RegistrationCode extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'registration_code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'referral_user_id'], 'integer'],
            [['dt'], 'safe'],
            [['code'], 'required'],
            [['code'], 'string', 'max' => 8],
            [['code'], 'unique']
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferralUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'referral_user_id']);
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
    public function getUsers()
    {
        return $this->hasMany('\app\models\User', ['registration_code_id' => 'id']);
    }
}
