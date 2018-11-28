<?php

namespace app\models;

use Yii;

class AdvertisingInterest extends \app\models\base\AdvertisingInterest
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'advertising_id' => Yii::t('app','Advertising ID'),
            'level1_interest_id' => Yii::t('app','Level1 Interest ID'),
            'level2_interest_id' => Yii::t('app','Level2 Interest ID'),
            'level3_interest_id' => Yii::t('app','Level3 Interest ID'),
        ];
    }
	
	public function deleteAdvertisingInterest($id){
		$model =  \app\models\AdvertisingInterest::deleteAll(['advertising_id'=>$id]);
	}
}
