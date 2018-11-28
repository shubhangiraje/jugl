<?php

namespace app\models;

use Yii;


class PayOutRequestForm extends \app\components\Model {
    public $packet_id;
    public $payment_method;

    public function rules() {
        return [
          [['packet_id'],'required','message'=>Yii::t('app','please select jugls amount')],
          [['payment_method'],'required','message'=>Yii::t('app','please select payout method')],
          [['packet_id'],'exist','targetClass'=>'app\models\PayOutPacket','targetAttribute'=>'id'],
          [['payment_method'],'match','pattern'=>'%^(ELV|PAYPAL)$%']
        ];
    }
}