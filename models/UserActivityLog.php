<?php

namespace app\models;

use Yii;

class UserActivityLog extends \app\models\base\UserActivityLog
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app','User ID'),
            'dt' => Yii::t('app','Dt'),
        ];
    }
}
