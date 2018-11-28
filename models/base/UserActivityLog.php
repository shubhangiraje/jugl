<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_activity_log".
 *
 * @property integer $user_id
 * @property string $dt
 * @property string $dt_full
 *
 * @property User $user
 */
class UserActivityLog extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_activity_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'dt', 'dt_full'], 'required'],
            [['user_id'], 'integer'],
            [['dt', 'dt_full'], 'safe']
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
