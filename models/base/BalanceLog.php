<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "balance_log".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $sum
 * @property string $dt
 * @property integer $initiator_user_id
 * @property string $comment
 *
 * @property User $user
 * @property User $initiatorUser
 * @property BalanceLogMod[] $balanceLogMods
 * @property PayInRequest[] $payInRequests
 * @property PayOutRequest[] $payOutRequests
 */
class BalanceLog extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'balance_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'sum', 'initiator_user_id'], 'required'],
            [['user_id', 'initiator_user_id'], 'integer'],
            [['type'], 'string'],
            [['sum'], 'number'],
            [['dt'], 'safe'],
            [['comment'], 'string', 'max' => 512]
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
    public function getInitiatorUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'initiator_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalanceLogMods()
    {
        return $this->hasMany('\app\models\BalanceLogMod', ['balance_log_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayInRequests()
    {
        return $this->hasMany('\app\models\PayInRequest', ['balance_log_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayOutRequests()
    {
        return $this->hasMany('\app\models\PayOutRequest', ['balance_log_id' => 'id']);
    }
}
