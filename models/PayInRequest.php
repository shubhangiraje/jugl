<?php

namespace app\models;

use Yii;

class PayInRequest extends \app\models\base\PayInRequest
{
    const RETURN_STATUS_AWAITING='AWAITING';
    const RETURN_STATUS_SUCCESS='SUCCESS';
    const RETURN_STATUS_FAILURE='FAILURE';
    const RETURN_STATUS_PENDING='PENDING';
    const RETURN_STATUS_CANCEL='CANCEL';

    const CONFIRM_STATUS_AWAITING='AWAITING';
    const CONFIRM_STATUS_SUCCESS='SUCCESS';
    const CONFIRM_STATUS_FAILURE='FAILURE';

    const PAYMENT_METHOD_PAYONE_ELV='PAYONE_ELV';
    const PAYMENT_METHOD_PAYONE_GIROPAY='PAYONE_GIROPAY';
    const PAYMENT_METHOD_PAYONE_CC='PAYONE_CC';
    const PAYMENT_METHOD_PAYONE_PAYPAL='PAYONE_PAYPAL';
    const PAYMENT_METHOD_PAYONE_SOFORT='PAYONE_SOFORT';
    const PAYMENT_METHOD_ELV='ELV';
    const PAYMENT_METHOD_JUGL='JUGL';

    const TYPE_PAY_IN='PAY_IN';
    const TYPE_PAY_IN_TOKEN='PAY_IN_TOKEN';
    const TYPE_PAY_IN_TOKEN_DEPOSIT='PAY_IN_TOKEN_DEPOSIT';
    const TYPE_PACKET='PACKET';
    const TYPE_PACKET_VIP_PLUS='PACKET_VIP_PLUS';


    public static function getVipPrice($months) {
        $settingName=Yii::$app->user->identity->packet==\app\models\User::PACKET_STANDART ? 'VIP_UPGRADE_COST_':'VIP_COST_';
        $settingName.=intval($months);
        $settingName.='_MONTHS';

        return \app\models\Setting::get($settingName);
    }

    public static function getVipPacketPrices() {
        return [
            1=>static::getVipPrice(1),
            3=>static::getVipPrice(3),
            6=>static::getVipPrice(6),
            12=>static::getVipPrice(12),
            36=>static::getVipPrice(36)
        ];
    }

    public static function getVipPacketList() {
        return [
            [
                'value'=>1,
                'title'=>Yii::t('app','1 Monat: {price}€',['price'=>\app\components\Helper::formatPrice(static::getVipPrice(1))])
            ],
            [
                'value'=>3,
                'title'=>Yii::t('app','3 Monate: {price}€',['price'=>\app\components\Helper::formatPrice(static::getVipPrice(3))])
            ],
            [
                'value'=>6,
                'title'=>Yii::t('app','6 Monate: {price}€',['price'=>\app\components\Helper::formatPrice(static::getVipPrice(6))])
            ],
            [
                'value'=>12,
                'title'=>Yii::t('app','1 Jahr: {price}€',['price'=>\app\components\Helper::formatPrice(static::getVipPrice(12))])
            ],
            [
                'value'=>36,
                'title'=>Yii::t('app','3 Jahre: {price}€',['price'=>\app\components\Helper::formatPrice(static::getVipPrice(36))])
            ]
        ];
    }

    private function getPaymentDescription() {
        if ($this->type==PayInRequest::TYPE_PACKET) {
            return Yii::t('app','Premium Mitgliedschaft');
        }

        if ($this->type==PayInRequest::TYPE_PACKET_VIP_PLUS) {
            return Yii::t('app','PremiumPlus Mitgliedschaft');
        }

        if ($this->type==PayInRequest::TYPE_PAY_IN) {
            return 'Buy '.$this->jugl_sum.' Jugls';
        }

        if ($this->type==PayInRequest::TYPE_PAY_IN_TOKEN) {
            return 'Buy '.$this->jugl_sum.' Tokens';
        }
    }


    // Returns the value for the request parameter "requestFingerprintOrder".
    private function getRequestFingerprintOrder($theParams) {
        $ret = "";
        foreach ($theParams as $key=>$value) {
            $ret .= "$key,";
        }
        $ret .= "requestFingerprintOrder,secret";
        return $ret;
    }

    //--------------------------------------------------------------------------------//

    // Returns the value for the request parameter "requestFingerprint".
    private function getRequestFingerprint($theParams, $theSecret) {
        $ret = "";
        foreach ($theParams as $key=>$value) {
            $ret .= "$value";
        }
        $ret .= "$theSecret";
        return md5($ret);
    }

    private function getConfigData() {
        switch ($this->payment_method) {
            default;
                return Yii::$app->params['Wirecard'];
        }
    }

    private function getPaymentMethodDataELV()
    {
        return [
            'message'=>Yii::t('app','
<span class="ico-title">Um den Vorgang abschließen zu können überweise den Betrag {sum} EUR an folgende Bankverbindung:</span><br/>
<br/>
<span class="ico-payment-data">
<b>Kontoinhaber: JuglApp GmbH<br/>
IBAN: DE91 8607 0024 0199 9101 01<br/>
BIC: DEUTDEDBLEG</b><br/> 
</span>
<br/>
Damit Deine Bezahlung richtig zugeordnet wird, bitte als Verwendungszweck <b>UNBEDINGT</b> folgenden Code angeben:<br/> 
<br/>
<span class="ico-code"><b>{ext_code}</b></span>',[
                'ext_code'=>$this->ext_code,
                'sum'=>\app\components\Helper::formatPrice($this->currency_sum)
            ])
        ];
    }

    private function getPaymentMethodDataPayOne($retUrl,$clearingType) {

        $config=$this->getConfigData();

        $params=[
            'customerId'=>$config['customerId'],
            'shopId'=>$config['shopId'],
            'amount'=>intval($this->currency_sum*100)/100,
            'currency'=>'EUR',
            'orderDescription'=>$this->getPaymentDescription(),
            'customerStatement'=>$this->getPaymentDescription(),
            'orderReference'=>$this->id,
            'duplicateRequestCheck' => 'no',
            'successUrl' => $retUrl."success",
            'cancelUrl' => $retUrl."cancel",
            'failureUrl' => $retUrl."failure",
            'pendingUrl' => $retUrl."pending",
            'serviceUrl' => \yii\helpers\Url::to(['site/my','#'=>'/nutzungsbedingungen'],true),
            'confirmUrl' => \yii\helpers\Url::to(['api-funds-pay-in-data/confirm'],true),
            'language' => 'de',
            'displayText' => "Thank you very much",
            'imageUrl' => Yii::$app->request->hostInfo. "/static/images/site/logo.png",
            'cssUrl'=>'https://jugl.net/static/css/wirecard.css',
            'shopname_payin_id' => $this->id,
            'paymentType' => $clearingType
        ];

        $params['requestFingerprintOrder'] = $this->getRequestFingerprintOrder($params);
        $params['requestFingerprint'] = $this->getRequestFingerprint($params, $config["secret"]);

        //$params['cssUrl']='https://jugl.net/static/css/wirecard.css';//Yii::$app->request->hostInfo."/static/css/wirecard.css",

        $formParams=[];
        foreach($params as $k=>$v) {
            $formParams[]=[
                'name'=>$k,
                'value'=>$v
            ];
        }

        return [
            'formParams'=>$formParams,
        ];
    }

    public static function getPaymentMethodList() {
        $items = [
            static::PAYMENT_METHOD_PAYONE_GIROPAY=>Yii::t('app', 'Giropay'),
            static::PAYMENT_METHOD_PAYONE_CC=>Yii::t('app', 'Kreditkarte (Visa, Mastercard, American Express)'),
            static::PAYMENT_METHOD_PAYONE_SOFORT=>Yii::t('app', 'Sofortüberweisung'),
            static::PAYMENT_METHOD_PAYONE_PAYPAL=>Yii::t('app', 'PayPal'),
            static::PAYMENT_METHOD_ELV=>Yii::t('app', 'Banküberweisung'),
            static::PAYMENT_METHOD_JUGL=>Yii::t('app', 'Jugls')
        ];
        return $items;
    }

    public function paymentMethodLabel() {
        switch ($this->payment_method) {
            case static::PAYMENT_METHOD_PAYONE_GIROPAY:
                return Yii::t('app', 'Giropay');
            case static::PAYMENT_METHOD_PAYONE_CC:
                return Yii::t('app', 'Kreditkarte (Visa, Mastercard, American Express)');
            case static::PAYMENT_METHOD_PAYONE_SOFORT:
                return Yii::t('app', 'Sofortüberweisung');
            case static::PAYMENT_METHOD_PAYONE_PAYPAL:
                return Yii::t('app', 'PayPal');
            case static::PAYMENT_METHOD_ELV:
                return Yii::t('app', 'Banküberweisung');
            case static::PAYMENT_METHOD_JUGL:
                return Yii::t('app', 'Jugls');
        }
    }


    public function getPaymentMethodData($retUrl) {
        switch ($this->payment_method) {
            case static::PAYMENT_METHOD_PAYONE_CC:
                return $this->getPaymentMethodDataPayOne($retUrl,'CCARD');
            case static::PAYMENT_METHOD_PAYONE_GIROPAY:
                return $this->getPaymentMethodDataPayOne($retUrl,'GIROPAY');
            case static::PAYMENT_METHOD_PAYONE_PAYPAL:
                return $this->getPaymentMethodDataPayOne($retUrl,'PAYPAL');
            case static::PAYMENT_METHOD_PAYONE_SOFORT:
                return $this->getPaymentMethodDataPayOne($retUrl,'SOFORTUEBERWEISUNG');
            case static::PAYMENT_METHOD_ELV:
                return $this->getPaymentMethodDataElv();
            case static::PAYMENT_METHOD_JUGL:
                return $this->getPaymentMethodDataJugl();
        }
    }

    function areReturnParametersValid($theParams, $theSecret) {

        // gets the fingerprint-specific response parameters sent by Wirecard
        $responseFingerprintOrder = isset($theParams["responseFingerprintOrder"]) ? $theParams["responseFingerprintOrder"] : "";
        $responseFingerprint = isset($theParams["responseFingerprint"]) ? $theParams["responseFingerprint"] : "";

        // values of the response parameters for computing the fingerprint
        $fingerprintSeed = "";

        // array containing the names of the response parameters used by Wirecard to compute the response fingerprint
        $order = explode(",", $responseFingerprintOrder);

        // checks if there are required response parameters in responseFingerprintOrder
        if (in_array ("paymentState", $order) && in_array ("secret", $order) ) {
            // collects all values of response parameters used for computing the fingerprint
            for ($i = 0; $i < count($order); $i++) {
                $name = $order[$i];
                $value = isset($theParams[$name]) ? $theParams[$name] : "";
                $fingerprintSeed .= $value; // adds value of response parameter to fingerprint
                if (strcmp($name, "secret") == 0) {
                    $fingerprintSeed .= $theSecret; // adds your secret to fingerprint
                }
            }
            $fingerprint = md5($fingerprintSeed); // computes the fingerprint
            // checks if computed fingerprint and responseFingerprint have the same value
            if (strcmp($fingerprint, $responseFingerprint) == 0) {
                return true; // fingerprint check passed successfully
            }
        }
        return false;
    }

    private function addRequestParamsToDetails($theParams) {
        $this->details.="\n";
        foreach($theParams as $k=>$v) {
            $this->details.="$k: $v\n";
        }
    }

    public function confirmSuccess() {
        $this->confirm_status=static::CONFIRM_STATUS_SUCCESS;
        $this->save();

        if ($this->type==static::TYPE_PAY_IN) {
            $this->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_PAYIN, $this->jugl_sum, $this->user, Yii::t('app', 'Buyed {jugl_sum} jugls for {currency_sum}€', [
                'jugl_sum' => $this->jugl_sum,
                'currency_sum' => $this->currency_sum
            ]),true);

            $this->user->updateStatBuyedJugl($this->jugl_sum);

            Yii::$app->mailer->sendEmail($this->user,'payin-success');

        }

        if ($this->type==static::TYPE_PAY_IN_TOKEN) {
            $this->user->distributeTokenReferralPayment($this->jugl_sum, $this->user,
                \app\models\BalanceTokenLog::TYPE_IN,
                \app\models\BalanceTokenLog::TYPE_IN_REF,
                \app\models\BalanceTokenLog::TYPE_IN_REF_REF,
                Yii::t('app', 'Buyed {jugl_sum} Tokens for {currency_sum}€', [
                    'jugl_sum' => $this->jugl_sum,
                    'currency_sum' => $this->currency_sum
                ]), 0, '', '', '', true, false, true);
        }

        if ($this->type===static::TYPE_PAY_IN_TOKEN_DEPOSIT) {
            $model=\app\models\TokenDeposit::findOne(['pay_in_request_id'=>$this->id]);
            if ($model) {
                $model->status=\app\models\TokenDeposit::STATUS_ACTIVE;
                $model->save();
            }
        }

        if ($this->type==static::TYPE_PACKET) {
            if ($this->user->packet==\app\models\User::PACKET_STANDART) {
                \app\models\DailyStats::packetUpgrades();
            }

            $this->user->addVipPacket($this->packet_duration_months);
            $this->user->status=\app\models\User::STATUS_ACTIVE;
            $this->user->save();
        }

        if ($this->type==static::TYPE_PACKET_VIP_PLUS) {
            if (in_array($this->user->packet,[\app\models\User::PACKET_STANDART,\app\models\User::PACKET_VIP])) {
                \app\models\DailyStats::packetUpgrades();
            }

            $this->user->addVipPlusPacket($this->packet_duration_months);
            $this->user->status=\app\models\User::STATUS_ACTIVE;
            $this->user->save();
        }

        $this->sendInvoice();

        $this->user->updateChatContactsAfterRegistration();
    }

    public function confirm() {
        $trx=Yii::$app->db->beginTransaction();

        $config=$this->getConfigData();

        $theParams=Yii::$app->request->getBodyParams();
        $paymentState = isset($theParams["paymentState"]) ? $theParams["paymentState"] : "";
        switch ($paymentState) {
            case "FAILURE":
                $this->confirm_status=$paymentState;
                $this->details=$theParams['message'];
                $this->addRequestParamsToDetails($theParams);
                $this->save();
                break;
            case "CANCEL":
                $this->confirm_status="FAILURE";
                $this->details=$theParams['The checkout process has been cancelled by the user.'];
                $this->addRequestParamsToDetails($theParams);
                $this->save();
                break;
            case "PENDING":
                if ($this->areReturnParametersValid($theParams, $config['secret'])) {
                    $this->confirm_status="AWAITING";
                    $this->details= "The checkout process is pending and not yet finished.";
                    $this->addRequestParamsToDetails($theParams);
                    $this->save();
                }
                break;
            case "SUCCESS":
                //var_dump($theParams);var_dump($config);exit;
                if ($this->areReturnParametersValid($theParams, $config['secret']) && $this->confirm_status!=$paymentState) {
                    $this->confirm_status=$paymentState;
                    $this->details= "The checkout process has been successfully finished.";
                    $this->addRequestParamsToDetails($theParams);

                    $this->confirmSuccess();
                }

                break;
            default:
                break;
        }

        $trx->commit();
    }

    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','User ID'),
            'balance_log_id' => Yii::t('app','Balance Log ID'),
            'jugl_sum' => Yii::t('app','Jugl Sum'),
            'currency_sum' => Yii::t('app','Currency Sum'),
            'dt' => Yii::t('app','Dt'),
            'payment_method' => Yii::t('app','Payment Method'),
            'status' => Yii::t('app','Status'),
            'details' => Yii::t('app','Details'),
        ];
    }


    public function sendInvoice() {
        $tmpFile=tempnam(Yii::getAlias('@runtime/mpdf/temp/'), 'invoice');
        \app\components\DocumentGenerator::download('invoice', ['payInRequest'=>$this], $tmpFile);

        Yii::$app->mailer->sendEmail($this->user,'payin-invoice',[],[
            [
                'path'=>$tmpFile,
                'name'=>'Rechnung.pdf'
            ]
        ]);


    }

    public function generateExtId() {
        if (!$this->ext_code && $this->payment_method==static::PAYMENT_METHOD_ELV) {
            do {
                $symbols='23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
                $this->ext_code=date('Ymd-');
                for ($i=0;$i<6;$i++) {
                    $this->ext_code.=$symbols[rand(0,strlen($symbols)-1)];
                }
            } while (static::findOne(['ext_code'=>$this->ext_code]));
        }
    }

    public function beforeInsert() {
        if ($this->payment_method==static::PAYMENT_METHOD_ELV) {
            \app\models\UserEvent::addSystemMessage(Yii::$app->user->id,Yii::t('app','Bitte überweise den Betrag {sum} EUR mit dem Verwendungszweck <b>{ext_code}</b> an "<b>JuglApp GmbH / IBAN: DE91 8607 0024 0199 9101 01 / BIC: DEUTDEDBLEG</b>"',[
                'ext_code'=>$this->ext_code,
                'sum'=>\app\components\Helper::formatPrice($this->currency_sum)
            ]));

            if (in_array($this->type,[static::TYPE_PACKET,static::TYPE_PACKET_VIP_PLUS])) {
                Yii::$app->user->identity->not_force_packet_selection_till=
                    (new \app\components\EDateTime())->modify("+7 days")->sqlDateTime();
                Yii::$app->user->identity->save();
            }
        }
    }
}

\yii\base\Event::on(PayInRequest::className(), \yii\db\ActiveRecord::EVENT_BEFORE_INSERT, function ($event) {
    $event->sender->generateExtId();
    $event->sender->beforeInsert();
});
