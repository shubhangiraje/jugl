<?php

namespace app\models;

use Yii;

class AdvertisingSearchRequestProvider extends \app\models\base\AdvertisingSearchRequestProvider
{

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
			'provider_id' => Yii::t('app', 'provider id'),
			'auth_token' => Yii::t('app', 'Auth token'),
        ];
    }
}
