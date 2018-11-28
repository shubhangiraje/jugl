<?php

namespace app\models;

use Yii;

class AdminSessionLog extends \app\models\base\AdminSessionLog
{
    const SESSION_MAX_INACTIVITY=600;

    public function attributeLabels()
    {
        return [
            'admin_id' => Yii::t('app','Admin'),
            'dt_start' => Yii::t('app','Start Date'),
            'dt_end' => Yii::t('app','End Date'),
        ];
    }
}
