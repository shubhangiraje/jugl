<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property string $name
 * @property string $title
 * @property string $type
 * @property string $value
 */
class Setting extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title', 'type', 'value'], 'required'],
            [['type'], 'string'],
            [['name'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 128],
            [['value'], 'string', 'max' => 16384]
        ];
    }

}
