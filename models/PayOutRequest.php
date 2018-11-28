<?php

namespace app\models;

use app\components\EDateTime;
use Yii;
use \app\models\BalanceLog;

class PayOutRequest extends \app\models\base\PayOutRequest
{
    const STATUS_NEW='NEW';
    const STATUS_ACCEPTED='ACCEPTED';
    const STATUS_DECLINED='DECLINED';
    const STATUS_PROCESSED='PROCESSED';

    const PAYMENT_METHOD_ELV='ELV';
    const PAYMENT_METHOD_PAYPAL='PAYPAL';

    const TYPE_JUGLS='JUGLS';
    const TYPE_TOKEN_DEPOSIT='TOKEN_DEPOSIT';
    const TYPE_TOKEN_DEPOSIT_PERCENT='TOKEN_DEPOSIT_PERCENT';

    public static function getTypeList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::TYPE_JUGLS=>Yii::t('app','PAYOUT_REQUEST_TYPE_JUGLS'),
                static::TYPE_TOKEN_DEPOSIT=>Yii::t('app','PAYOUT_REQUEST_TYPE_TOKEN_DEPOSIT'),
                static::TYPE_TOKEN_DEPOSIT_PERCENT=>Yii::t('app','PAYOUT_REQUEST_TYPE_TOKEN_DEPOSIT_PERCENT')
            ];
        }

        return $items;
    }

    public static function getStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::STATUS_NEW=>Yii::t('app','PAYOUT_REQUEST_STATUS_NEW'),
                static::STATUS_ACCEPTED=>Yii::t('app','PAYOUT_REQUEST_STATUS_ACCEPTED'),
                static::STATUS_DECLINED=>Yii::t('app','PAYOUT_REQUEST_STATUS_DECLINED'),
                static::STATUS_PROCESSED=>Yii::t('app','PAYOUT_REQUEST_STATUS_PROCESSED'),
            ];
        }

        return $items;
    }

    public function getTypeLabel() {
        return static::getTypeList()[$this->type];
    }

    public function getStatusLabel() {
        return static::getStatusList()[$this->status];
    }

    public static function getPaymentMethodList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::PAYMENT_METHOD_ELV=>Yii::t('app','ELV'),
                static::PAYMENT_METHOD_PAYPAL=>Yii::t('app','PayPal'),
            ];
        }

        return $items;
    }

    public function getPaymentMethodLabel() {
        return static::getPaymentMethodList()[$this->payment_method];
    }

    public function setDetailsData($data) {
        $this->details=json_encode($data,JSON_UNESCAPED_UNICODE);
    }

    public function getDetailsData() {
        $data=json_decode($this->details,true);
        if ($data===null) {
            $data=[];
        }

        return $data;
    }

    public function getPayOutDataModel() {
        switch ($this->payment_method) {
            case static::PAYMENT_METHOD_ELV:
                $model=new PayOutELVForm;
                break;
            case static::PAYMENT_METHOD_PAYPAL:
                $model=new PayOutPayPalForm;
                break;
            default:
                return;
        }

        $model->attributes=json_decode($this->details,true);

        return $model;
    }

    public function accept() {
        if ($this->status==static::STATUS_NEW && $this->user->balance>=$this->currency_sum) {
            $trx=Yii::$app->db->beginTransaction();

            $this->status=static::STATUS_ACCEPTED;
            $this->save();

            switch ($this->type) {
                case static::TYPE_JUGLS:
                    $this->user->addBalanceLogItem(BalanceLog::TYPE_PAYOUT,-$this->jugl_sum,$this->user,Yii::t('app','Auszahlung Jugls'),false,true);
                    break;
                case static::TYPE_TOKEN_DEPOSIT;
                    break;
                case static::TYPE_TOKEN_DEPOSIT_PERCENT:
                    $this->user->addBalanceLogItem(BalanceLog::TYPE_PAYOUT,-$this->jugl_sum,$this->user,Yii::t('app','Auszahlung des Zinsertrags'),false,false,false,true);
                    break;
            }


            $trx->commit();
            Yii::$app->mailer->sendEmail($this->user, 'payout-accepted', ['model' => $this]);
        }
    }

    public function decline() {
        if ($this->status==static::STATUS_NEW) {
            $this->status = static::STATUS_DECLINED;
            $this->save();

            Yii::$app->mailer->sendEmail($this->user, 'payout-declined', ['model' => $this]);
        }
    }

    public function process() {
        if ($this->status==static::STATUS_ACCEPTED) {
            $this->status = static::STATUS_PROCESSED;
            $now=new EDateTime();
            $this->dt_processed=$now->sqlDateTime();
            $this->save();

            Yii::$app->mailer->sendEmail($this->user, 'payout-processed', ['model' => $this]);
        }
    }

    public function getDefinedId() {
        $prefix=[
            static::PAYMENT_METHOD_PAYPAL=>'PP',
            static::PAYMENT_METHOD_ELV=>'BA'
        ];

        return sprintf("%s-%08d",$prefix[$this->payment_method],$this->pay_out_method_num);
    }

    public function generatePayoutNum() {
        $num=Yii::$app->db->createCommand("select pay_out_method_num from pay_out_request where payment_method=:payment_method order by id desc",[':payment_method'=>$this->payment_method])->queryScalar();
        if ($num>0) {
            $num++;
        } else {
            $num=1;
        }

        $this->pay_out_method_num=$num;
    }

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
            'pay_out_method_num' => Yii::t('app','ID'),
            'status' => Yii::t('app','Status'),
            'details' => Yii::t('app','Details')
        ];
    }
}
