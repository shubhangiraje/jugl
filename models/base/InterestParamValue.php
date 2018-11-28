<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "interest_param_value".
 *
 * @property integer $id
 * @property integer $interest_id
 * @property integer $param_id
 * @property integer $param_value_id
 *
 * @property Interest $interest
 * @property Param $param
 * @property ParamValue $paramValue
 */
class InterestParamValue extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'interest_param_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['interest_id', 'param_id', 'param_value_id'], 'required'],
            [['interest_id', 'param_id', 'param_value_id'], 'integer']
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInterest()
    {
        return $this->hasOne('\app\models\Interest', ['id' => 'interest_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParam()
    {
        return $this->hasOne('\app\models\Param', ['id' => 'param_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParamValue()
    {
        return $this->hasOne('\app\models\ParamValue', ['id' => 'param_value_id']);
    }
}
