<?php

namespace app\models;

use Yii;

class Faq extends \app\models\base\Faq
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'question_de' => Yii::t('app','Fragen'),
            'question_en' => Yii::t('app','Fragen'),
            'question_ru' => Yii::t('app','Fragen'),
            'response_de' => Yii::t('app','Antworten'),
            'response_en' => Yii::t('app','Antworten'),
            'response_ru' => Yii::t('app','Antworten'),
        ];
    }
}
