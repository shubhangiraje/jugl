<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "admin_session_log".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property string $session
 * @property string $dt_start
 * @property string $dt_end
 * @property string $ip
 * @property string $user_agent
 *
 * @property Admin $admin
 */
class AdminSessionLog extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_session_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'session'], 'required'],
            [['admin_id'], 'integer'],
            [['dt_start', 'dt_end'], 'safe'],
            [['session', 'ip'], 'string', 'max' => 64],
            [['user_agent'], 'string', 'max' => 256]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne('\app\models\Admin', ['id' => 'admin_id']);
    }
}
