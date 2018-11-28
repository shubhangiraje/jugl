<?php

namespace app\models;

use Yii;

class Advertising extends \app\models\base\Advertising
{
	const ADVERTISING_TYPE_SELF='SELF';
	const ADVERTISING_TYPE_BANNER='BANNER';
	
	const ADVERTISING_POSITION_FORUM_TOP='FORUM_TOP';
	const ADVERTISING_POSITION_FORUM_BOTTOM='FORUM_BOTTOM';
	const ADVERTISING_POSITION_ADVERTISING='ADVERTISING';
	const ADVERTISING_POSITION_ADVERTISING_DETAIL='ADVERTISING_DETAIL';
	const ADVERTISING_POSITION_ADVERTISING_DETAIL_WHITOUT_BONUS_DETAIL='ADVERTISING_WHITOUT_BONUS_DETAIL';
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
			'advertising_name' => Yii::t('app', 'Name'),
			'advertising_display_name' => Yii::t('app', 'Anzeige Name'),
			'advertising_total_bonus' => Yii::t('app', 'Vergütung in Euro'),
			'advertising_total_views' => Yii::t('app', 'Views zur Vergütung'),
			'advertising_total_clicks' => Yii::t('app', 'Klicks zur Vergütung'),
			'banner' => Yii::t('app', 'Banner Bild'),
			'link' => Yii::t('app', 'Link'),
			'dt' => Yii::t('app', 'Erstelldatum'),
			'status' => Yii::t('app', 'Status'),
			'advertising_position' => Yii::t('app', 'Position'),
			'provider' => Yii::t('app', 'Anbieter'),
			'banner_height' => Yii::t('app', 'Banner Höhe'),
			'banner_width' => Yii::t('app', 'Banner Breite'),
			'user_bonus' => Yii::t('app', 'Auszahlung Jugl an Nutzer'),
			'advertising_type' => Yii::t('app', 'Typ der Werbung'),
			'click_interval' => Yii::t('app', 'Klick Interval - Reload Sperre'),
			'popup_interval' => Yii::t('app', 'Zeit in Sekunden im Popup'),
			'display_date' => Yii::t('app', 'Anzeige aktiv bis'),
			'country_id' => Yii::t('app', 'Land'),
			'release_date' => Yii::t('app','Erscheindatum'),
        ];
    }
	
	 /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advertising_name', 'advertising_display_name', 'provider', 'user_bonus', 'advertising_total_bonus', 'advertising_total_views', 'display_date', 'release_date', 'popup_interval'], 'required'],
			[['advertising_total_views', 'advertising_total_clicks', 'status', 'popup_interval'], 'integer'],
            [['dt', 'link', 'banner', 'advertising_position', 'provider', 'banner_height', 'banner_width', 'advertising_type', 'advertising_display_name', 'click_interval', 'country_id'], 'safe'],
			[['user_bonus', 'advertising_total_bonus'], 'number']
        ];
    }
	
	public static function getProviderList() {
        static $items;

		$items=array(0 => Yii::t('app','Eigener'));
		$model = AdvertisingSearchRequestProvider::find()->all();
		if($model){
			foreach($model as $value){
				$items[$value->provider_id]=$value->provider_name;
			
			}
		}		
        return $items;
    }
	
	public static function getAdvertisingTypeList() {
       static $items;

        if (!isset($items)) {
            $items=[
				static::ADVERTISING_TYPE_BANNER=>Yii::t('app','Banner'),
            ];
        }

        return $items;
    }
	
	public static function getAdvertisingPositionList() {
       static $items;

        if (!isset($items)) {
            $items=[
				static::ADVERTISING_POSITION_ADVERTISING=>Yii::t('app','Dashboard - Zwischen der Werbung'),
				static::ADVERTISING_POSITION_FORUM_TOP=>Yii::t('app','Forum oben'),
				static::ADVERTISING_POSITION_FORUM_BOTTOM=>Yii::t('app','Forum unten'),
				static::ADVERTISING_POSITION_ADVERTISING_DETAIL=>Yii::t('app','Werbung Detail - Zwischen der Werbung mit Werbebonus'),
				static::ADVERTISING_POSITION_ADVERTISING_DETAIL_WHITOUT_BONUS_DETAIL=>Yii::t('app','Werbung Detail - Zwischen der Werbung ohne Werbebonus'),
            ];
        }

        return $items;
    }

	
	public function getAdvertisingOfferAdvanceSearch($position = '' , $provider = '',$pageNum=1,$perPage=null, $country_id = 0, $excludeIds){
		if($excludeIds){
			foreach($excludeIds as $k => $v){
				$excludeIds[$k] = intval($v.'000');
			}
		}
		if($provider != ''){	
			$query = Advertising::find()->distinct()
            ->innerJoin('advertising_interest','advertising_interest.advertising_id=advertising.id')
			->orderBy(['id'=>SORT_DESC])->offset(($pageNum-1)*$perPage)->limit($perPage);		
			$query->where('advertising_position=:position and provider=:provider and status=1 and display_date>=NOW() and NOW()>=release_date and country_id=:country_id', 
			['position'=>$position, 'provider'=>$provider, 'country_id'=>$country_id]);
			
			if(!empty($excludeIds)){
				$query->andWhere('not exists(select id from advertising_interest where advertising_id=advertising.id and level1_interest_id in ('.implode(',',$excludeIds).'))');
			}
			
			$model=$query->all();
			
			$query = Advertising::find()->distinct()
            ->innerJoin('advertising_interest','advertising_interest.advertising_id=advertising.id')
			->orderBy(['id'=>SORT_DESC]);		
			$query->where('advertising_position=:position and provider=:provider and status=1 and display_date>=NOW() and NOW()>=release_date and country_id=:country_id', 
			['position'=>$position, 'provider'=>$provider, 'country_id'=>$country_id]);
			
			if(!empty($excludeIds)){
				$query->andWhere('not exists(select id from advertising_interest where advertising_id=advertising.id and level1_interest_id in ('.implode(',',$excludeIds).'))');
			}
			
			$rows=$query->all();
			$rowsCount=count($rows);
			
		}else{	
			$query = Advertising::find()->distinct()
            ->innerJoin('advertising_interest','advertising_interest.advertising_id=advertising.id')
			->orderBy(['id'=>SORT_DESC])->offset(($pageNum-1)*$perPage)->limit($perPage);		
			$query->where('advertising_position=:position and status=1 and display_date>=NOW() and NOW()>=release_date and country_id=:country_id', 
			['position'=>$position, 'country_id'=>$country_id]);
			
			if(!empty($excludeIds)){
				$query->andWhere('not exists(select id from advertising_interest where advertising_id=advertising.id and level1_interest_id in ('.implode(',',$excludeIds).'))');
			}

			$model=$query->all();
			
			$query = Advertising::find()->distinct()
            ->innerJoin('advertising_interest','advertising_interest.advertising_id=advertising.id')
			->orderBy(['id'=>SORT_DESC]);		
			$query->where('advertising_position=:position and status=1 and display_date>=NOW() and NOW()>=release_date and country_id=:country_id', 
			['position'=>$position, 'country_id'=>$country_id]);
			
			if(!empty($excludeIds)){
				$query->andWhere('not exists(select id from advertising_interest where advertising_id=advertising.id and level1_interest_id in ('.implode(',',$excludeIds).'))');
			}
			
			$rows=$query->all();
			$rowsCount=count($rows);
		}
		if($model){
			return $data = array('model'=>$model, 'rowsCount' => $rowsCount);
		}
	}
	
	
	public function getAdvertising($position = '' , $provider = '',$pageNum=1,$perPage=null, $country_id = 0){

		if($provider != ''){	
			$query = Advertising::find()->distinct()
            ->innerJoin('advertising_interest','advertising_interest.advertising_id=advertising.id')
			->orderBy(['id'=>SORT_DESC])->offset(($pageNum-1)*$perPage)->limit($perPage);		
			$query->where('advertising_position=:position and provider=:provider and status=1 and display_date>=NOW() and NOW()>=release_date and country_id=:country_id', 
			['position'=>$position, 'provider'=>$provider, 'country_id'=>$country_id]);
			if(Yii::$app->user->identity){
				Yii::$app->user->identity->addAdvertisingFilterConditions($query);
			}
			
			$model=$query->all();
			
			$query = Advertising::find()->distinct()
            ->innerJoin('advertising_interest','advertising_interest.advertising_id=advertising.id')
			->orderBy(['id'=>SORT_DESC]);		
			$query->where('advertising_position=:position and status=1 and display_date>=NOW() and NOW()>=release_date and country_id=:country_id', 
			['position'=>$position, 'country_id'=>$country_id]);
			if(Yii::$app->user->identity){
				Yii::$app->user->identity->addAdvertisingFilterConditions($query);
			}
			$rows=$query->all();
			$rowsCount=count($rows);
			
		}else{	
			$query = Advertising::find()->distinct()
            ->innerJoin('advertising_interest','advertising_interest.advertising_id=advertising.id')
			->orderBy(['id'=>SORT_DESC])->offset(($pageNum-1)*$perPage)->limit($perPage);		
			$query->where('advertising_position=:position and status=1 and display_date>=NOW() and NOW()>=release_date and country_id=:country_id', 
			['position'=>$position, 'country_id'=>$country_id]);
			if(Yii::$app->user->identity){
				Yii::$app->user->identity->addAdvertisingFilterConditions($query);
			}
			$model=$query->all();
			
			$query = Advertising::find()->distinct()
            ->innerJoin('advertising_interest','advertising_interest.advertising_id=advertising.id')
			->orderBy(['id'=>SORT_DESC]);		
			$query->where('advertising_position=:position and status=1 and display_date>=NOW() and NOW()>=release_date and country_id=:country_id', 
			['position'=>$position, 'country_id'=>$country_id]);
			if(Yii::$app->user->identity){
				Yii::$app->user->identity->addAdvertisingFilterConditions($query);
			}
			$rows=$query->all();
			$rowsCount=count($rows);
		}
		if($model){
			return $data = array('model'=>$model, 'rowsCount' => $rowsCount);
		}
	}
	
	public function getAdvertisingDashboard($position = '' , $provider = '',$pageNum=1,$perPage=null, $country_id = 0){

		if($provider != ''){	
			$query = Advertising::find()->distinct()
            ->innerJoin('advertising_interest','advertising_interest.advertising_id=advertising.id')
			->orderBy(['id'=>SORT_DESC])->offset(($pageNum-1)*$perPage)->limit($perPage);		
			$query->where('advertising_position=:position and provider=:provider and status=1 and display_date>=NOW() and NOW()>=release_date and country_id=:country_id', 
			['position'=>$position, 'provider'=>$provider, 'country_id'=>$country_id]);
			if(Yii::$app->user->identity){
				Yii::$app->user->identity->addAdvertisingFilterConditions($query);
			}
			
			$model=$query->all();
		}else{	
			$query = Advertising::find()->distinct()
            ->innerJoin('advertising_interest','advertising_interest.advertising_id=advertising.id')
			->orderBy(['id'=>SORT_DESC])->offset(($pageNum-1)*$perPage)->limit($perPage);		
			$query->where('advertising_position=:position and status=1 and display_date>=NOW() and NOW()>=release_date and country_id=:country_id', 
			['position'=>$position, 'country_id'=>$country_id]);
			if(Yii::$app->user->identity){
				Yii::$app->user->identity->addAdvertisingFilterConditions($query);
			}
			$model=$query->all();
		}
		if($model){
			return $model;
		}
	}
	
	public function getSearchRequestParamValues()
    {
        return $this->hasMany('\app\models\SearchRequestParamValue', ['search_request_id' => 'id'])->joinWith('param')->orderBy('param.interest_id asc,param.sort_order asc');
    }
}
