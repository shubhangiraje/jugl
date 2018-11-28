<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "registration_code_packet".
 *
 * @property integer $id
 * @property integer $registration_codes_count
 * @property string $sum
 */
class RegistrationCodePacket extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'registration_code_packet';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['registration_codes_count', 'sum'], 'required'],
            [['registration_codes_count'], 'integer'],
            [['sum'], 'number']
        ];
    }

}
