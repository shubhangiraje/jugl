<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "offer_param_value".
 *
 * @property integer $id
 * @property integer $offer_id
 * @property integer $param_id
 * @property integer $param_value_id
 * @property string $param_value
 *
 * @property Param $param
 * @property ParamValue $paramValue
 * @property Offer $offer
 */
class OfferParamValue extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_param_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['offer_id', 'param_id'], 'required'],
            [['offer_id', 'param_id', 'param_value_id'], 'integer'],
            [['param_value'], 'string', 'max' => 128]
        ];
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffer()
    {
        return $this->hasOne('\app\models\Offer', ['id' => 'offer_id']);
    }
}
