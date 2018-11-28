<?php

namespace app\models;

use Yii;
use yii\base\Model;


class IcoPaymentDeposit extends IcoPayment {

    public $period_months;
    public $token_deposit_guarantee_id;

    public function rules() {
        return array_merge(parent::rules(),[
            [['period_months'],'required','message'=>Yii::t('app','Bitte wähle Zeitraum')],
            [['token_deposit_guarantee_id'],'required','when'=>function($model){return count(TokenDepositGuarantee::getList())>0;},'message'=>Yii::t('app','Bitte wähle Immobilie')],
            [['token_deposit_guarantee_id'],'exist',
                'targetClass'=>\app\models\TokenDepositGuarantee::class,
                'targetAttribute'=>['token_deposit_guarantee_id'=>'id'],
                'filter'=>['status'=>\app\models\TokenDepositGuarantee::STATUS_ACTIVE]
            ]
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[
            'period_months'=>Yii::t('app','Zeitraum'),
        ]);
    }
}
