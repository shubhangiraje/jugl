<?php

namespace app\models;

use Yii;

class UserValidationPhoneNotification extends \app\models\base\UserValidationPhoneNotification
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','User ID'),
            'dt' => Yii::t('app','Dt'),
        ];
    }
}
