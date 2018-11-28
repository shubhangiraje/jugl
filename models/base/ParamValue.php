<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "param_value".
 *
 * @property integer $id
 * @property integer $param_id
 * @property string $title
 * @property integer $sort_order
 *
 * @property InterestParamValue[] $interestParamValues
 * @property Param $param
 * @property SearchRequestParamValue[] $searchRequestParamValues
 */
class ParamValue extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'param_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['param_id', 'title'], 'required'],
            [['param_id', 'sort_order'], 'integer'],
            [['title'], 'string', 'max' => 200]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInterestParamValues()
    {
        return $this->hasMany('\app\models\InterestParamValue', ['param_value_id' => 'id']);
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
    public function getSearchRequestParamValues()
    {
        return $this->hasMany('\app\models\SearchRequestParamValue', ['param_value_id' => 'id']);
    }
}
