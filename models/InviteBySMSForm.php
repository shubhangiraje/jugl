<?php

namespace app\models;

use Yii;


class InviteBySMSForm extends \app\components\Model {
    public $phones;
    public $text;

    public function rules() {
        return [
            [['phones','text'],'required'],
            ['phonesAsArray','app\components\CountValidator','max'=>100,'message'=>Yii::t('app','please specify max {max} phones at one time')],
            ['text','string','max'=>300]
        ];
    }

    public function getPhonesAsArray() {
        return preg_split('/[\s,]+/s',$this->phones,-1,PREG_SPLIT_NO_EMPTY);
    }

    public function attributeLabels() {
        return [
            'phones'=>Yii::t('app','Phone numbers'),
            'text'=>Yii::t('app','Text'),
        ];
    }
}