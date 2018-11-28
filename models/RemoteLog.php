<?php

namespace app\models;

use Yii;

class RemoteLog extends \app\models\base\RemoteLog
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'session' => Yii::t('app','Session'),
            'dt' => Yii::t('app','Dt'),
            'type' => Yii::t('app','Type'),
            'message' => Yii::t('app','Message'),
        ];
    }
}
