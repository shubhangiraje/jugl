<?php

namespace app\models;

use Yii;

class SearchRequestFile extends \app\models\base\SearchRequestFile
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'search_request_id' => Yii::t('app','Search Request ID'),
            'file_id' => Yii::t('app','Abbildung'),
            'sort_order' => Yii::t('app','Sort Order'),
        ];
    }

    public function rules()
    {
        return [
            [['search_request_id'], 'required'],
            ['file_id','safe'],
            [['search_request_id', 'file_id', 'sort_order'], 'integer']
        ];
    }
}
