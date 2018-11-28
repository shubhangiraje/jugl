<?php

namespace app\models;

use Yii;

class UserUsedDevice extends \app\models\base\UserUsedDevice
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app','User ID'),
            'device_uuid' => Yii::t('app','Device Uuid'),
        ];
    }
}
