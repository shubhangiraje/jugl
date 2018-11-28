<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "offer_interest".
 *
 * @property integer $id
 * @property integer $offer_id
 * @property integer $level1_interest_id
 * @property integer $level2_interest_id
 * @property integer $level3_interest_id
 *
 * @property Interest $level1Interest
 * @property Interest $level2Interest
 * @property Interest $level3Interest
 * @property Offer $offer
 */
class OfferInterest extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_interest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['offer_id', 'level1_interest_id'], 'required'],
            [['offer_id', 'level1_interest_id', 'level2_interest_id', 'level3_interest_id'], 'integer']
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
    public function getOffer()
    {
        return $this->hasOne('\app\models\Offer', ['id' => 'offer_id']);
    }
}
