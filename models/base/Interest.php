<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "interest".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $title
 * @property integer $file_id
 * @property integer $sort_order
 * @property integer $offer_view_bonus
 * @property double $offer_view_total_bonus
 * @property integer $search_request_bonus
 * @property string $type
 *
 * @property File $file
 * @property Interest $parent
 * @property Interest[] $interests
 * @property InterestParamValue[] $interestParamValues
 * @property Offer[] $offers
 * @property OfferInterest[] $offerInterests
 * @property OfferInterest[] $offerInterests0
 * @property OfferInterest[] $offerInterests1
 * @property Param[] $params
 * @property SearchRequestInterest[] $searchRequestInterests
 * @property SearchRequestInterest[] $searchRequestInterests0
 * @property SearchRequestInterest[] $searchRequestInterests1
 * @property UserInterest[] $userInterests
 * @property UserInterest[] $userInterests0
 * @property UserInterest[] $userInterests1
 * @property UserOfferRequestCompletedInterest[] $userOfferRequestCompletedInterests
 * @property User[] $users
 */
class Interest extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'interest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'file_id', 'sort_order'], 'integer'],
            [[ 'offer_view_bonus', 'search_request_bonus','offer_view_total_bonus'], 'number'],
            [['type'], 'string'],
            [['title'], 'string', 'max' => 200],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['file_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Interest::className(), 'targetAttribute' => ['parent_id' => 'id']]
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
    public function getParent()
    {
        return $this->hasOne('\app\models\Interest', ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInterests()
    {
        return $this->hasMany('\app\models\Interest', ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInterestParamValues()
    {
        return $this->hasMany('\app\models\InterestParamValue', ['interest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffers()
    {
        return $this->hasMany('\app\models\Offer', ['uf_offer_request_completed_interest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferInterests()
    {
        return $this->hasMany('\app\models\OfferInterest', ['level1_interest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferInterests0()
    {
        return $this->hasMany('\app\models\OfferInterest', ['level2_interest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferInterests1()
    {
        return $this->hasMany('\app\models\OfferInterest', ['level3_interest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParams()
    {
        return $this->hasMany('\app\models\Param', ['interest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestInterests()
    {
        return $this->hasMany('\app\models\SearchRequestInterest', ['level1_interest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestInterests0()
    {
        return $this->hasMany('\app\models\SearchRequestInterest', ['level2_interest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestInterests1()
    {
        return $this->hasMany('\app\models\SearchRequestInterest', ['level3_interest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInterests()
    {
        return $this->hasMany('\app\models\UserInterest', ['level1_interest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInterests0()
    {
        return $this->hasMany('\app\models\UserInterest', ['level2_interest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserInterests1()
    {
        return $this->hasMany('\app\models\UserInterest', ['level3_interest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserOfferRequestCompletedInterests()
    {
        return $this->hasMany('\app\models\UserOfferRequestCompletedInterest', ['interest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany('\app\models\User', ['id' => 'user_id'])->viaTable('user_offer_request_completed_interest', ['interest_id' => 'id']);
    }
}
