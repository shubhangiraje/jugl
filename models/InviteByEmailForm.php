<?php

namespace app\models;

use Yii;


class InviteByEmailForm extends \app\components\Model {
    public $emails;
    public $text;

    public function rules() {
        return [
            ['emails','required', 'message'=>Yii::t('app', 'Bitte gib eine Emailadresse ein.')],
            ['text','required', 'message'=>Yii::t('app', 'Bitte gib einen Einadungstext ein.')],
            ['emailsAsArray','app\components\CountValidator','max'=>100,'message'=>Yii::t('app','please specify max {max} emails at one time')],
            ['text','string','max'=>2048]
        ];
    }

    public function getEmailsAsArray() {
        return preg_split('/[\s,]+/s',$this->emails,-1,PREG_SPLIT_NO_EMPTY);
    }

    public function attributeLabels() {
        return [
            'emails'=>Yii::t('app','E-Mail addresses'),
            'text'=>Yii::t('app','Text'),
        ];
    }
}
