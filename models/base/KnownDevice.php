<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "known_device".
 *
 * @property integer $id
 * @property string $device_uuid
 * @property integer $user_id
 *
 * @property User $user
 */
class KnownDevice extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'known_device';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['device_uuid', 'user_id'], 'required'],
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
