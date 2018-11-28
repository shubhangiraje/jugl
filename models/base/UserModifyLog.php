<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_modify_log".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $modify_dt
 * @property string $description
 *
 * @property User $user
 */
class UserModifyLog extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_modify_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['modify_dt'], 'safe'],
            [['description'], 'string'],
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
