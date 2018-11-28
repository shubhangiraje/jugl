<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_validation_phone_notification".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $dt
 *
 * @property User $user
 */
class UserValidationPhoneNotification extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_validation_phone_notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['dt'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']]
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
