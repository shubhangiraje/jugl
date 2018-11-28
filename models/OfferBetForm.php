<?php

namespace app\models;

use Yii;


class OfferBetForm extends \app\components\Model {
    public $offer_id;
    public $offer_request_id;
    public $price;
    public $dont_check_price;
    public $period;
    public $description;

    public function attributeLabels()
    {
        return [
            'price'=>Yii::t('app','Dein Gebot'),
            'period'=>Yii::t('app','Dein Gebot ist gültig'),
            'text'=>Yii::t('app','Ihr Nachricht an der Verkäufer')
        ];
    }

    public function rules() {
        return [
            [['price','period','offer_id'],'required'],
            ['price','number'],
            ['offer_id','offerIdValidator'],
            ['offer_request_id','offerRequestIdValidator'],
            ['offer_id','enoughMoneyValidator'],
            [['description','dont_check_price','offer_request_id'],'safe'],
        ];
    }

    public function offerIdValidator() {
        if (!$this->offer_request_id) {
            $offer = \app\models\Offer::findOne($this->offer_id);

            if (!$offer || !$offer->canCreateRequest()) {
                $this->addError('offer_id', Yii::t('app', 'Es tut uns leid, das Angebot ist nicht mehr verfügbar'));
            }
        }
    }

    public function enoughMoneyValidator() {
        $offer = \app\models\Offer::findOne($this->offer_id);

        if ($offer->pay_allow_jugl &&
            !$offer->pay_allow_bank &&
            !$offer->pay_allow_paypal &&
            !$offer->pay_allow_pod &&
            $this->price*\app\models\Setting::get('EXCHANGE_JUGLS_PER_EURO')>Yii::$app->user->identity->balance) {
                $this->addError('offer_id',Yii::t('app','Du hast nicht genug Jugls'));
        }

    }

    public function offerRequestIdValidator() {
        if ($this->offer_request_id) {
            $offerRequest=\app\models\OfferRequest::findOne($this->offer_request_id);
            if (!$offerRequest || !$offerRequest->betCanBeChanged) {
                $this->addError('offer_id', Yii::t('app', 'Es tut uns leid, das Angebot ist nicht mehr verfügbar'));
            }
            $this->offer_id=$offerRequest->offer_id;
        }
    }
}