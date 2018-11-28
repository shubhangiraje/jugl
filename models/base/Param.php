<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "param".
 *
 * @property integer $id
 * @property integer $interest_id
 * @property string $title
 * @property string $type
 * @property integer $required
 * @property integer $sort_order
 *
 * @property InterestParamValue[] $interestParamValues
 * @property Interest $interest
 * @property ParamValue[] $paramValues
 * @property SearchRequestParamValue[] $searchRequestParamValues
 */
class Param extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'param';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['interest_id', 'title', 'type', 'required'], 'required'],
            [['interest_id', 'required', 'sort_order'], 'integer'],
            [['type'], 'string'],
            [['title'], 'string', 'max' => 200]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInterestParamValues()
    {
        return $this->hasMany('\app\models\InterestParamValue', ['param_id' => 'id']);
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
    public function getParamValues()
    {
        return $this->hasMany('\app\models\ParamValue', ['param_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestParamValues()
    {
        return $this->hasMany('\app\models\SearchRequestParamValue', ['param_id' => 'id']);
    }
}
