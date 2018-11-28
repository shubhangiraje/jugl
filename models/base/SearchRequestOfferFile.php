<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "search_request_offer_file".
 *
 * @property integer $search_request_offer_id
 * @property integer $file_id
 * @property integer $sort_order
 *
 * @property SearchRequestOffer $searchRequestOffer
 * @property File $file
 */
class SearchRequestOfferFile extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_request_offer_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['search_request_offer_id', 'file_id'], 'required'],
            [['search_request_offer_id', 'file_id', 'sort_order'], 'integer']
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
    public function getFile()
    {
        return $this->hasOne('\app\models\File', ['id' => 'file_id']);
    }
}
