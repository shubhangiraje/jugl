<?php

namespace app\models;

use Yii;

class OfferFavorite extends \app\models\base\OfferFavorite
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app','User ID'),
            'offer_id' => Yii::t('app','Offer ID'),
        ];
    }
}
