<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "admin_action_log".
 *
 * @property integer $id
 * @property string $dt
 * @property integer $admin_id
 * @property string $action
 *
 * @property Admin $admin
 */
class AdminActionLog extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_action_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dt'], 'safe'],
            [['admin_id', 'action'], 'required'],
            [['admin_id'], 'integer'],
            [['action'], 'string', 'max' => 64]
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
