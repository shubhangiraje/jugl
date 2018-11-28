<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "token_deposit_guarantee_file".
 *
 * @property integer $token_deposit_guarantee_id
 * @property integer $file_id
 * @property integer $sort_order
 *
 * @property TokenDepositGuarantee $tokenDepositGuarantee
 * @property File $file
 */
class TokenDepositGuaranteeFile extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'token_deposit_guarantee_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['token_deposit_guarantee_id', 'file_id'], 'required'],
            [['token_deposit_guarantee_id', 'file_id', 'sort_order'], 'integer'],
            [['token_deposit_guarantee_id'], 'exist', 'skipOnError' => true, 'targetClass' => TokenDepositGuarantee::className(), 'targetAttribute' => ['token_deposit_guarantee_id' => 'id']],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['file_id' => 'id']]
        ];
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
    public function getFile()
    {
        return $this->hasOne('\app\models\File', ['id' => 'file_id']);
    }
}
