<?php

namespace app\models;

use Yii;


class OfferPayForm extends \app\components\Model {
    const DELIVERY_ADDRESS_ADDRESS='address';
    const PAYMENT_METHOD_PAYPAL='PAYPAL';
    const PAYMENT_METHOD_JUGLS='JUGLS';
    const PAYMENT_METHOD_POD='POD';

    public $offer;
    public $offerRequest;
    public $payment_method;
    public $delivery_address;
    public $address_city;
    public $address_zip;
    public $address_street;
    public $address_house_number;

    public $resolved_pay_data;
    public $resolved_pay_method;
    public $resolved_delivery_address;
    public $resolved_delivery_address_city;
    public $resolved_delivery_address_zip;
    public $resolved_delivery_address_street;
    public $resolved_delivery_address_house_number;

    public function rules() {
        return [
            [['payment_method'],'required','message'=>Yii::t('app','Bitte wähle eine Zahlungsmöglichkeit!')],
            [['address_city','address_zip','address_street','address_house_number'],'required','when'=>function($model) {
                return $model->delivery_address==static::DELIVERY_ADDRESS_ADDRESS;
            }],
            [['payment_method'],'paymentMethodValidator'],
            [['delivery_address'],'deliveryAddressValidator','skipOnEmpty'=>false],
        ];
    }

    public function resolvePayData() {
        $this->resolved_pay_data=null;
        $this->resolved_pay_method=null;

        if ($this->payment_method==static::PAYMENT_METHOD_PAYPAL) {
            $this->resolved_pay_data=Yii::t('app','Paypal, E-Mail: {email}',['email'=>$this->offer->user->paypal_email]);
            $this->resolved_pay_method=\app\models\OfferRequest::PAY_METHOD_PAYPAL;
            return;
        }

        if ($this->payment_method==static::PAYMENT_METHOD_JUGLS) {
            $this->resolved_pay_method=\app\models\OfferRequest::PAY_METHOD_JUGLS;
            $this->resolved_pay_data=Yii::t('app','Jugls');
            return;
        }

        if ($this->payment_method==static::PAYMENT_METHOD_POD) {
            $this->resolved_pay_method=\app\models\OfferRequest::PAY_METHOD_POD;
            $this->resolved_pay_data='';
            return;
        }

        $this->resolved_pay_method=\app\models\OfferRequest::PAY_METHOD_BANK;

        if (!preg_match('/^bank_data_(\d+)$/',$this->payment_method,$m)) return;
        if (count($this->offer->user->userBankDatas)<=$m[1]) return;

        $pd=$this->offer->user->userBankDatas[$m[1]];
        $this->resolved_pay_data=Yii::t('app',"Banküberweisung, IBAN: {iban} BIC: {bic} Kontoinhaber: {owner}",[
            'iban'=>$pd->iban,
            'bic'=>$pd->bic,
            'owner'=>$pd->owner
        ]);
    }

    public function paymentMethodValidator($attribute,$params) {
        $this->resolvePayData();

        if ($this->resolved_pay_data=='' && $this->resolved_pay_method!=static::PAYMENT_METHOD_POD) $this->addError($attribute,Yii::t('app','Bitte wähle eine Zahlungsmöglichkeit!'));

        if ($this->resolved_pay_method==\app\models\OfferRequest::PAY_METHOD_JUGLS) {

            switch($this->offer->type) {
                case Offer::TYPE_AUCTION:
                    $priceJugls=$this->offerRequest->bet_price*\app\models\Setting::get('EXCHANGE_JUGLS_PER_EURO');
                    break;
                case Offer::TYPE_AUTOSELL:
                    $priceJugls=$this->offer->price*\app\models\Setting::get('EXCHANGE_JUGLS_PER_EURO');
                    break;
            }


            if ($this->offerRequest->user->balance<$priceJugls) {
                $this->addError($attribute,Yii::t('app','Du hast nicht genug Jugls.'));
            }
        }
    }


    public function resolveDeliveryAddress() {
        $this->resolved_delivery_address=null;

        if ($this->resolved_pay_method==static::PAYMENT_METHOD_POD) return;

        if ($this->delivery_address==static::DELIVERY_ADDRESS_ADDRESS) {
            $this->resolved_delivery_address="{$this->address_street} {$this->address_house_number}, {$this->address_zip} {$this->address_city}";
            $this->resolved_delivery_address_street=$this->address_street;
            $this->resolved_delivery_address_house_number=$this->address_house_number;
            $this->resolved_delivery_address_city=$this->address_city;
            $this->resolved_delivery_address_zip=$this->address_zip;
        }

        if (!preg_match('/^delivery_address_(\d+)$/',$this->delivery_address,$m)) return;
        if (count($this->offerRequest->user->userDeliveryAddresses)<=$m[1]) return;

        $da=$this->offerRequest->user->userDeliveryAddresses[$m[1]];
        $this->resolved_delivery_address="{$da->street} {$da->house_number}, {$da->zip} {$da->city}";

        $this->resolved_delivery_address_street=$da->street;
        $this->resolved_delivery_address_house_number=$da->house_number;
        $this->resolved_delivery_address_city=$da->city;
        $this->resolved_delivery_address_zip=$da->zip;
    }

    public function deliveryAddressValidator($attribute,$params) {
        $this->resolveDeliveryAddress();

        if ($this->payment_method!=static::PAYMENT_METHOD_POD) {
            if ($this->resolved_delivery_address == '') $this->addError($attribute, Yii::t('app', 'Bitte gib eine Lieferadresse an!'));
        }
    }

    public function attributeLabels() {
        return [
            'address_city'=>Yii::t('app','Ort'),
            'address_zip'=>Yii::t('app','Plz'),
            'address_street'=>Yii::t('app','Strasse'),
            'address_house_number'=>Yii::t('app','Hausnummer'),
        ];
    }
}