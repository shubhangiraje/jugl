<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "default_text".
 *
 * @property integer $id
 * @property string $text
 * @property string $category
 */
class DefaultText extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'default_text';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'required'],
            [['text'], 'string'],
            [['category'], 'string', 'max' => 128]
        ];
    }

}
