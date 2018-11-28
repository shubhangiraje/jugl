<?php

namespace app\models;

use Yii;

class PayOutPacket extends \app\models\base\PayOutPacket
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'jugl_sum' => Yii::t('app','Jugl Sum'),
            'currency_sum' => Yii::t('app','Currency Sum'),
        ];
    }
}
