<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "offer_request_modification".
 *
 * @property integer $id
 * @property integer $offer_request_id
 * @property string $dt
 * @property string $price
 *
 * @property OfferRequest $offerRequest
 */
class OfferRequestModification extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_request_modification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['offer_request_id', 'price'], 'required'],
            [['offer_request_id'], 'integer'],
            [['dt'], 'safe'],
            [['price'], 'number']
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferRequest()
    {
        return $this->hasOne('\app\models\OfferRequest', ['id' => 'offer_request_id']);
    }
}
