<?php

namespace app\models;

use app\components\EDateTime;
use app\models\Video;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form about `app\modules\foms\models\User`.
 */
class VideoSearch extends Video
{
	
	
	 
	 public $gesamt_bonus;
	 public $monat_bonus;
	 public $letzter_monat_bonus;
	 public $gestern_bonus;
	 
	 public $gesamt_klicks;
	 public $monat_klicks;
	 public $letzter_monat_klicks;
	 public $klicks;
	 public $gestern_klicks;
	 public $video_name;
	 public $video_cat;
	 
	//public $cat_name;
	//public $name;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_name', 'name','video_date','bonus'], 'safe']
        ];
    }
	 public function attributeLabels() {
        return array_merge(parent::attributeLabels(),[
            'cat_name'=>Yii::t('app','Kategorie'),
            'name'=>Yii::t('app','Videotitel'),
            'video_date'=>Yii::t('app','Datum'),
			'bonus'=>Yii::t('app','Bonus'),
			'bonusnew'=>Yii::t('app','Bonus'),
			'last_bonus'=>Yii::t('app','Last Bonus'),
			'klicks'=>Yii::t('app','Klicks'),
			'last_klicks'=>Yii::t('app','Last Klicks'),
        ]);
    }
	
	 public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
	
	public static function getDateFilterList() {
        return [
            'VIDEO_DATE_DAY'=>Yii::t('app','Tag'),
            'VIDEO_DATE_MONTH'=>Yii::t('app','Monat')
        ];
    }
	
    public function search($params) {
		$query = VideoSearch::find()->where('');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes'=>[
                    'cat_name',
                    'name',
					'bonus'
                ],
                'defaultOrder'=>['video_id'=>SORT_DESC]
            ]
        ]);

		if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

		if($this->cat_name != ''){
			$query->andFilterWhere(['like', 'cat_name', $this->cat_name]);
		}
		
		if($this->name != ''){
			$query->andFilterWhere(['like', 'name', $this->name]);
		}
		
		return $dataProvider;
	}
	
	public function searchDate(){

				$sql = '
						SELECT v.video_id, v.name, v.cat_name, uv.video_name as video_name, uv.video_cat as video_cat, uv.dt, SUM(uv.bonus) as gesamt_bonus, COUNT(uv.video_id) as gesamt_klicks,

								(    SELECT SUM(bonus) 
									FROM user_video AS bv 
									WHERE
								   bv.video_id = v.video_id
								   AND YEAR(dt)=YEAR(CURRENT_DATE)
								   AND MONTH(dt)=MONTH(CURRENT_DATE)
								   AND (DAY(dt)=DAY(CURRENT_DATE))
									group by bv.video_id
								) 
								as bonus,
								
								(    SELECT SUM(bonus) 
									FROM user_video AS gbv
									WHERE
								   gbv.video_id = v.video_id
								   AND YEAR(dt)=YEAR(CURRENT_DATE)
								   AND MONTH(dt)=MONTH(CURRENT_DATE)
								   AND DAY(dt)=DAY(CURRENT_DATE-INTERVAL 1 DAY)
									group by gbv.video_id
								) 
								as gestern_bonus,
								
								(    SELECT COUNT(video_id)  
									FROM user_video kv
									WHERE
								   kv.video_id = v.video_id
								   AND YEAR(dt)=YEAR(CURRENT_DATE)
								   AND MONTH(dt)=MONTH(CURRENT_DATE)
								   AND (DAY(dt)=DAY(CURRENT_DATE))
									group by kv.video_id
								) 
								as klicks,
								
								(    SELECT COUNT(video_id) 
									FROM user_video AS gkv 
									WHERE
								   gkv.video_id = v.video_id
								   AND YEAR(dt)=YEAR(CURRENT_DATE)
								   AND MONTH(dt)=MONTH(CURRENT_DATE)
								   AND DAY(dt)=DAY(CURRENT_DATE-INTERVAL 1 DAY) 
								   group by gkv.video_id
								) 
								as gestern_klicks,
								
								
								(   SELECT SUM(bonus) 
									FROM user_video  AS mbv
									WHERE
								   mbv.video_id = v.video_id
								   AND YEAR(dt)=YEAR(CURRENT_DATE)
								   AND MONTH(dt)=MONTH(CURRENT_DATE)
								   group by mbv.video_id
								) 
								as monat_bonus,
								
								(    SELECT SUM(bonus) 
									FROM user_video AS lmbv
									WHERE
								  lmbv.video_id = v.video_id
								   AND YEAR(dt)=YEAR(CURRENT_DATE)
								   AND MONTH(dt)=MONTH(CURRENT_DATE-INTERVAL 1 MONTH)
									group by lmbv.video_id
								) 
								as letzter_monat_bonus,
								
								
								
								(    SELECT COUNT(video_id) 
									FROM user_video  AS mkv
									WHERE
								   mkv.video_id = v.video_id
								   AND YEAR(dt)=YEAR(CURRENT_DATE)
								   AND MONTH(dt)=MONTH(CURRENT_DATE)
									group by mkv.video_id
								) 
								as monat_klicks,
								
								(    SELECT COUNT(video_id) 
									FROM user_video AS lmkv
									WHERE
								   lmkv.video_id = v.video_id
								   AND YEAR(dt)=YEAR(CURRENT_DATE)
								   AND MONTH(dt)=MONTH(CURRENT_DATE-INTERVAL 1 MONTH)
									group by lmkv.video_id
								) 
								as letzter_monat_klicks
								
								

							FROM video as v
							LEFT JOIN 
							user_video as uv ON(v.video_id =  uv.video_id)
							GROUP BY v.video_id,uv.dt, uv.video_name, uv.video_cat
							ORDER BY gesamt_klicks DESC
						';
				$query=VideoSearch::findBySql($sql)->all();
				
				$result=$query;
				$array=array();
					foreach($result as $data){
						array_push($array,array(($data['video_cat'] != '' ? $data['video_cat'] : $data['cat_name']),($data['video_name'] != '' ? $data['video_name'] : $data['name']),$data['gesamt_klicks'],$data['monat_klicks'],$data['letzter_monat_klicks'],$data['klicks'],$data['gestern_klicks'],$data['gesamt_bonus'],$data['monat_bonus'],$data['letzter_monat_bonus'],$data['bonus'],$data['gestern_bonus']));
					}
				return $array;
					

	}
}