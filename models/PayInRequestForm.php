<?php

namespace app\models;

use Yii;


class PayInRequestForm extends \app\components\Model {
    public $packet_id;
    public $payment_method;

    public function rules() {
        return [
          [['packet_id'],'required','message'=>Yii::t('app','please select jugls amount')],
          [['payment_method'],'required','message'=>Yii::t('app','please select payin method')],
          [['packet_id'],'exist','targetClass'=>'app\models\PayInPacket','targetAttribute'=>'id'],
          [['payment_method'],'match','pattern'=>'%^(PAYONE_(GIROPAY|PAYPAL|CC|SOFORT)|ELV)$%']
        ];
    }
}