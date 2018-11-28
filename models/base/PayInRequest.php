<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "pay_in_request".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $balance_log_id
 * @property string $jugl_sum
 * @property string $currency_sum
 * @property string $dt
 * @property string $payment_method
 * @property string $return_status
 * @property string $confirm_status
 * @property string $details
 *
 * @property BalanceLog $balanceLog
 * @property User $user
 */
class PayInRequest extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay_in_request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'payment_method', 'return_status', 'confirm_status'], 'required'],
            [['user_id', 'balance_log_id'], 'integer'],
            [['jugl_sum', 'currency_sum'], 'number'],
            [['dt'], 'safe'],
            [['payment_method', 'return_status', 'confirm_status', 'details'], 'string']
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
}
