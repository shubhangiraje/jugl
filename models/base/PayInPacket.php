<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "pay_in_packet".
 *
 * @property integer $id
 * @property string $jugl_sum
 * @property string $currency_sum
 */
class PayInPacket extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay_in_packet';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jugl_sum', 'currency_sum'], 'required'],
            [['jugl_sum', 'currency_sum'], 'number']
        ];
    }

}
