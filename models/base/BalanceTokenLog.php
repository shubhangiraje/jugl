<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "balance_token_log".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $sum
 * @property string $sum_earned
 * @property string $sum_buyed
 * @property string $dt
 * @property integer $initiator_user_id
 * @property string $comment
 *
 * @property User $user
 * @property User $initiatorUser
 * @property BalanceTokenLogMod[] $balanceTokenLogMods
 */
class BalanceTokenLog extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'balance_token_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'sum','sum_earned', 'sum_buyed', 'initiator_user_id'], 'required'],
            [['user_id', 'initiator_user_id'], 'integer'],
            [['type'], 'string'],
            [['sum', 'sum_earned', 'sum_buyed'], 'number'],
            [['dt'], 'safe'],
            [['comment'], 'string', 'max' => 512],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['initiator_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['initiator_user_id' => 'id']]
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
    public function getBalanceTokenLogMods()
    {
        return $this->hasMany('\app\models\BalanceTokenLogMod', ['balance_token_log_id' => 'id']);
    }
}
