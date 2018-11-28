<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "invitation".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $dt
 * @property string $type
 * @property string $status
 * @property string $address
 * @property string $text
 * @property integer $referral_user_id
 *
 * @property User $user
 * @property User $referralUser
 */
class Invitation extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invitation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'status', 'address', 'text'], 'required'],
            [['user_id', 'referral_user_id'], 'integer'],
            [['dt'], 'safe'],
            [['type', 'status'], 'string'],
            [['address'], 'string', 'max' => 64],
            [['text'], 'string', 'max' => 2048]
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
