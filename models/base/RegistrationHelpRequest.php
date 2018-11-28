<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "registration_help_request".
 *
 * @property integer $id
 * @property string $dt
 * @property string $ip
 * @property string $first_name
 * @property string $last_name
 * @property string $nick_name
 * @property string $company_name
 * @property string $birthday
 * @property string $email
 * @property string $phone
 * @property string $sex
 * @property integer $step
 * @property integer $user_id
 *
 * @property User $user
 */
class RegistrationHelpRequest extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'registration_help_request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dt', 'birthday'], 'safe'],
            [['sex'], 'string'],
            [['step'], 'required'],
            [['step', 'user_id'], 'integer'],
            [['ip'], 'string', 'max' => 20],
            [['first_name', 'last_name', 'nick_name', 'company_name', 'phone'], 'string', 'max' => 64],
            [['email'], 'string', 'max' => 128]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }
}
