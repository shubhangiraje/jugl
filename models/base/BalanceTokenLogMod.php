<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "balance_token_log_mod".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property integer $balance_token_log_id
 * @property string $comments
 *
 * @property Admin $admin
 * @property BalanceTokenLog $balanceTokenLog
 */
class BalanceTokenLogMod extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'balance_token_log_mod';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'balance_token_log_id', 'comments'], 'required'],
            [['admin_id', 'balance_token_log_id'], 'integer'],
            [['comments'], 'string'],
            [['admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => Admin::className(), 'targetAttribute' => ['admin_id' => 'id']],
            [['balance_token_log_id'], 'exist', 'skipOnError' => true, 'targetClass' => BalanceTokenLog::className(), 'targetAttribute' => ['balance_token_log_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne('\app\models\Admin', ['id' => 'admin_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBalanceTokenLog()
    {
        return $this->hasOne('\app\models\BalanceTokenLog', ['id' => 'balance_token_log_id']);
    }
}
