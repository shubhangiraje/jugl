<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "pay_out_request".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property integer $balance_log_id
 * @property string $jugl_sum
 * @property string $currency_sum
 * @property string $dt
 * @property string $payment_method
 * @property string $status
 * @property integer $pay_out_method_num
 * @property string $details
 * @property string $dt_processed
 *
 * @property BalanceLog $balanceLog
 * @property User $user
 * @property TokenDeposit[] $tokenDeposits
 */
class PayOutRequest extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay_out_request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'payment_method', 'status', 'pay_out_method_num'], 'required'],
            [['user_id', 'balance_log_id', 'pay_out_method_num'], 'integer'],
            [['type', 'payment_method', 'status', 'details'], 'string'],
            [['jugl_sum', 'currency_sum'], 'number'],
            [['dt', 'dt_processed'], 'safe'],
            [['balance_log_id'], 'exist', 'skipOnError' => true, 'targetClass' => BalanceLog::className(), 'targetAttribute' => ['balance_log_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalanceLog()
    {
        return $this->hasOne('\app\models\BalanceLog', ['id' => 'balance_log_id']);
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
    public function getTokenDeposits()
    {
        return $this->hasMany('\app\models\TokenDeposit', ['payout_pay_out_request_id' => 'id']);
    }
}
