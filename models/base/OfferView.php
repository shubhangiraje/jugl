<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "offer_view".
 *
 * @property integer $offer_id
 * @property integer $user_id
 * @property string $dt
 * @property string $code
 * @property string $got_view_bonus
 *
 * @property Offer $offer
 * @property User $user
 */
class OfferView extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_view';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['offer_id', 'user_id', 'dt', 'code'], 'required'],
            [['offer_id', 'user_id'], 'integer'],
            [['dt'], 'safe'],
            [['got_view_bonus'], 'number'],
            [['code'], 'string', 'max' => 40]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffer()
    {
        return $this->hasOne('\app\models\Offer', ['id' => 'offer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }
}
