<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "offer".
 *
 * @property integer $id
 * @property string $create_dt
 * @property string $type
 * @property integer $allow_contact
 * @property integer $user_id
 * @property string $title
 * @property string $description
 * @property string $price
 * @property string $notify_if_price_bigger
 * @property integer $delivery_days
 * @property string $view_bonus
 * @property string $view_bonus_total
 * @property string $view_bonus_used
 * @property string $buy_bonus
 * @property integer $country_id
 * @property string $zip
 * @property string $city
 * @property string $address
 * @property string $active_till
 * @property integer $accepted_offer_request_id
 * @property string $status
 * @property string $status_before_deleted
 * @property integer $pay_allow_bank
 * @property integer $pay_allow_paypal
 * @property integer $pay_allow_jugl
 * @property integer $pay_allow_pod
 * @property integer $uf_enabled
 * @property integer $uf_age_from
 * @property integer $uf_age_to
 * @property string $uf_sex
 * @property string $uf_packet
 * @property integer $uf_offer_request_completed_interest_id
 * @property integer $uf_member_from
 * @property integer $uf_member_to
 * @property double $uf_offers_view_buy_ratio_from
 * @property double $uf_offers_view_buy_ratio_to
 * @property string $uf_balance_from
 * @property integer $uf_country_id
 * @property string $uf_city
 * @property string $uf_zip
 * @property integer $uf_distance_km
 * @property string $uf_offer_year_turnover_from
 * @property string $uf_offer_year_turnover_to
 * @property integer $uf_active_search_requests_from
 * @property integer $uf_messages_per_day_from
 * @property integer $uf_messages_per_day_to
 * @property integer $amount
 * @property integer $show_amount
 * @property string $delivery_cost
 * @property string $closed_dt
 * @property string $closed_dt_before_deleted
 * @property integer $created_by_admin
 * @property string $validation_status
 * @property integer $receivers_count
 * @property string $comment
 * @property integer $count_offer_view
 * @property string $scheduled_dt
 *
 * @property User $user
 * @property OfferRequest $acceptedOfferRequest
 * @property Country $ufCountry
 * @property Interest $ufOfferRequestCompletedInterest
 * @property Country $country
 * @property OfferFavorite[] $offerFavorites
 * @property User[] $users
 * @property OfferFile[] $offerFiles
 * @property File[] $files
 * @property OfferInterest[] $offerInterests
 * @property OfferParamValue[] $offerParamValues
 * @property OfferRequest[] $offerRequests
 * @property OfferView[] $offerViews
 * @property User[] $users0
 * @property OfferViewLog[] $offerViewLogs
 * @property UserSpamReport[] $userSpamReports
 */
class Offer extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_dt', 'active_till', 'closed_dt', 'closed_dt_before_deleted', 'scheduled_dt'], 'safe'],
            [['type', 'user_id', 'title', 'description', 'active_till'], 'required'],
            [['type', 'description', 'status', 'status_before_deleted', 'uf_sex', 'uf_packet', 'validation_status', 'comment'], 'string'],
            [['allow_contact', 'user_id', 'delivery_days', 'country_id', 'accepted_offer_request_id', 'pay_allow_bank', 'pay_allow_paypal', 'pay_allow_jugl', 'pay_allow_pod', 'uf_enabled', 'uf_age_from', 'uf_age_to', 'uf_offer_request_completed_interest_id', 'uf_member_from', 'uf_member_to', 'uf_country_id', 'uf_distance_km', 'uf_active_search_requests_from', 'uf_messages_per_day_from', 'uf_messages_per_day_to', 'amount', 'show_amount', 'created_by_admin', 'receivers_count', 'count_offer_view'], 'integer'],
            [['price', 'notify_if_price_bigger', 'view_bonus', 'view_bonus_total', 'view_bonus_used', 'buy_bonus', 'uf_offers_view_buy_ratio_from', 'uf_offers_view_buy_ratio_to', 'uf_balance_from', 'uf_offer_year_turnover_from', 'uf_offer_year_turnover_to', 'delivery_cost'], 'number'],
            [['title'], 'string', 'max' => 200],
            [['zip', 'city'], 'string', 'max' => 64],
            [['address'], 'string', 'max' => 128],
            [['uf_city'], 'string', 'max' => 32],
            [['uf_zip'], 'string', 'max' => 8],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['accepted_offer_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => OfferRequest::className(), 'targetAttribute' => ['accepted_offer_request_id' => 'id']],
            [['uf_country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['uf_country_id' => 'id']],
            [['uf_offer_request_completed_interest_id'], 'exist', 'skipOnError' => true, 'targetClass' => Interest::className(), 'targetAttribute' => ['uf_offer_request_completed_interest_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']]
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
    public function getAcceptedOfferRequest()
    {
        return $this->hasOne('\app\models\OfferRequest', ['id' => 'accepted_offer_request_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUfCountry()
    {
        return $this->hasOne('\app\models\Country', ['id' => 'uf_country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUfOfferRequestCompletedInterest()
    {
        return $this->hasOne('\app\models\Interest', ['id' => 'uf_offer_request_completed_interest_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne('\app\models\Country', ['id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferFavorites()
    {
        return $this->hasMany('\app\models\OfferFavorite', ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany('\app\models\User', ['id' => 'user_id'])->viaTable('offer_favorite', ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferFiles()
    {
        return $this->hasMany('\app\models\OfferFile', ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany('\app\models\File', ['id' => 'file_id'])->viaTable('offer_file', ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferInterests()
    {
        return $this->hasMany('\app\models\OfferInterest', ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferParamValues()
    {
        return $this->hasMany('\app\models\OfferParamValue', ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferRequests()
    {
        return $this->hasMany('\app\models\OfferRequest', ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferViews()
    {
        return $this->hasMany('\app\models\OfferView', ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers0()
    {
        return $this->hasMany('\app\models\User', ['id' => 'user_id'])->viaTable('offer_view', ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferViewLogs()
    {
        return $this->hasMany('\app\models\OfferViewLog', ['offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserSpamReports()
    {
        return $this->hasMany('\app\models\UserSpamReport', ['offer_id' => 'id']);
    }
}
