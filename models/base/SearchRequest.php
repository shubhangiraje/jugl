<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "search_request".
 *
 * @property integer $id
 * @property string $search_request_type
 * @property integer $provider_id
 * @property string $create_dt
 * @property integer $user_id
 * @property string $title
 * @property string $description
 * @property string $price_from
 * @property string $price_to
 * @property string $bonus
 * @property integer $country_id
 * @property string $zip
 * @property string $city
 * @property string $address
 * @property string $active_till
 * @property string $status
 * @property string $status_before_deleted
 * @property string $closed_dt
 * @property string $closed_dt_before_deleted
 * @property string $validation_status
 * @property string $feedback_text_de
 * @property string $feedback_text_en
 * @property string $feedback_text_ru
 * @property string $scheduled_dt
 *
 * @property User $user
 * @property Country $country
 * @property SearchRequestComment[] $searchRequestComments
 * @property SearchRequestFavorite[] $searchRequestFavorites
 * @property User[] $users
 * @property SearchRequestFile[] $searchRequestFiles
 * @property File[] $files
 * @property SearchRequestInterest[] $searchRequestInterests
 * @property SearchRequestOffer[] $searchRequestOffers
 * @property SearchRequestParamValue[] $searchRequestParamValues
 * @property UserSpamReport[] $userSpamReports
 */
class SearchRequest extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_request';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['search_request_type', 'description', 'status', 'status_before_deleted', 'validation_status', 'feedback_text_de', 'feedback_text_en', 'feedback_text_ru'], 'string'],
            [['provider_id', 'user_id', 'country_id'], 'integer'],
            [['create_dt', 'active_till', 'closed_dt', 'closed_dt_before_deleted', 'scheduled_dt'], 'safe'],
            [['user_id', 'title', 'description', 'price_from', 'bonus', 'country_id', 'zip', 'active_till'], 'required'],
            [['price_from', 'price_to', 'bonus'], 'number'],
            [['title'], 'string', 'max' => 200],
            [['zip', 'city'], 'string', 'max' => 64],
            [['address'], 'string', 'max' => 128],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
    public function getCountry()
    {
        return $this->hasOne('\app\models\Country', ['id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestComments()
    {
        return $this->hasMany('\app\models\SearchRequestComment', ['search_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestFavorites()
    {
        return $this->hasMany('\app\models\SearchRequestFavorite', ['search_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany('\app\models\User', ['id' => 'user_id'])->viaTable('search_request_favorite', ['search_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestFiles()
    {
        return $this->hasMany('\app\models\SearchRequestFile', ['search_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany('\app\models\File', ['id' => 'file_id'])->viaTable('search_request_file', ['search_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestInterests()
    {
        return $this->hasMany('\app\models\SearchRequestInterest', ['search_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestOffers()
    {
        return $this->hasMany('\app\models\SearchRequestOffer', ['search_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestParamValues()
    {
        return $this->hasMany('\app\models\SearchRequestParamValue', ['search_request_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserSpamReports()
    {
        return $this->hasMany('\app\models\UserSpamReport', ['search_request_id' => 'id']);
    }
}
