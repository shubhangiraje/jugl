<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_stick_to_parent_request".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $referral_user_id
 * @property string $expires_at
 *
 * @property User $user
 * @property User $referralUser
 */
class UserStickToParentRequest extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_stick_to_parent_request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'referral_user_id'], 'required'],
            [['user_id', 'referral_user_id'], 'integer'],
            [['expires_at'], 'safe'],
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
