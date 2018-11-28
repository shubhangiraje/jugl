<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "faq".
 *
 * @property integer $id
 * @property string $question_de
 * @property string $question_en
 * @property string $question_ru
 * @property string $response_de
 * @property string $response_en
 * @property string $response_ru
 */
class Faq extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'faq';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_de', 'response_de'], 'required'],
			[['question_de', 'question_en', 'question_ru','response_de', 'response_en', 'response_ru'], 'string'],
            [['question_de', 'question_en', 'question_ru'], 'string', 'max' => 256]
        ];
    }

}
