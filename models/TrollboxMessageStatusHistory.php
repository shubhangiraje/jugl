<?php

namespace app\models;

use Yii;

class TrollboxMessageStatusHistory extends \app\models\base\TrollboxMessageStatusHistory
{
    const STATUS_ACTIVE='ACTIVE';
    const STATUS_REJECTED='REJECTED';
    const STATUS_AWAITING_ACTIVATION='AWAITING_ACTIVATION';

    public static function getStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::STATUS_ACTIVE=>Yii::t('app','Aktiv'),
                static::STATUS_REJECTED=>Yii::t('app','Abgelehnt'),
                static::STATUS_AWAITING_ACTIVATION=>Yii::t('app','Neu'),
            ];
        }

        return $items;
    }

    public function getStatusLabel() {
        return static::getStatusList()[$this->status];
    }

    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'trollbox_message_id' => Yii::t('app','Trollbox Message ID'),
            'status' => Yii::t('app','Status'),
            'dt' => Yii::t('app','Dt'),
            'user_id' => Yii::t('app','User ID'),
        ];
    }
}
