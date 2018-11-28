<?php
//commit -  neu
namespace app\controllers;

use Yii;
use app\models\Video;
use app\models\VideoSearch;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

/**
 * AdminUserController implements the CRUD actions for User model.
 */
class AdminVideoController extends AdminController
{
	public $x_user_id = '24b1e691-7694-4d3c-b5ba-f76d0f415ae4';
	public $x_email_address = 'devops@lukasvollmer.com';
	public $x_password = 'Uzumymw123!';
	public $x_access_token = '';
	public $x_tenant_id = 't-bcrhcg6ibgwx';
	public $headers = '';
	public $timeout = 5;
	public $tableVideo = 'video';
	public $insertCount = 0;
	public $deleteCount = 0;
	public $errorMessage = '';
	public $deleteCatCount = '';
	public $deleteMessage = '';
	public $cat_array = array();
	public $dailymotionUser =  'Jugl_net';
	public $dailymotion_video_ids = array();
	
    public function actionIndex()
    {

		$searchModel = new VideoSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
        return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
    }

    public function actionCreate()
    {
        $model = new Video();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->save();
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

	
	public function actionDeleteall()
	{
        Yii::$app->db->createCommand("DELETE FROM video")->query();
        return $this->pjaxRefresh();
	}
	
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
        } catch (\yii\base\Exception $e) {
            return $this->pjaxRefreshAlert(Yii::t('app',"Can't delete this item, it is use by another item(s)"));
        }
        return $this->pjaxRefresh();
    }
	
	public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['admin-video/index']);

        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }
	
	/**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdatebonus()
    {
		$model = new \app\models\AdminVideoUpdateBonusForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$bonus = str_replace(",", ".", $model->input);
			$checkVideo = Yii::$app->db->createCommand("
			UPDATE  
				video 
			SET 
				bonus =  ".$bonus."
			WHERE 
				provider = '".$model->type."'
			")->query();
			Yii::$app->session->setFlash('result',Yii::t('app','Der Werbonus für '.$model->type.' wurde für alle Videos erfolgreich auf '.$bonus.' gesetzt.'));
        }
		
        return $this->render('updatebonus', [
            'model' => $model,
        ]);
	}
	
    protected function findModel($id)
    {
        if (($model = Video::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	/*
	 * IMPORT FUCTION FROM DAILYMOTION VIDEO PLAYLISTS
	 */
	public function actionImportdailymotion($cron = false){
		$model = new Video();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->save();
                return $this->redirect(['index']);
            }
        }
		$data = $this->_get_all_playlist_dailymotion();
		
		
		if($data == false){
			$message = 'Es ist ein fehler beim Importieren aufgetreten: Die Playliste von Dailymotion konnte mit dem User '.$this->dailymotionUser.' nicht gefunden werden!';
		}else{
			
				$this->_removeOldCategories_dailymotion();
				$message = Yii::t('app','Import erfolgreich!<br /> Es wurden '.$this->insertCount.' Video(s) hinzugefügt und '.$this->deleteCount.' gelöscht.<br /><br />'.($this->errorMessage != '' ? '<b>FEHLER</b><br />'.$this->errorMessage.'<br /><br />' : '').($this->deleteMessage != '' ? '<b>KATEGORIEN</b><br />'.$this->deleteMessage : ''));
			
		}
		return $this->render('importdailymotion', [
			'import_result' => $message
        ]);
	}
	
	public function _get_all_playlist_dailymotion($limit = 100){
		$url = 'https://api.dailymotion.com/user/'.$this->dailymotionUser.'/playlists?limit='.$limit.'&fields=name,id,item_type,description,thumbnail_url,videos_total';
		$chI = curl_init();
		curl_setopt($chI, CURLOPT_URL, $url);
		curl_setopt($chI, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($chI, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($chI, CURLOPT_SSL_VERIFYPEER, FALSE);
		$data = curl_exec($chI);
		curl_close($chI);
		$data = json_decode($data);
		if(isset($data->error)){
			return false;
		}else{
			
			

			foreach($data->list as $key => $val){
				$playlistData = [];
				$playlistData[] = $val;
				$this->_get_all_videos_by_playlists_dailymotion($playlistData);
			}
			$this->_removeVideo_dailymotion();
			return true;
		}
		
		
	}
	public function _get_all_videos_by_playlists_dailymotion($playlistData, $limit = 100){
		$dataVideo = [];
		
		if(isset($playlistData) && $playlistData != 'NULL'){
				$url = 'https://api.dailymotion.com/playlist/'.$playlistData[0]->id.'/videos?limit='.$limit.'&fields=thumbnail_180_url,id,title,country,created_time,description,duration,end_time,start_time,language,tags,updated_time';
				$chI = curl_init();
				curl_setopt($chI, CURLOPT_URL, $url);
				curl_setopt($chI, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($chI, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($chI, CURLOPT_SSL_VERIFYPEER, FALSE);
				$dataTmp = curl_exec($chI);
				curl_close($chI);
				
				$dataVideo[] = json_decode($dataTmp);
				$dataVideo[0]->playlist_id = $playlistData[0]->id;
				$dataVideo[0]->playlist_name = $playlistData[0]->name;
				$dataVideo[0]->playlist_item_type = $playlistData[0]->item_type;
				$dataVideo[0]->playlist_description = $playlistData[0]->description;
				$dataVideo[0]->playlist_thumbnail_url = $playlistData[0]->thumbnail_url;
				$dataVideo[0]->playlist_videos_total = $playlistData[0]->videos_total;
				$this->_import_all_dailymotion($dataVideo);
				return true;

		}

	}
	
	function _checkVideos_dailymotion($video, $catId, $cat_name){

		/* NVII-MEDIA
		 * Fügt das Glomex Video hinzu, wenn keine Übereinstimmung mit der Datenbank vorhanden ist.
		 */
		
		$result = true;
		$checkVideo = Yii::$app->db->createCommand("
			SELECT 
				* 
			FROM 
				video 
			WHERE 
				clip_id = '".$video->id."' 
				AND cat_id = '".$catId."' 
				AND cat_name = '".$cat_name."'
				AND provider = 'dailymotion'
			")->query();
			
		foreach($checkVideo as $item){
			if($item['video_id']){
				$result = false;
			}
		}
		return $result;
	}
	
	function _removeOldCategories_dailymotion(){
		$videoCats = Yii::$app->db->createCommand("
			SELECT 
				cat_id,
				cat_name
			FROM 
				video 
			WHERE 
				provider = 'daylimotion'
			GROUP BY cat_id, cat_name
		")->query();

		 foreach($videoCats as $catItem){
			 if (!in_array($catItem["cat_id"], $this->cat_array)){
				 $deleteCount = Yii::$app->db->createCommand("SELECT COUNT(*) as deleteCount FROM video WHERE cat_id = '".$catItem["cat_id"]."' AND provider = 'daylimotion'")->query();

				foreach($deleteCount as $deleteCountItem){
					$countDelete = $deleteCountItem['deleteCount'];
				}

				if($countDelete != 0){
					Yii::$app->db->createCommand("DELETE FROM video WHERE cat_id = '".$catItem["cat_id"]."' AND provider = 'daylimotion'")->query();
					$this->deleteMessage .= 'Es wurde die Kategorie '.$catItem["cat_name"].' mit '.$countDelete.' Video(s) gelöscht<br />';
					$this->deleteCount += $countDelete;
				}
			 }
		 }
	}
	
	public function _removeVideo_dailymotion(){
		$video_ids = array();
		$videoByCat = Yii::$app->db->createCommand("
				SELECT 
					clip_id,
					cat_name,
					provider
				FROM 
					video 
				WHERE 
					provider = 'dailymotion'
				")->query();
				
		
		foreach($videoByCat as $key => $val){
			$video_ids[] = $val['clip_id'];
		}
		
		foreach($video_ids as $key1 => $val1){
			if (!in_array($val1, $this->dailymotion_video_ids)){
				Yii::$app->db->createCommand("DELETE FROM video WHERE clip_id = '".$val1."' AND provider = 'dailymotion'")->query();
				$this->deleteCount++;
			 }
		}

	}
	
	
	public function _import_all_dailymotion($data){
		
	
		foreach($data as $key => $val){
		
			
			foreach($val->list as $key1 => $val1){
			
				$result = $this->_checkVideos_dailymotion($val1, $val->playlist_id, $val->playlist_name);
				
				$this->dailymotion_video_ids[] = $val1->id;
				if($result == true){	
					
					$val1->duration = $val1->duration * 1000;
					if($val1->duration == ''){
						$this->errorMessage .= 'Video '.$val1->title.' hat keine Zeitangabe!<br />'; 
						return false;
					}
					
					
					$taxI = 0;
					$alias = '';
					$taxos = '';
					foreach($val1->tags as $taxoKey => $taxoVal){
						if($taxI >= 1){
							$alias = ',';
						}
							if($taxI < 2){
								$taxos .= $alias.strtolower(utf8_encode($taxoVal));
								$taxI++;
							}
					}					
					$titelTmp = str_replace("'", "", $val1->title);
					$titel = str_replace("\\", "", $titelTmp);
					
					$descriptionTmp = str_replace("'", "", $val1->description);
					$description = str_replace("\\", "", $descriptionTmp);
					
					$thumbnail_180_urlTmp = str_replace("'", "", $val1->thumbnail_180_url);
					$thumbnail_180_url = str_replace("\\", "", $thumbnail_180_urlTmp);
					
					$this->insertCount++;
					Yii::$app->db->createCommand("
					INSERT INTO ".$this->tableVideo." (
								clip_id, 
								name, 
								description, 
								image, 
								language, 
								clip_duration, 
								start_date, 
								end_date, 
								created_at,
								cat_id,
								cat_name,
								provider,
								video_tags,
								modified_at
							) 
							VALUES (
								'".($val1->id != '' ? $val1->id : '')."', 
								'".($titel != '' ? str_replace("'", "", $titel) : '')."', 
								'".($description != '' ? str_replace("'", "", $description) : '')."', 
								'".($thumbnail_180_url != '' ? str_replace("'", "", $thumbnail_180_url) : '')."', 
								'".($val1->language != '' ? $val1->language : '')."', 
								'".($val1->duration != '' ? $val1->duration : '')."', 
								'".($val1->start_time != '' ? $val1->start_time : '')."', 
								'".($val1->end_time != '' ? $val1->end_time : '')."', 
								'".($val1->created_time != '' ? $val1->created_time : '')."',
								'".$val->playlist_id."',
								'".$val->playlist_name."',
								'dailymotion',
								'".$taxos."',
								'".($val1->updated_time != '' ? $val1->updated_time : '')."'
							)")->query();
				}
			}
		}	
	}
	
	/*
	 * IMPORT FUCTION FROM GLOMEX VIDEO PLAYLISTS
	 */
	public function actionImportglomex($cron = false){
		
		$model = new Video();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->save();
                return $this->redirect(['index']);
            }
        }
		
		$access_result = $this->_get_x_access_token();
		if(!empty($access_result)){
			if($cron == true){
				$this->set_show_today_new();
			}
			$import_result = $this->_get_all_videos_by_playlists_glomex();
		}
		
		return $this->render('importglomex', [
			'import_result' => $import_result
        ]);

	}
	
	function _get_x_access_token(){
		$getAccessToken = 'https://mes-user-registration-service-prod-eu-west-1.mep.glomex.cloud/sessions';
		$chI = curl_init();
		curl_setopt($chI, CURLOPT_URL, $getAccessToken);
		curl_setopt($chI, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($chI, CURLOPT_CONNECTTIMEOUT, $this->timeout);
		curl_setopt($chI, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($chI, CURLOPT_POSTFIELDS, array('email_address' => $this->x_email_address, 'password' => $this->x_password) );  
		$data = curl_exec($chI);
		curl_close($chI);
		$data = json_decode($data);
		$this->x_access_token = $data->access_token;
		
		$this->headers = [
			'x-user-id: '.$this->x_user_id,
			'x-access-token: '.$this->x_access_token,
			'x-tenant-id: '.$this->x_tenant_id
		];
		return $this->x_access_token;
	}
	
	function _get_all_playlist_glomex(){
		$getVideoPlaylist = 'https://mes-portal-frontend-proxy-prod-eu-west-1.mep.glomex.cloud/pb/tenant_playlists?asset_type=playlist';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $getVideoPlaylist);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_USERPWD, $this->x_email_address.':'.$this->x_password );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$data = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($data);
		return $data;
	}
	
	function _get_all_videos_by_playlists_glomex(){
			$dataPlaylists = $this->_get_all_playlist_glomex();
			if(isset($dataPlaylists)){
				foreach($dataPlaylists as $key => $val){
					foreach($val as $key1 => $val1){
						$playlistId = $val1->id;
						$getAllVideos = 'https://mes-portal-frontend-proxy-prod-eu-west-1.mep.glomex.cloud/pb/tenant_playlists/'.$playlistId;
						
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $getAllVideos);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
						curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
						curl_setopt($ch, CURLOPT_USERPWD, $this->x_email_address.':'.$this->x_password );
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

						$data = curl_exec($ch);
						curl_close($ch);
						$data = json_decode($data);
						$this->cat_array[] = $playlistId; 
						$result = $this->_import_all_glomex($data, $playlistId);
					}
				}
			}
			
			
			$this->_removeOldCategories_glomex();
			return Yii::t('app','Import erfolgreich!<br /> Es wurden '.$this->insertCount.' Video(s) hinzugefügt und '.$this->deleteCount.' gelöscht.<br /><br />'.($this->errorMessage != '' ? '<b>FEHLER</b><br />'.$this->errorMessage.'<br /><br />' : '').($this->deleteMessage != '' ? '<b>KATEGORIEN</b><br />'.$this->deleteMessage : ''));

	}
	
	function _checkVideos_glomex($video, $catId, $cat_name){

		/* NVII-MEDIA
		 * Fügt das Glomex Video hinzu, wenn keine Übereinstimmung mit der Datenbank vorhanden ist.
		 */
		$result = true;
		$checkVideo = Yii::$app->db->createCommand("
			SELECT 
				* 
			FROM 
				video 
			WHERE 
				clip_id = '".$video->_source->clip_id."' 
				AND tenant_id = '".$video->_source->tenant_id."' 
				AND cat_id = '".$catId."' 
				AND cat_name = '".$cat_name."'
				AND provider = 'glomex'
			")->query();
			
		foreach($checkVideo as $item){
			if($item['video_id']){
				$result = false;
			}
		}

		return $result;
	}
		
	function _removeOldCategories_glomex(){
		$videoCats = Yii::$app->db->createCommand("
			SELECT 
				cat_id,
				cat_name
			FROM 
				video 
			WHERE 
				provider = 'glomex'
			GROUP BY cat_id, cat_name
		")->query();

		 foreach($videoCats as $catItem){
			
			 if (!in_array($catItem["cat_id"], $this->cat_array)){
				 $deleteCount = Yii::$app->db->createCommand("SELECT COUNT(*) as deleteCount FROM video WHERE cat_id = '".$catItem["cat_id"]."' AND provider = 'glomex'")->query();

				foreach($deleteCount as $deleteCountItem){
					$countDelete = $deleteCountItem['deleteCount'];
				}

				if($countDelete != 0){
				 Yii::$app->db->createCommand("DELETE FROM video WHERE cat_id = '".$catItem["cat_id"]."' AND provider = 'glomex'")->query();
				 $this->deleteMessage .= 'Es wurde die Kategorie '.$catItem["cat_name"].' mit '.$countDelete.' Video(s) gelöscht<br />';
				 $this->deleteCount += $countDelete;
				}
			 }
		 }
	}
	

	function _import_all_glomex($data, $catId){ 
		$cat_name = $data->title;
		
		/* NVII-MEDIA
		 * Überprüft Datenbank und Glomex auf unterschiedliche ID's 
		 * Entfernt ggf. Video aus der Datenbank anhand der clip_id und catId
		 */
		$videoByCat = Yii::$app->db->createCommand("
			SELECT 
				clip_id,
				cat_name,
				provider
			FROM 
				video 
			WHERE cat_id = '".$catId."' 
				AND cat_name = '".$cat_name."'
				AND provider = 'glomex'
			")->query();
			

		foreach($videoByCat as $item){
			if (!in_array($item["clip_id"], $data->video_ids)){
				Yii::$app->db->createCommand("DELETE FROM video WHERE clip_id = '".$item["clip_id"]."' AND cat_id = '".$catId."' AND provider = 'glomex'")->query();
				$this->deleteCount++;
			}
		}		
	

		foreach($data->videos as $video){
				$result = $this->_checkVideos_glomex($video, $catId, $cat_name);
				if($result == true){
					
					$taxI = 0;
					$alias = '';
					$taxos = '';
					foreach($video->_source->taxonomies as $taxo){
						if($taxI >= 1){
							$alias = ',';
						}
						
						if($taxo->title != $video->_source->channel->title && $taxo->title != $video->_source->title && $taxo->title != $video->_source->content_owner->display_name){
							if($taxI < 2){
								$taxos .= $alias.strtolower(utf8_encode($taxo->title));
								$taxI++;
							}
						}
					}
			
					
					if($video->_source->clip_duration == ''){
						$this->errorMessage .= 'Video '.$video->_source->title.' hat keine Zeitangabe!<br />'; 
						return false;
					}
					$this->insertCount++;
					Yii::$app->db->createCommand("	
						INSERT INTO ".$this->tableVideo." (
							clip_id, 
							tenant_id, 
							name, 
							description, 
							image, 
							channel, 
							content_owner, 
							language, 
							clip_duration, 
							start_date, 
							end_date, 
							modified_at, 
							created_at,
							cat_id,
							cat_name,
							video_tags,
							provider
						) 
						VALUES (
							'".($video->_source->clip_id != '' ? $video->_source->clip_id : '')."', 
							'".($video->_source->tenant_id != '' ? $video->_source->tenant_id : '')."', 
							'".($video->_source->title != '' ? str_replace("'", "", $video->_source->title) : '')."', 
							'".($video->_source->description != '' ? str_replace("'", "", $video->_source->description) : '')."', 
							'".($video->_source->image->url != '' ? str_replace("'", "", $video->_source->image->url.'/profile:new-video-224x126') : '')."', 
							'".($video->_source->channel->title != '' ? str_replace("'", "", $video->_source->channel->title) : '')."', 
							'".($video->_source->content_owner->display_name != '' ? str_replace("'", "", $video->_source->content_owner->display_name)  : '')."', 
							'".($video->_source->language != '' ? $video->_source->language : '')."', 
							'".($video->_source->clip_duration != '' ? $video->_source->clip_duration : '')."', 
							'".($video->_source->start_date != '' ? $video->_source->start_date : '')."', 
							'".($video->_source->end_date != '' ? $video->_source->end_date : '')."', 
							'".($video->_source->modified_at != '' ? $video->_source->modified_at : '')."', 
							'".($video->_source->created_at != '' ? $video->_source->created_at : '')."',
							'".$catId."',
							'".$data->title."',
							'".($taxos != '' ? $taxos : '')."',
							'glomex'
						)")->query();	
				}
		}
	}
		
	
	public function actionStatistics($cron = false){
	

	$searchModel = new VideoSearch();
	$data = $searchModel->searchDate();
		
        return $this->render('statistics', [
            'searchModel' => $searchModel,
            'data' => json_encode($data)
        ]);
	}
}