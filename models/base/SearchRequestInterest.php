<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "search_request_interest".
 *
 * @property integer $id
 * @property integer $search_request_id
 * @property integer $level1_interest_id
 * @property integer $level2_interest_id
 * @property integer $level3_interest_id
 *
 * @property Interest $level1Interest
 * @property Interest $level2Interest
 * @property Interest $level3Interest
 * @property SearchRequest $searchRequest
 */
class SearchRequestInterest extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_request_interest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['search_request_id', 'level1_interest_id'], 'required'],
            [['search_request_id', 'level1_interest_id', 'level2_interest_id', 'level3_interest_id'], 'integer']
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLevel1Interest()
    {
        return $this->hasOne('\app\models\Interest', ['id' => 'level1_interest_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLevel2Interest()
    {
        return $this->hasOne('\app\models\Interest', ['id' => 'level2_interest_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLevel3Interest()
    {
        return $this->hasOne('\app\models\Interest', ['id' => 'level3_interest_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequest()
    {
        return $this->hasOne('\app\models\SearchRequest', ['id' => 'search_request_id']);
    }
}
