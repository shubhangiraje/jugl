<?php

namespace app\models;

use Yii;

class Info extends \app\models\base\Info {

    public function attributeLabels() {
        return [
            'id' => Yii::t('app','ID'),
            'view' => Yii::t('app','View'),
            'title_de' => Yii::t('app','Title'),
            'title_en' => Yii::t('app','Title'),
            'title_ru' => Yii::t('app','Title'),
            'description_de' => Yii::t('app','Description'),
            'description_en' => Yii::t('app','Description'),
            'description_ru' => Yii::t('app','Description'),
        ];
    }

    public function rules() {
        return array_merge(parent::rules(), [
            [['view'], 'unique']
        ]);
    }

}
