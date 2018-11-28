<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "invite_me".
 *
 * @property integer $id
 * @property string $dt
 * @property string $ip
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $normalized_phone
 * @property integer $invited_count
 * @property integer $invited_by_sms
 * @property integer $invited_by_email
 */
class InviteMe extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invite_me';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dt'], 'safe'],
            [['ip', 'first_name', 'last_name', 'email', 'phone'], 'required'],
            [['invited_count', 'invited_by_sms', 'invited_by_email'], 'integer'],
            [['ip'], 'string', 'max' => 20],
            [['first_name', 'last_name', 'phone', 'normalized_phone'], 'string', 'max' => 64],
            [['email'], 'string', 'max' => 128]
        ];
    }

}
