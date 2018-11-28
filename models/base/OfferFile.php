<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "offer_file".
 *
 * @property integer $offer_id
 * @property integer $file_id
 * @property integer $sort_order
 *
 * @property File $file
 * @property Offer $offer
 */
class OfferFile extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['offer_id', 'file_id'], 'required'],
            [['offer_id', 'file_id', 'sort_order'], 'integer']
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
    public function getOffer()
    {
        return $this->hasOne('\app\models\Offer', ['id' => 'offer_id']);
    }
}
