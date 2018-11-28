<?php

namespace app\models;

use Yii;

class SearchRequestOfferFile extends \app\models\base\SearchRequestOfferFile
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'search_request_offer_id' => Yii::t('app','Search Request Offer ID'),
            'file_id' => Yii::t('app','File ID'),
            'sort_order' => Yii::t('app','Sort Order'),
        ];
    }
}
