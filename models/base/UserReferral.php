<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_referral".
 *
 * @property integer $user_id
 * @property integer $referral_user_id
 * @property integer $level
 *
 * @property User $user
 * @property User $referralUser
 */
class UserReferral extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_referral';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'referral_user_id', 'level'], 'required'],
            [['user_id', 'referral_user_id', 'level'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['referral_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['referral_user_id' => 'id']]
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
    public function getReferralUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'referral_user_id']);
    }
}
