<?php

namespace app\models;

use Yii;

class RegistrationCodePacket extends \app\models\base\RegistrationCodePacket
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'registration_codes_count' => Yii::t('app','Registration Codes Count'),
            'sum' => Yii::t('app','Sum'),
        ];
    }
}
