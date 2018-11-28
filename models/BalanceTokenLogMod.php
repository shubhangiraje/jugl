<?php

namespace app\models;

use Yii;

class BalanceTokenLogMod extends \app\models\base\BalanceTokenLogMod
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'admin_id' => Yii::t('app','Admin ID'),
            'balance_token_log_id' => Yii::t('app','Balance Token Log ID'),
            'comments' => Yii::t('app','Comments'),
        ];
    }
}
