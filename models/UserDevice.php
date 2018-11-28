<?php

namespace app\models;

use Yii;

class UserDevice extends \app\models\base\UserDevice
{
    public function transactions()
    {
        return [
            static::SCENARIO_DEFAULT=>static::OP_DELETE|static::OP_INSERT
        ];
    }

    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','User ID'),
            'type' => Yii::t('app','Type'),
            'device_id' => Yii::t('app','Device ID'),
            'key' => Yii::t('app','Key'),
            'description' => Yii::t('app','Description'),
            'last_seen' => Yii::t('app','Last Seen'),
        ];
    }
}
