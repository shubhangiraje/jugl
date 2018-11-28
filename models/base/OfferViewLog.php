<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "offer_view_log".
 *
 * @property integer $id
 * @property string $create_dt
 * @property integer $offer_id
 * @property integer $user_id
 * @property integer $duration
 *
 * @property Offer $offer
 * @property User $user
 */
class OfferViewLog extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_view_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_dt'], 'safe'],
            [['offer_id', 'user_id', 'duration'], 'integer'],
            [['offer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Offer::className(), 'targetAttribute' => ['offer_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']]
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
