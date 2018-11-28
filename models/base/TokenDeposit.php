<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "token_deposit".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $sum
 * @property integer $period_months
 * @property string $contribution_percentage
 * @property string $created_at
 * @property string $completion_dt
 * @property integer $token_deposit_guarantee_id
 * @property string $status
 * @property integer $pay_in_request_id
 * @property string $buy_sum
 * @property string $buy_currency
 * @property string $percents_payed_sum
 * @property string $last_percents_payout_dt
 * @property integer $payout_pay_out_request_id
 * @property integer $payout_balance_log_id
 *
 * @property User $user
 * @property TokenDepositGuarantee $tokenDepositGuarantee
 * @property PayInRequest $payInRequest
 * @property PayOutRequest $payoutPayOutRequest
 * @property BalanceLog $payoutBalanceLog
 */
class TokenDeposit extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'token_deposit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'sum', 'period_months', 'contribution_percentage', 'status', 'buy_sum', 'buy_currency'], 'required'],
            [['user_id', 'period_months', 'token_deposit_guarantee_id', 'pay_in_request_id', 'payout_pay_out_request_id', 'payout_balance_log_id'], 'integer'],
            [['sum', 'contribution_percentage', 'buy_sum', 'percents_payed_sum'], 'number'],
            [['created_at', 'completion_dt', 'last_percents_payout_dt'], 'safe'],
            [['status', 'buy_currency'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['token_deposit_guarantee_id'], 'exist', 'skipOnError' => true, 'targetClass' => TokenDepositGuarantee::className(), 'targetAttribute' => ['token_deposit_guarantee_id' => 'id']],
            [['pay_in_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => PayInRequest::className(), 'targetAttribute' => ['pay_in_request_id' => 'id']],
            [['payout_pay_out_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => PayOutRequest::className(), 'targetAttribute' => ['payout_pay_out_request_id' => 'id']],
            [['payout_balance_log_id'], 'exist', 'skipOnError' => true, 'targetClass' => BalanceLog::className(), 'targetAttribute' => ['payout_balance_log_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokenDepositGuarantee()
    {
        return $this->hasOne('\app\models\TokenDepositGuarantee', ['id' => 'token_deposit_guarantee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayInRequest()
    {
        return $this->hasOne('\app\models\PayInRequest', ['id' => 'pay_in_request_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayoutPayOutRequest()
    {
        return $this->hasOne('\app\models\PayOutRequest', ['id' => 'payout_pay_out_request_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayoutBalanceLog()
    {
        return $this->hasOne('\app\models\BalanceLog', ['id' => 'payout_balance_log_id']);
    }
}
