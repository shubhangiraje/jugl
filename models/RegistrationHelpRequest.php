<?php

namespace app\models;

use Yii;

class RegistrationHelpRequest extends \app\models\base\RegistrationHelpRequest
{
    const SEX_M = 'M';
    const SEX_F = 'F';

    public static function getSexList()
    {
        return [
            static::SEX_M => Yii::t('app', 'Man'),
            static::SEX_F => Yii::t('app', 'Woman'),
        ];
    }

    public function getSexLabel() {
        return $this->getSexList()[$this->sex];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'dt' => Yii::t('app','Dt'),
            'ip' => Yii::t('app','Ip'),
            'first_name' => Yii::t('app','First Name'),
            'last_name' => Yii::t('app','Last Name'),
            'nick_name' => Yii::t('app','Nick Name'),
            'company_name' => Yii::t('app','Company Name'),
            'birthday' => Yii::t('app','Birthday'),
            'email' => Yii::t('app','Email'),
            'phone' => Yii::t('app','Phone'),
            'sex' => Yii::t('app','Sex'),
            'step' => Yii::t('app','Step'),
        ];
    }
}
