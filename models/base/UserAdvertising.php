<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "advertising_user".
 *
 * @property integer $id
 * @property integer $advertising_id
 * @property integer $user_id
 * @property string $dt
 * @property integer $status
 * @property string $advertising_bonus
 * @property string $name
 * @property Advertising $advertising
 * @property User $user
 */
 
 
class UserAdvertising extends \app\components\ActiveRecord
{
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'advertising_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
		return [
            [['id', 'advertising_id', 'user_id'], 'integer'],
            [['advertising_bonus'], 'number']
        ];
    }
	public function getName(){
		return $this->hasOne('\app\models\Advertising', ['id' => 'name']);
	}
	public function getAdvertising()
    {
        return $this->hasOne('\app\models\Advertising', ['id' => 'video_id']);
    }
	public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }

	
}
