<?php

namespace app\models;

use app\components\EDateTime;
use Yii;

class TokenDeposit extends \app\models\base\TokenDeposit
{
    const PAYOUT_TYPE_JUGLS='JUGLS';
    const PAYOUT_TYPE_TOKENS='TOKENS';

    const STATUS_AWAITING_PAYMENT='AWAITING_PAYMENT';
    const STATUS_ACTIVE='ACTIVE';
    const STATUS_COMPLETED='COMPLETED';

    const BUY_CURRENCY_JUGLS='JUGLS';
    const BUY_CURRENCY_EUR='EUR';

    public static function getPeriodList() {
        return [
            '12'=>Yii::t('app','1 Jahr'),
            '24'=>Yii::t('app','2 Jahre'),
            '36'=>Yii::t('app','3 Jahre'),
        ];
    }

    public static function getStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                //static::STATUS_AWAITING_PAYMENT=>Yii::t('app','TOKEN_DEPOSIT_AWAITING_PAYMENT'),
                static::STATUS_ACTIVE=>Yii::t('app','TOKEN_DEPOSIT_STATUS_ACTIVE'),
                static::STATUS_COMPLETED=>Yii::t('app','TOKEN_DEPOSIT_STATUS_COMPLETED'),
            ];
        }

        return $items;
    }

    public function getStatusLabel() {
        return static::getStatusList()[$this->status];
    }

    public function getPercentSum() {
        $total=(new EDateTime($this->completion_dt))->getTimestamp()-(new EDateTime($this->created_at))->getTimestamp();
        $completed=(new EDateTime())->getTimestamp()-(new EDateTime($this->created_at))->getTimestamp();
        return $this->sum*$this->contribution_percentage/100*$completed/$total;
        //var_dump($total);var_dump($completed);exit;
        //return 1.23;
    }

    public function beforeUpdate() {
        if ($this->oldAttributes['status']!=static::STATUS_ACTIVE && $this->status==static::STATUS_ACTIVE) {
            if ($this->tokenDepositGuarantee) {
                $this->tokenDepositGuarantee->updateCounters(['sum'=>$this->sum]);
            }
            $now=new \app\components\EDateTime();
            $this->created_at=$now->sqlDateTime();
            $this->last_percents_payout_dt=$this->created_at;
            $now->modify("+".$this->period_months." months");
            $this->completion_dt=$now->sqlDateTime();
        }

        if ($this->oldAttributes['status']==static::STATUS_ACTIVE && $this->status!=static::STATUS_ACTIVE) {
            if ($this->tokenDepositGuarantee) {
                $this->tokenDepositGuarantee->updateCounters(['sum' => -$this->sum]);
            }
        }
    }

    public static function doPayouts() {
        $maxLastPayoutDt=new \app\components\EDateTime();
        $maxLastPayoutDt->modify('-1 day');
        $query=static::find()->andWhere('last_percents_payout_dt<:max_last_payout_dt and last_percents_payout_dt<completion_dt',[
            ':max_last_payout_dt'=>$maxLastPayoutDt->sqlDateTime()
        ]);
        foreach($query->each(100) as $model) {
            $trx=Yii::$app->db->beginTransaction();
            $model->lockForUpdate();
            $model->percentsPayout();
            $trx->commit();
        }
    }

    private function percentsPayout() {
        $days=floor(((new \app\components\EDateTime($this->completion_dt))->getTimestamp()-(new \app\components\EDateTime($this->created_at))->getTimestamp())/3600/24+0.5);
        $dailySum=$this->contribution_percentage/100/$days*$this->sum*\app\models\Setting::TOKEN_DEPOSIT_TOKEN_TO_JUGL_EXCHANGE_RATE;
        $this->percents_payed_sum+=$dailySum;
        $this->last_percents_payout_dt=(new \app\components\EDateTime($this->last_percents_payout_dt))->modify('+1 day')->sqlDateTime();

        $this->user->distributeReferralPayment(
            $dailySum,
            $this->user,
            \app\models\BalanceLog::TYPE_IN,
            \app\models\BalanceLog::TYPE_IN_REF,
            \app\models\BalanceLog::TYPE_IN_REF_REF,
            Yii::t('app','Zinsertrag aus den festgelegten Tokens {sum} für {date}',[
                'sum'=>\app\components\Helper::formatPrice($this->sum),
                'date'=>(new \app\components\EDateTime($this->last_percents_payout_dt))->format('d.m.Y')
            ]), 0, '', '', '', true, false, true
        );

        $this->save();
    }

    public function payoutInJugls() {
        $this->status=static::STATUS_COMPLETED;

        $sum=$this->sum*\app\models\Setting::TOKEN_DEPOSIT_TOKEN_TO_JUGL_EXCHANGE_RATE;

        $this->payout_balance_log_id=$this->user->addBalanceLogItem(
            \app\models\BalanceTokenLog::TYPE_PAYIN,
            $sum,
            $this->user,
            Yii::t('app','Auszahlung der festgelegten Tokens'),true)->id;

        $this->save();
    }


    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','User ID'),
            'sum' => Yii::t('app','Sum'),
            'period_months' => Yii::t('app','Period Months'),
            'contribution_percentage' => Yii::t('app','Contribution Percentage'),
            'created_at' => Yii::t('app','Created At'),
            'completion_dt' => Yii::t('app','Completion Dt'),
            'token_deposit_guarantee_id' => Yii::t('app','Token Deposit Guarantee ID'),
            'status' => Yii::t('app','Status'),
        ];
    }

    public static function getLogTokenDeposit() {
        $tokenDeposits=TokenDeposit::find()->andWhere(['user_id'=>Yii::$app->user->id, 'status'=>TokenDeposit::STATUS_ACTIVE])->orderBy('id desc')->all();
        $data=[];
        foreach($tokenDeposits as $tokenDeposit) {
            $data[]=$tokenDeposit->toArray();
        }
        return $data;
    }



}
