<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "search_request_param_value".
 *
 * @property integer $id
 * @property integer $search_request_id
 * @property integer $param_id
 * @property integer $param_value_id
 * @property string $param_value
 *
 * @property Param $param
 * @property ParamValue $paramValue
 * @property SearchRequest $searchRequest
 */
class SearchRequestParamValue extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_request_param_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['search_request_id', 'param_id'], 'required'],
            [['search_request_id', 'param_id', 'param_value_id'], 'integer'],
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
    public function getSearchRequest()
    {
        return $this->hasOne('\app\models\SearchRequest', ['id' => 'search_request_id']);
    }
}
