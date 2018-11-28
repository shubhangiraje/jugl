<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "balance_log_mod".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property integer $balance_log_id
 * @property string $comments
 *
 * @property Admin $admin
 * @property BalanceLog $balanceLog
 */
class BalanceLogMod extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'balance_log_mod';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'balance_log_id', 'comments'], 'required'],
            [['admin_id', 'balance_log_id'], 'integer'],
            [['comments'], 'string']
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
    public function getBalanceLog()
    {
        return $this->hasOne('\app\models\BalanceLog', ['id' => 'balance_log_id']);
    }
}
