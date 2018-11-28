<?php

namespace app\models;

use Yii;

class SearchRequestInterest extends \app\models\base\SearchRequestInterest
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'search_request_id' => Yii::t('app','Search Request ID'),
            'level1_interest_id' => Yii::t('app','Level1 Interest ID'),
            'level2_interest_id' => Yii::t('app','Level2 Interest ID'),
            'level3_interest_id' => Yii::t('app','Level3 Interest ID'),
        ];
    }
}
