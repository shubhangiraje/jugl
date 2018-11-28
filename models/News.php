<?php

namespace app\models;

use Yii;

class News extends \app\models\base\News
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'image_file_id' => Yii::t('app','Image'),
            'dt' => Yii::t('app','Date'),
			'title_de' => Yii::t('app','Title'),
			'title_en' => Yii::t('app','Title'),
			'title_ru' => Yii::t('app','Title'),
            'text_de' => Yii::t('app','Text'),
            'text_en' => Yii::t('app','Text'),
            'text_ru' => Yii::t('app','Text'),
        ];
    }
}
