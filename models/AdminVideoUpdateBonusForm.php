<?php

namespace app\models;

use Yii;


class AdminVideoUpdateBonusForm extends \app\components\Model {
    public $input;
    public $type;

    const TYPE_GLOMEX='glomex';
    const TYPE_DAILYMOTION='dailymotion';

    public static function getTypeList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::TYPE_GLOMEX=>Yii::t('app','Glomex'),
                static::TYPE_DAILYMOTION=>Yii::t('app','Dailymotion'),
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