<?php

namespace app\models;

use Yii;

class OfferParamValue extends \app\models\base\OfferParamValue
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'offer_id' => Yii::t('app','Offer ID'),
            'param_id' => Yii::t('app','Param ID'),
            'param_value_id' => Yii::t('app','Param Value ID'),
            'param_value' => Yii::t('app','Param Value'),
        ];
    }
}
