<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "search_request_favorite".
 *
 * @property integer $user_id
 * @property integer $search_request_id
 *
 * @property User $user
 * @property SearchRequest $searchRequest
 */
class SearchRequestFavorite extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_request_favorite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'search_request_id'], 'required'],
            [['user_id', 'search_request_id'], 'integer']
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequest()
    {
        return $this->hasOne('\app\models\SearchRequest', ['id' => 'search_request_id']);
    }
}
