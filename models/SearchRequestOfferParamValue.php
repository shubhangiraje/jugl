<?php

namespace app\models;

use Yii;

class SearchRequestOfferParamValue extends \app\models\base\SearchRequestOfferParamValue
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'search_request_offer_id' => Yii::t('app','Search Request Offer ID'),
            'param_id' => Yii::t('app','Param ID'),
            'match' => Yii::t('app','Match'),
        ];
    }
}
