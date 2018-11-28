<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "offer_request".
 *
 * @property integer $id
 * @property integer $offer_id
 * @property integer $user_id
 * @property string $description
 * @property string $status
 * @property string $bet_price
 * @property string $bet_dt
 * @property string $bet_period
 * @property string $bet_active_till
 * @property string $closed_dt
 * @property integer $pay_tx_id
 * @property string $pay_status
 * @property string $pay_method
 * @property integer $no_payment_buyer_notified
 * @property integer $no_payment_seller_notified
 * @property integer $payment_complaint
 * @property string $pay_data
 * @property string $delivery_address
 * @property integer $user_feedback_id
 * @property integer $counter_user_feedback_id
 * @property integer $modifications
 *
 * @property Offer[] $offers
 * @property UserFeedback $counterUserFeedback
 * @property Offer $offer
 * @property User $user
 * @property UserFeedback $userFeedback
 * @property OfferRequestModification[] $offerRequestModifications
 */
class OfferRequest extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['offer_id', 'user_id'], 'required'],
            [['offer_id', 'user_id', 'pay_tx_id', 'no_payment_buyer_notified', 'no_payment_seller_notified', 'payment_complaint', 'user_feedback_id', 'counter_user_feedback_id', 'modifications'], 'integer'],
            [['description', 'status', 'pay_status', 'pay_method'], 'string'],
            [['bet_price'], 'number'],
            [['bet_dt', 'bet_active_till', 'closed_dt'], 'safe'],
            [['bet_period'], 'string', 'max' => 32],
            [['pay_data', 'delivery_address'], 'string', 'max' => 512],
            [['counter_user_feedback_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserFeedback::className(), 'targetAttribute' => ['counter_user_feedback_id' => 'id']],
            [['offer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Offer::className(), 'targetAttribute' => ['offer_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['user_feedback_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserFeedback::className(), 'targetAttribute' => ['user_feedback_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffers()
    {
        return $this->hasMany('\app\models\Offer', ['accepted_offer_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCounterUserFeedback()
    {
        return $this->hasOne('\app\models\UserFeedback', ['id' => 'counter_user_feedback_id']);
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFeedback()
    {
        return $this->hasOne('\app\models\UserFeedback', ['id' => 'user_feedback_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferRequestModifications()
    {
        return $this->hasMany('\app\models\OfferRequestModification', ['offer_request_id' => 'id']);
    }
}
