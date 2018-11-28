<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "search_request_draft".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $data
 * @property string $create_dt
 *
 * @property User $user
 */
class SearchRequestDraft extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'search_request_draft';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['data'], 'string'],
            [['create_dt'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']]
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }
}
