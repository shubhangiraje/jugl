<?php

namespace app\models;

use Yii;
use yii\base\Model;


class IcoPayment extends Model {

    const PAYMENT_METHOD_PAYONE_GIROPAY = 'PAYONE_GIROPAY';
    const PAYMENT_METHOD_PAYONE_CC = 'PAYONE_CC';
    const PAYMENT_METHOD_PAYONE_SOFORT = 'PAYONE_SOFORT';
    const PAYMENT_METHOD_ELV = 'ELV';
    const PAYMENT_METHOD_JUGL = 'JUGL';

    public $payment_method;
    public $tokens;

    public function rules() {
        return [
            [['payment_method'],'required','message'=>Yii::t('app','Bitte wähle eine Einzahlungsmethode')],
            [['payment_method'],'match','pattern'=>'%^(PAYONE_(GIROPAY|CC|SOFORT)|ELV|JUGL)$%'],
            ['tokens', 'required'],
            ['tokens', 'tokenAmountValidator']
        ];
    }

    public function tokenAmountValidator($attribute, $params, $validator) {
        $validator = new \yii\validators\NumberValidator([
            'integerOnly'=>true,
            'min'=>$this->payment_method==static::PAYMENT_METHOD_JUGL ? 10:\app\models\Setting::get('TOKEN_MIN_BUY_QUANTITY'),
            'max'=>100000
        ]);

        if (!$validator->validate($this->$attribute, $error)) {
            $this->addError($attribute,$error);
        }
    }

    public function attributeLabels()
    {
        return [
            'tokens'=>Yii::t('app','Tokens'),
            'payment_method'=>Yii::t('app','Payment Method')
        ];
    }

    public static function getPaymentMethodList() {
        return [
            static::PAYMENT_METHOD_PAYONE_GIROPAY=>Yii::t('app', 'Giropay'),
            static::PAYMENT_METHOD_PAYONE_CC=>Yii::t('app', 'Kreditkarte'),
            static::PAYMENT_METHOD_PAYONE_SOFORT=>Yii::t('app', 'Sofortüberweisung'),
            static::PAYMENT_METHOD_ELV=>Yii::t('app', 'Banküberweisung'),
            static::PAYMENT_METHOD_JUGL=>Yii::t('app', 'Jugls')
        ];
    }


}
