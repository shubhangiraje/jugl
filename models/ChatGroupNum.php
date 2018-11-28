<?php

namespace app\models;

use Yii;

class ChatGroupNum extends \app\models\base\ChatGroupNum
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
        ];
    }
}
