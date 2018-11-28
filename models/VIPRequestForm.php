<?php

namespace app\models;

use Yii;


class VIPRequestForm extends \app\components\Model {
    public $payment_method;
    public $packet;

    public function rules() {
        return [
          [['payment_method'],'required','message'=>Yii::t('app','please select payin method')],
          [['payment_method'],'match','pattern'=>'%^(PAYONE_(GIROPAY|PAYPAL|CC|SOFORT)|ELV)$%'],
          [['packet'],'required'],
          [['packet'],'validatePacket'],
        ];
    }

    public function validatePacket() {
        $packets=\app\models\PayInRequest::getVipPacketPrices();
        if (!$packets[$this->packet]) {
            $this->addError('packet',Yii::t('app','Invalid VIP packet'));
        }
    }
}