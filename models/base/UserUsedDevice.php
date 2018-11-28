<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_used_device".
 *
 * @property integer $user_id
 * @property string $device_uuid
 *
 * @property User $user
 */
class UserUsedDevice extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_used_device';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'device_uuid'], 'required'],
            [['user_id'], 'integer'],
            [['device_uuid'], 'string', 'max' => 128],
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
