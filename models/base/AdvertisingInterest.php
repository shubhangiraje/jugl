<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "advertising_interest".
 *
 * @property integer $id
 * @property integer $advertising_id
 * @property integer $level1_interest_id
 * @property integer $level2_interest_id
 * @property integer $level3_interest_id
 *
 * @property Interest $level1Interest
 * @property Interest $level2Interest
 * @property Interest $level3Interest
 * @property SearchRequest $searchRequest
 */
class AdvertisingInterest extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'advertising_interest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advertising_id', 'level1_interest_id'], 'required'],
            [['advertising_id', 'level1_interest_id', 'level2_interest_id', 'level3_interest_id'], 'integer']
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
    public function getAdvertising()
    {
        return $this->hasOne('\app\models\Advertising', ['id' => 'advertising_id']);
    }
}
