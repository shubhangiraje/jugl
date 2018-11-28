<?php

namespace app\models;

use Yii;

class UserBankData extends \app\models\base\UserBankData
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'bic' => Yii::t('app','BIC'),
            'iban' => Yii::t('app','IBAN'),
            'owner' => Yii::t('app','Kontoinhaber'),
        ];
    }
}
