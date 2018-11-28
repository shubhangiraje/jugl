<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_event".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $dt
 * @property string $type
 * @property integer $second_user_id
 * @property string $text
 *
 * @property User $user
 * @property User $secondUser
 */
class UserEvent extends \app\components\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type'], 'required'],
            [['user_id', 'second_user_id'], 'integer'],
            [['dt'], 'safe'],
            [['type', 'text'], 'string']
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
}
