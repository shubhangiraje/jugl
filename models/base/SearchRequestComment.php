<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "search_request_comment".
 *
 * @property integer $id
 * @property integer $search_request_id
 * @property integer $user_id
 * @property string $comment
 * @property string $create_dt
 * @property string $response
 * @property string $response_dt
 *
 * @property SearchRequest $searchRequest
 * @property User $user
 */
class SearchRequestComment extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_request_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['search_request_id', 'user_id', 'comment'], 'required'],
            [['search_request_id', 'user_id'], 'integer'],
            [['create_dt', 'response_dt'], 'safe'],
            [['comment', 'response'], 'string', 'max' => 4096],
            [['search_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => SearchRequest::className(), 'targetAttribute' => ['search_request_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']]
        ];
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
}
