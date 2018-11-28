<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_bank_data".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $bic
 * @property string $iban
 * @property string $owner
 * @property integer $sort_order
 *
 * @property User $user
 */
class UserBankData extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_bank_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'sort_order'], 'integer'],
            [['bic', 'iban', 'owner'], 'string', 'max' => 256]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }
}
