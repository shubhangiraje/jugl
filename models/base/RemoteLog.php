<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "remote_log".
 *
 * @property integer $id
 * @property string $session
 * @property string $dt
 * @property string $type
 * @property string $message
 */
class RemoteLog extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'remote_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['session', 'dt', 'type'], 'required'],
            [['type', 'message'], 'string'],
            [['session'], 'string', 'max' => 256],
            [['dt'], 'string', 'max' => 32]
        ];
    }

}
