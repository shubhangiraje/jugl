<?php

namespace app\models;

use Yii;

class SearchRequestParamValue extends \app\models\base\SearchRequestParamValue
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'search_request_id' => Yii::t('app','Search Request ID'),
            'param_id' => Yii::t('app','Param ID'),
            'param_value_id' => Yii::t('app','Param Value ID'),
            'param_value' => Yii::t('app','Param Value'),
        ];
    }
}
