<?php

namespace app\models;

use Yii;

class OfferInterest extends \app\models\base\OfferInterest
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'offer_id' => Yii::t('app','Offer ID'),
            'level1_interest_id' => Yii::t('app','Level1 Interest ID'),
            'level2_interest_id' => Yii::t('app','Level2 Interest ID'),
            'level3_interest_id' => Yii::t('app','Level3 Interest ID'),
        ];
    }
}
