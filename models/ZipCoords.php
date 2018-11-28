<?php

namespace app\models;

use Yii;

class ZipCoords extends \app\models\base\ZipCoords
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'country_id' => Yii::t('app','Country ID'),
            'zip' => Yii::t('app','Zip'),
            'lattitude' => Yii::t('app','Lattitude'),
            'longitude' => Yii::t('app','Longitude'),
        ];
    }
}
