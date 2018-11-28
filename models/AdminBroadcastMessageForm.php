<?php

namespace app\models;

use Yii;


class AdminBroadcastMessageForm extends \app\components\Model {
    public $text;
    public $type;
    public $decline;

    const TYPE_EVENT='EVENT';
    const TYPE_EMAIL='EMAIL';

    public static function getTypeList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::TYPE_EVENT=>Yii::t('app','Activity'),
                static::TYPE_EMAIL=>Yii::t('app','E-Mail'),
            ];
        }

        return $items;
    }

    public function rules() {
        return [
            [['text','type'],'required']
        ];
    }

    public function attributeLabels() {
        return [
            'text'=>Yii::t('app','Text'),
            'type'=>Yii::t('app','Send by'),
        ];
    }
}