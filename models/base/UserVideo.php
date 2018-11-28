<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_video".
 *
 * @property integer $video_id
 * @property integer $user_id
 * @property string $dt
 * @property string $dt_full
 * @property string $bonus
 * @property string $name
 * @property Video $video
 * @property User $user
 */
 
 
class UserVideo extends \app\components\ActiveRecord
{
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_video';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
		return [
            [['video_id', 'user_id'], 'integer'],
            [['bonus'], 'number']
        ];
    }
	public function getName(){
	return $this->hasOne('\app\models\Video', ['video_id' => 'name']);
	}
	public function getVideo()
    {
        return $this->hasOne('\app\models\Video', ['video_id' => 'video_id']);
    }
	public function getUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'user_id']);
    }

	
}
