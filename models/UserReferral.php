<?php

namespace app\models;

use Yii;

class UserReferral extends \app\models\base\UserReferral
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app','User ID'),
            'referral_user_id' => Yii::t('app','Referral User ID'),
            'level' => Yii::t('app','Level'),
        ];
    }
}
