<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "chat_user".
 *
 * @property integer $user_id
 * @property integer $online
 * @property integer $online_mobile
 * @property string $mobile_last_seen
 *
 * @property User $user
 */
class ChatUser extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'online', 'online_mobile'], 'integer'],
            [['mobile_last_seen'], 'safe']
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
