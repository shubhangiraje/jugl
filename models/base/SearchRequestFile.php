<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "search_request_file".
 *
 * @property integer $search_request_id
 * @property integer $file_id
 * @property integer $sort_order
 *
 * @property File $file
 * @property SearchRequest $searchRequest
 */
class SearchRequestFile extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_request_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['search_request_id', 'file_id'], 'required'],
            [['search_request_id', 'file_id', 'sort_order'], 'integer']
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne('\app\models\File', ['id' => 'file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequest()
    {
        return $this->hasOne('\app\models\SearchRequest', ['id' => 'search_request_id']);
    }
}
