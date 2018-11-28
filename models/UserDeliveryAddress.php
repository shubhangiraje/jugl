<?php

namespace app\models;

use Yii;

class UserDeliveryAddress extends \app\models\base\UserDeliveryAddress
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','User ID'),
            'street' => Yii::t('app','Street'),
            'house_number' => Yii::t('app','House Number'),
            'city' => Yii::t('app','City'),
            'zip' => Yii::t('app','Zip'),
            'sort_order' => Yii::t('app','Sort Order'),
        ];
    }
}
