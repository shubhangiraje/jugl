<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_info_view".
 *
 * @property integer $user_id
 * @property string $views
 *
 * @property User $user
 */
class UserInfoView extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_info_view';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['views'], 'string'],
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
