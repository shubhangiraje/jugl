<?php

namespace app\models;

use Yii;

class OfferRequestModification extends \app\models\base\OfferRequestModification
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'offer_request_id' => Yii::t('app','Offer Request ID'),
            'dt' => Yii::t('app','Dt'),
            'price' => Yii::t('app','Price'),
        ];
    }
}
