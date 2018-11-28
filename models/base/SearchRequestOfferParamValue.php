<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "search_request_offer_param_value".
 *
 * @property integer $search_request_offer_id
 * @property integer $param_id
 * @property integer $match
 *
 * @property SearchRequestOffer $searchRequestOffer
 * @property Param $param
 */
class SearchRequestOfferParamValue extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_request_offer_param_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['search_request_offer_id', 'param_id', 'match'], 'required'],
            [['search_request_offer_id', 'param_id', 'match'], 'integer']
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestOffer()
    {
        return $this->hasOne('\app\models\SearchRequestOffer', ['id' => 'search_request_offer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParam()
    {
        return $this->hasOne('\app\models\Param', ['id' => 'param_id']);
    }
}
