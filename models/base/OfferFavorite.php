<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "offer_favorite".
 *
 * @property integer $user_id
 * @property integer $offer_id
 *
 * @property User $user
 * @property Offer $offer
 */
class OfferFavorite extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_favorite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'offer_id'], 'required'],
            [['user_id', 'offer_id'], 'integer']
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
    public function getOffer()
    {
        return $this->hasOne('\app\models\Offer', ['id' => 'offer_id']);
    }
}
