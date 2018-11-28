<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "token_deposit_guarantee".
 *
 * @property integer $id
 * @property string $title_de
 * @property string $title_en
 * @property string $title_ru
 * @property string $description_de
 * @property string $description_en
 * @property string $description_ru
 * @property string $sum
 * @property string $sum_cost
 * @property string $status
 * @property integer $show
 *
 * @property TokenDeposit[] $tokenDeposits
 * @property TokenDepositGuaranteeFile[] $tokenDepositGuaranteeFiles
 * @property File[] $files
 */
class TokenDepositGuarantee extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'token_deposit_guarantee';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title_de', 'description_de', 'sum'], 'required'],
            [['description_de', 'description_en', 'description_ru', 'status'], 'string'],
            [['sum', 'sum_cost'], 'number'],
            [['show'], 'integer'],
            [['title_de', 'title_en', 'title_ru'], 'string', 'max' => 256]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokenDeposits()
    {
        return $this->hasMany('\app\models\TokenDeposit', ['token_deposit_guarantee_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokenDepositGuaranteeFiles()
    {
        return $this->hasMany('\app\models\TokenDepositGuaranteeFile', ['token_deposit_guarantee_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany('\app\models\File', ['id' => 'file_id'])->viaTable('token_deposit_guarantee_file', ['token_deposit_guarantee_id' => 'id']);
    }
}
