<?php

namespace app\models;

use Yii;

class Video extends \app\models\base\Video
{
	
	const STATUS_ACTIVE='ACTIVE';
    const STATUS_REJECTED='REJECTED';
    const STATUS_AWAITING_ACTIVATION='AWAITING_ACTIVATION';
	const VIEW_BONUS_PERCENT_PARENT=10;
    const VIEW_BONUS_PERCENT_JUGL=10;
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'cat_name' => Yii::t('app','Kategorie'),
            'name' => Yii::t('app','Title'),
			'clip_duration' => Yii::t('app','Videolänge'),
			'language' => Yii::t('app','Sprache'),
			'bonus' => Yii::t('app','Werbebonus'),
            'create_date' => Yii::t('app','create date'),
            'update_date' => Yii::t('app','update date'),
            'id' => Yii::t('app','cat id')
        ];
    }
	
	 /**
     * @inheritdoc
	 * Robert remove cat_name from array
     */
    public function rules()
    {
        return [
            [['video_id', 'name', 'cat_id'], 'required'],
            [['tenant_id', 'name', 'description', 'image', 'channel', 'content_owner', 'taxonomies', 'clip_id', 'language', 'start_date', 'end_date', 'modified_at', 'created_at', 'cat_id', 'cat_name'], 'string'],
            [['video_id', 'clip_duration'], 'integer'],
            [['bonus'], 'number']
        ];
    }
	
	public function getCatName() {
		return $this->cat_name;
	}

	/*
	public function addCountVideoView() {

		wird nicht mehr benötigt, da die statistik in der video_user gespeichert werden soll
        $this->show_today++;
        $this->save(); 
	}
	*/ 
	
	public function getVideoList($cat_id, $limit = 5){
		$result = Yii::$app->db->createCommand("SELECT * FROM video WHERE cat_id = '".$cat_id."' ORDER BY rand() LIMIT ".$limit)->query();
		$data = [];
		
		foreach($result as $item){
			$tagsTemp = [];
			$tagsObj = explode(',', $item['video_tags']);
			foreach($tagsObj as $tags){
				$tagsTemp[] = $tags;
			}
			
			 $idata = [
                'video_id' => $item['video_id'],
				'name' => $item['name'],
				'clip_id' => $item['clip_id'],
				'language' => $item['language'],
				'cat_name' => $item['cat_name'],
				'cat_id' => $item['cat_id'],
				'image' => $item['image'],
				'video_tags' => $tagsTemp
            ];
			$idata['name'] = \yii\helpers\StringHelper::truncate($item['name'],40);
			$idata['full_name'] = $item['name'];
			$data[] = $idata;
		}

		return $data;
	}
	
	public function getVideoState($video_id, $date = NULL){
		$result = Yii::$app->db->createCommand("SELECT COUNT(*) as total_view, SUM(bonus) as total_bonus FROM user_video WHERE video_id = '".$video_id."' AND dt = '".$date."' GROUP BY video_id")->query();
		
		foreach($result as $item){
			$data = [
				'video_total_view' => $item['total_view'],
				'video_total_bonus' => $item['total_bonus']
			];
		}
		if($data === NULL){
			$data = [
				'video_total_view' => 0,
				'video_total_bonus' => 0
			];
		}
		
		return $data;
	}

	public function getStatic($date){
		$result = Yii::$app->db->createCommand("
		SELECT v.video_id, v.name, uv.dt,
        (SELECT COUNT(*) FROM user_video as uv1 WHERE uv1.video_id = v.video_id and uv1.dt = uv.dt GROUP BY v.video_id) as total_view,
        (SELECT SUM(bonus) FROM user_video as uv1 WHERE uv1.video_id = v.video_id and uv1.dt = uv.dt GROUP BY  v.video_id) as total_bonus
        FROM user_video as uv
        RIGHT JOIN video as v ON(uv.video_id = v.video_id)
        GROUP BY v.video_id, uv.dt ORDER BY total_view DESC, video_id ASC LIMIT 20")->query();
		
		foreach($result as $item){
			$data[] = [
				'video_id' => $item['video_id'],
				'video_name' => $item['name'],
				'video_total_view' => $item['total_view'],
				'video_total_bonus' => $item['total_bonus']
			];
		}

		
		return $data;
	}
	
	public function getUserVideo()
    {
        return $this->hasMany('\app\models\UserVideo', ['video_id' => 'id']);
    }
	
	
}
