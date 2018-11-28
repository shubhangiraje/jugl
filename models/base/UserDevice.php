<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_device".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $device_uuid
 * @property string $push_token
 * @property string $key
 * @property string $description
 * @property string $last_seen
 *
 * @property User $user
 */
class UserDevice extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_device';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'device_uuid'], 'required'],
            [['user_id'], 'integer'],
            [['type'], 'string'],
            [['last_seen'], 'safe'],
            [['device_uuid', 'key'], 'string', 'max' => 128],
            [['push_token', 'description'], 'string', 'max' => 256],
            [['type', 'device_uuid'], 'unique', 'targetAttribute' => ['type', 'device_uuid'], 'message' => 'The combination of Type and Device Uuid has already been taken.']
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
