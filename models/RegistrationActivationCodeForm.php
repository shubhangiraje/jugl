<?php

namespace app\models;

use Yii;


class RegistrationActivationCodeForm extends \app\components\Model
{
    public $tries;
    public $sentCode;
    public $code;
    public $verifyCode;
    public $request_help;

    public function rules()
    {
        return [
            ['code','required'],
            ['verifyCode','captcha','on'=>'withCaptcha'],
            ['code','compare','compareAttribute'=>'sentCode','message'=>Yii::t('app','Ungültiger Aktivierungscode'),'enableClientValidation'=>false],
            ['request_help','safe']
        ];
    }

    public function scenarios() {
        $scenarios=parent::scenarios();
        $scenarios['withCaptcha']=$scenarios[static::SCENARIO_DEFAULT];

        return $scenarios;
    }

    public function attributeLabels() {
        return [
            'code'=>Yii::t('app','Hier Code eingeben'),
            'verifyCode'=>Yii::t('app','Code eingeben')
        ];
    }
}
