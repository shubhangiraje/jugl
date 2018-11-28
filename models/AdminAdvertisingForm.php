<?php

namespace app\models;

use Yii;


class AdminAdvertisingForm extends \app\components\Model {
    public $input;
    public $type;

    const TYPE_ACTIVE=1;
    const TYPE_DISABLED=0;

    public static function getStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::TYPE_ACTIVE=>Yii::t('app','Aktiviert'),
                static::TYPE_DISABLED=>Yii::t('app','Deaktiviert')
            ];
        }

        return $items;
    }

    public function rules() {
        return [
            [['input','type'],'required']
        ];
    }

    public function attributeLabels() {
        return [
            'input'=>Yii::t('app','Werbebonus'),
            'type'=>Yii::t('app','Send by'),
        ];
    }
}