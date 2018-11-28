<?php

namespace app\models;

use Yii;

class TokenDepositGuaranteeFile extends \app\models\base\TokenDepositGuaranteeFile
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'token_deposit_guarantee_id' => Yii::t('app','Token Deposit Guarantee ID'),
            'file_id' => Yii::t('app','Image'),
            'sort_order' => Yii::t('app','Sort Order'),
        ];
    }

    public function rules()
    {
        return [
            [['token_deposit_guarantee_id'], 'required'],
            [['file_id'], 'safe'],
            [['token_deposit_guarantee_id', 'file_id', 'sort_order'], 'integer'],
            [['token_deposit_guarantee_id'], 'exist', 'skipOnError' => true, 'targetClass' => TokenDepositGuarantee::className(), 'targetAttribute' => ['token_deposit_guarantee_id' => 'id']],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['file_id' => 'id']]
        ];
    }
}
