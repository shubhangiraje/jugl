<?php

namespace app\models;

use Yii;
use yii\base\Model;


class IcoPayoutForm extends Model {

    const PAYMENT_METHOD_ELV = 'ELV';

    public $iban;
    public $bic;
    public $kontoinhaber;
    public $payment_method;

    public function rules() {
        return [
            [['payment_method','iban','bic','kontoinhaber'],'required'],
        ];
    }

    public function attributeLabels() {
        return [
            'payment_method'=>Yii::t('app','Payment Method'),
            'iban'=>Yii::t('app','IBAN'),
            'bic'=>Yii::t('app','BIC'),
            'kontoinhaber'=>Yii::t('app','Kontoinhaber'),
        ];
    }

    public static function getPaymentMethodList() {
        return [
            static::PAYMENT_METHOD_ELV=>Yii::t('app', 'Banküberweisung')
        ];
    }

}
