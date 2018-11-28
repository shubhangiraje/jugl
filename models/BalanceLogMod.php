<?php

namespace app\models;

use Yii;

class BalanceLogMod extends \app\models\base\BalanceLogMod
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'admin_id' => Yii::t('app','Admin ID'),
            'comments' => Yii::t('app','Kommentar'),
            'balance_log_id' => Yii::t('app','Balance Log ID'),
        ];
    }
}
