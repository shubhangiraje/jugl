<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "video".
 *
 * @property integer $video_id
 * @property string $tenant_id
 * @property string $name
 * @property string $description
 * @property string $image
 * @property string $channel
 * @property string $content_owner
 * @property string $taxonomies
 * @property string $clip_id
 * @property string $language
 * @property integer $clip_duration
 * @property string $start_date
 * @property string $end_date
 * @property string $modified_at
 * @property string $created_at
 * @property string $cat_id
 * @property string $cat_name
 * @property double $bonus
 */
class Video extends \app\components\ActiveRecord
{
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'video';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
		return [
            [['video_id', 'tenant_id', 'title', 'cat_id', 'cat_name'], 'required'],
            [['tenant_id', 'name', 'description', 'image', 'channel', 'content_owner', 'taxonomies', 'clip_id', 'language', 'start_date', 'end_date', 'modified_at', 'created_at', 'cat_id', 'cat_name'], 'string'],
            [['video_id', 'clip_duration'], 'integer'],
            [['bonus'], 'number']
        ];
    }
	
	
	

	
}
