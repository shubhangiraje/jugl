<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "search_request_offer".
 *
 * @property integer $id
 * @property integer $search_request_id
 * @property string $create_dt
 * @property integer $user_id
 * @property string $description
 * @property string $details
 * @property string $price_from
 * @property string $price_to
 * @property integer $relevancy
 * @property string $status
 * @property string $reject_reason
 * @property string $reject_comment
 * @property integer $user_feedback_id
 * @property integer $counter_user_feedback_id
 * @property string $closed_dt
 *
 * @property UserFeedback $counterUserFeedback
 * @property SearchRequest $searchRequest
 * @property User $user
 * @property UserFeedback $userFeedback
 * @property SearchRequestOfferDetailsFile[] $searchRequestOfferDetailsFiles
 * @property File[] $files
 * @property SearchRequestOfferFile[] $searchRequestOfferFiles
 * @property File[] $files0
 * @property SearchRequestOfferParamValue[] $searchRequestOfferParamValues
 * @property Param[] $params
 */
class SearchRequestOffer extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_request_offer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['search_request_id', 'user_id', 'description', 'price_from'], 'required'],
            [['search_request_id', 'user_id', 'relevancy', 'user_feedback_id', 'counter_user_feedback_id'], 'integer'],
            [['create_dt', 'closed_dt'], 'safe'],
            [['description', 'details', 'status', 'reject_reason', 'reject_comment'], 'string'],
            [['price_from', 'price_to'], 'number'],
            [['counter_user_feedback_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserFeedback::className(), 'targetAttribute' => ['counter_user_feedback_id' => 'id']],
            [['search_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => SearchRequest::className(), 'targetAttribute' => ['search_request_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['user_feedback_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserFeedback::className(), 'targetAttribute' => ['user_feedback_id' => 'id']]
        ];
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
    public function getSearchRequest()
    {
        return $this->hasOne('\app\models\SearchRequest', ['id' => 'search_request_id']);
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
    public function getSearchRequestOfferDetailsFiles()
    {
        return $this->hasMany('\app\models\SearchRequestOfferDetailsFile', ['search_request_offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany('\app\models\File', ['id' => 'file_id'])->viaTable('search_request_offer_details_file', ['search_request_offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestOfferFiles()
    {
        return $this->hasMany('\app\models\SearchRequestOfferFile', ['search_request_offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFiles0()
    {
        return $this->hasMany('\app\models\File', ['id' => 'file_id'])->viaTable('search_request_offer_file', ['search_request_offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestOfferParamValues()
    {
        return $this->hasMany('\app\models\SearchRequestOfferParamValue', ['search_request_offer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParams()
    {
        return $this->hasMany('\app\models\Param', ['id' => 'param_id'])->viaTable('search_request_offer_param_value', ['search_request_offer_id' => 'id']);
    }
}
