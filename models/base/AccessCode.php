<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "access_code".
 *
 * @property integer $id
 * @property string $expires
 * @property string $type
 * @property string $object
 * @property string $code
 */
class AccessCode extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'access_code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['expires'], 'safe'],
            [['type', 'code'], 'required'],
            [['type'], 'string'],
            [['object'], 'string', 'max' => 200],
            [['code'], 'string', 'max' => 32]
        ];
    }

}
