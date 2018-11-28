<?php
namespace app\controllers;

use Yii;
use app\components\EDateTime;
use app\models\User;
use app\models\UserEvent;
use app\models\Country;
use app\models\BalanceLog;
use app\models\Video;
use app\components\Helper;
use yii\web\NotFoundHttpException;
use yii\helpers\Html;


class ExtApiVideoController extends \app\components\ExtApiController {
	
	//Robert erweitert
    public function actionDetails($id) {
        $model=Video::find()->andWhere('video_id=:video_id',[':video_id'=>$id])->one();

		
        if (!$model) {
            throw new \yii\web\NotFoundHttpException();
        }

		
		$data = $model->toArray([
            'video_id','tenant_id','name','description','image', 'clip_id', 'clip_duration', 'language','cat_id','cat_name','bonus', 'video_tags', 'provider'
        ]);
		
		
		$tagsTemp = [];
		$tagsObj = explode(',', $data['video_tags']);
		foreach($tagsObj as $tags){
			$tagsTemp[] = $tags;
		}
		
		$data['video_tags'] = $tagsTemp;
		$data['description'] = $data['description'];
		$data_list = Video::getVideoList($data['cat_id'], 4);
		$data_state = Video::getVideoState($data['video_id'], date('Y-m-d'));
		
		$video_user = $this->getUserByVideo(Yii::$app->user->id, $data['video_id']);
		foreach($video_user as $vu_item){
			$video_user = $vu_item;
		}
		
        return [
            'video'=>$data,
			'video_list'=>$data_list,
			'video_user'=>$video_user,
			'video_state'=>$data_state
        ];
		
    }
	
	public function actionGetCountVideoView($id) {
		$video_state = Video::getVideoState($id, date('Y-m-d'));
		return [
			'video_total_view'=> $video_state['video_total_view'],
			'video_total_bonus' => floatval($video_state['video_total_bonus'])
		];
		
        
    }
	
	public function actionGetUpdateParams($type, $id) {
        /*$video = Video::findOne($id);
		$video->addCountVideoView();
	

		return [
			$type=>$video->$type
		];*/
		
    }
	
	public function actionSetVideoBalance($id){
			
			
			$data = Video::find()->andWhere('video_id=:video_id',[':video_id'=>$id])->one();
			$check = $this->getUserByVideo(Yii::$app->user->id, $data['video_id']);
			foreach($check as $item){
				if(is_array($item) || count($item) > 0){
					return false;	
				}
			}
			if($data['bonus']){
				$this->setUserByVideo(Yii::$app->user->id, $data);
			}
			return $result=\app\models\UserVideo::accept(Yii::$app->user->id,$id);
			
			
	}
	
	public function getUserByVideo($userId, $videoId){
			return Yii::$app->db->createCommand("SELECT * FROM user_video WHERE user_id = ".$userId." AND video_id = '".$videoId."'")->queryAll();	
	}
	
	public function setUserByVideo($userId, $videoData){

		Yii::$app->db->createCommand("	
						INSERT INTO user_video (
							video_id, 
							user_id,
							video_name,
							video_cat,
							dt,
							dt_full,
							bonus
						) 
						VALUES (
							'".$videoData['video_id']."', 
							'".$userId."',
							'".$videoData['name']."',
							'".$videoData['cat_name']."',
							'".date('Y-m-d')."',
							'".date('Y-m-d H:i:s')."',
							'".$videoData['bonus']."'
						)")->query();
	}
	

}
