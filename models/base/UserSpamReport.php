<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_spam_report".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $second_user_id
 * @property string $dt
 * @property string $object
 * @property integer $offer_id
 * @property integer $search_request_id
 * @property string $comment
 * @property integer $is_active
 *
 * @property User $user
 * @property User $secondUser
 * @property Offer $offer
 * @property SearchRequest $searchRequest
 */
class UserSpamReport extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_spam_report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'second_user_id', 'object', 'comment'], 'required'],
            [['user_id', 'second_user_id', 'offer_id', 'search_request_id', 'is_active'], 'integer'],
            [['dt'], 'safe'],
            [['object'], 'string', 'max' => 256],
            [['comment'], 'string', 'max' => 1024],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['second_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['second_user_id' => 'id']],
            [['offer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Offer::className(), 'targetAttribute' => ['offer_id' => 'id']],
            [['search_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => SearchRequest::className(), 'targetAttribute' => ['search_request_id' => 'id']]
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
    public function getSecondUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'second_user_id']);
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
    public function getSearchRequest()
    {
        return $this->hasOne('\app\models\SearchRequest', ['id' => 'search_request_id']);
    }
}
