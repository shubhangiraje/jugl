<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_feedback".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $second_user_id
 * @property string $feedback
 * @property integer $rating
 * @property string $create_dt
 * @property string $response
 * @property string $response_dt
 *
 * @property OfferRequest[] $offerRequests
 * @property OfferRequest[] $offerRequests0
 * @property SearchRequestOffer[] $searchRequestOffers
 * @property SearchRequestOffer[] $searchRequestOffers0
 * @property User $user
 * @property User $secondUser
 */
class UserFeedback extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_feedback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'second_user_id', 'feedback', 'rating', 'response'], 'required'],
            [['user_id', 'second_user_id', 'rating'], 'integer'],
            [['create_dt', 'response_dt'], 'safe'],
            [['feedback', 'response'], 'string', 'max' => 4096],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['second_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['second_user_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferRequests()
    {
        return $this->hasMany('\app\models\OfferRequest', ['counter_user_feedback_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferRequests0()
    {
        return $this->hasMany('\app\models\OfferRequest', ['user_feedback_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestOffers()
    {
        return $this->hasMany('\app\models\SearchRequestOffer', ['counter_user_feedback_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestOffers0()
    {
        return $this->hasMany('\app\models\SearchRequestOffer', ['user_feedback_id' => 'id']);
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
    public function getSecondUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'second_user_id']);
    }
}
