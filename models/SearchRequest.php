<?php

namespace app\models;

use app\components\EDateTime;
use Yii;
use app\models\AdvertisingSearchRequestProvider;
use yii\db\Query;

class SearchRequestActiveQuery extends \yii\db\ActiveQuery
{
    public function init()
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->andWhere(['!=',"$tableName.status", SearchRequest::STATUS_DELETED]);
        $this->andWhere(['!=',"$tableName.status", Offer::STATUS_UNLINKED]);
        parent::init();
    }

}


class SearchRequest extends \app\models\base\SearchRequest
{
    const STATUS_ACTIVE='ACTIVE';
    const STATUS_EXPIRED='EXPIRED';
    const STATUS_CLOSED='CLOSED';
    const STATUS_DELETED='DELETED';
    const STATUS_UNLINKED='UNLINKED';
    const STATUS_AWAITING_VALIDATION='AWAITING_VALIDATION';
    const STATUS_REJECTED='REJECTED';
    const STATUS_SCHEDULED='SCHEDULED';

    const VALIDATION_STATUS_NOT_REQUIRED='NOT_REQUIRED';
    const VALIDATION_STATUS_AWAITING='AWAITING';
    const VALIDATION_STATUS_AWAITING_LATER='AWAITING_LATER';
    const VALIDATION_STATUS_ACCEPTED='ACCEPTED';
    const VALIDATION_STATUS_REJECTED='REJECTED';
	
	const SEARCH_REQUEST_TYPE_STANDART='STANDART';
	const SEARCH_REQUEST_TYPE_EXTERNAL_AD='EXTERNAL_AD';

    // used in search
    public $relevancy;
    public $search_request_bonus;
    public $country_counts;
	public $isAdmin = false;

	public $is_active_immediately;

    public static function getStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::STATUS_ACTIVE=>Yii::t('app','SEARCH_REQUEST_STATUS_ACTIVE'),
                static::STATUS_EXPIRED=>Yii::t('app','SEARCH_REQUEST_STATUS_EXPIRED'),
                static::STATUS_CLOSED=>Yii::t('app','SEARCH_REQUEST_STATUS_CLOSED'),
                static::STATUS_DELETED=>Yii::t('app','SEARCH_REQUEST_STATUS_DELETED'),
                static::STATUS_REJECTED=>Yii::t('app','SEARCH_REQUEST_STATUS_REJECTED'),
                static::STATUS_SCHEDULED=>Yii::t('app','Zeitvesetzt'),
            ];
        }

        return $items;
    }

    public function __toString() {
        return $this->title;
    }

    public function getStatusLabel() {
        return static::getStatusList()[$this->status];
    }

    public static function getValidationStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::VALIDATION_STATUS_NOT_REQUIRED=>Yii::t('app','Nicht erforderlich'),
                static::VALIDATION_STATUS_AWAITING=>Yii::t('app','Offen'),
                static::VALIDATION_STATUS_AWAITING_LATER=>Yii::t('app','Zurückgestellt'),
                static::VALIDATION_STATUS_ACCEPTED=>Yii::t('app','Akzeptiert'),
                static::VALIDATION_STATUS_REJECTED=>Yii::t('app','Abgelehnt'),
            ];
        }

        return $items;
    }

    public function getValidationStatusLabel() {
        return static::getValidationStatusList()[$this->validation_status];
    }
	
	public static function getSearchRequestTypeList() {
        static $items;

        if (!isset($items)) {
            $items=[
				static::SEARCH_REQUEST_TYPE_STANDART=>Yii::t('app','Standart'),
                static::SEARCH_REQUEST_TYPE_EXTERNAL_AD=>Yii::t('app','External Ads'),
            ];
        }

        return $items;
    }

    public function getSearchRequestTypeLabel() {
        return static::getSearchRequestTypeList()[$this->search_request_type];
    }
	
	
	public static function getProviderList() {
        static $items;

		$items=array(0 => 'Keine Angabe');
		$model = AdvertisingSearchRequestProvider::find()->all();
		
		if($model){
			foreach($model as $value){
				$items[$value->provider_id]=$value->provider_name;
			
			}
		}		
        return $items;
    }


    public static function find()
    {
        return new SearchRequestActiveQuery(get_called_class());
    }

    public static function setExpiredStatus() {
        // close expired deals that have accepted offers
        Yii::$app->db->createCommand("update ".static::tableName().' sr join search_request_offer sro on (sro.search_request_id=sr.id and sro.status="ACCEPTED") set sr.status=:status_closed where sr.status=:status_active and active_till<=CAST(NOW() AS DATE)',[
            ':status_active'=>static::STATUS_ACTIVE,
            ':status_closed'=>static::STATUS_CLOSED
        ])->query();

        // expire other expired deals
        Yii::$app->db->createCommand("update ".static::tableName().' set status=:status_expired where status=:status_active and active_till<=CAST(NOW() AS DATE)',[
            ':status_active'=>static::STATUS_ACTIVE,
            ':status_expired'=>static::STATUS_EXPIRED
        ])->query();
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','User ID'),
            'title' => Yii::t('app','Titel der Anzeige'),
            'provider_id' => Yii::t('app','Provider ID'),
            'description' => Yii::t('app','Beschreibung'),
            'price_from' => Yii::t('app','Preis von'),
            'price_to' => Yii::t('app','Preis bis'),
            'bonus' => Yii::t('app','Vermittlungsbonus'),
            'country_id' => Yii::t('app', 'Land'),
            'zip' => Yii::t('app','PLZ'),
            'city' => Yii::t('app','Ort'),
            'address' => Yii::t('app','Straße/Nr.'),
            'active_till' => Yii::t('app','Anzeige bis'),
            'validation_status'=>Yii::t('app','Kontrollstatus'),
            'create_dt'=>Yii::t('app','Erfasst am'),
			'feedback_text_de' => Yii::t('app','Bewertungstext'),
            'feedback_text_en' => Yii::t('app','Bewertungstext'),
            'feedback_text_ru' => Yii::t('app','Bewertungstext'),
            'scheduled_dt'=>Yii::t('app', 'Datum der Veröffentlichung'),
        ];
    }

    public function rules() {
        return array_merge(static::cleanPriceFromRules(parent::rules()),[
            [['active_till'],'date','format'=>'yyyy-MM-dd','message'=>Yii::t('app','Bitte wählen Anzeige aktiv bis Datum')],
            [['price_from'], 'required', 'message'=>Yii::t('app', 'Bitte gib einen Preis ein.')],
            [['price_from','price_to'],'number','min'=>'0'],
            [['bonus'],'number','min'=>'1'],
            [['price_from'],'is_price'],
            ['bonus', 'is_search_request_bonus'],
			['isAdmin', 'required', 'on' => 'saveAdmin'],
			[['provider_id', 'feedback_text_de'], 'checkIfType'],
            ['is_active_immediately', 'safe', 'on'=>'save'],
            ['scheduled_dt', 'required', 'on'=>'save', 'when'=>function($model) {
                return !$model->is_active_immediately;
            }],
            ['scheduled_dt', 'validateScheduledDt', 'on'=>'save', 'when'=>function($model) {
                return !$model->is_active_immediately;
            }]
        ]);
    }

    public function validateScheduledDt() {
        if (!EDateTime::createFromFormat('Y-m-d H:i:s', $this->scheduled_dt)) {
            $this->addError('scheduled_dt', Yii::t('app', 'Ungültiges Format beim "Datum der Veröffentlichung"'));
        } else {
            if ($this->scheduled_dt < date('Y-m-d H:i:s')) {
                $this->addError('scheduled_dt', Yii::t('app','Das eingegebene "Datum der Veröffentlichung" darf nicht in der Vergangenheit liegen'));
            }
        }
    }

	public function checkIfType(){
		if($this->search_request_type == static::SEARCH_REQUEST_TYPE_EXTERNAL_AD){
			
			/*if(empty($this->feedback_text_de)){
				$this->addError('feedback_text_de', Yii::t('app', 'Bitte gib einen Bewertungstext ein.'));
			}*/
			if(empty($this->provider_id)){
				$this->addError('provider_id', Yii::t('app', 'Bitte wähle einen Provider aus.'));
			}
		}
	}
	
    public static function cleanPriceFromRules($rules) {
        foreach ($rules as $key=>$rule) {
            if($rule[1] == 'required') {
                $keyRule = $key;
                $keyDel = array_search('price_from', $rules[$keyRule][0]);
                unset($rules[$keyRule][0][$keyDel]);
            }
        }

        return $rules;
    }
	
	public function checkDescriptionIfExternalAd() {
        if($this->provider_id){
			$providerCheck;
			if($this->provider_id==1){
				$providerCheck="tradetracker";				
			}
			if($this->provider_id==2){
				$providerCheck="cashface";	
			}
			if(!preg_match('/'.$providerCheck.'/',$this->description)){
				$this->addError('description','In der Beschreibung muss der Link des oben angegebenen Anbieters der Werbung hinterlegt sein!');
			}
		}
        
    }

    public function is_price(){
        if(!empty($this->price_to)) {
            if($this->price_from > $this->price_to){
                $this->addError('price_from', Yii::t('app', 'Bitte kontrolliere die Preise, die Du eingegeben hast'));
            }
        }
    }

    public function is_search_request_bonus(){
        if($this->bonus < $this->search_request_bonus){
            $this->addError('bonus', Yii::t('app', 'Vermittlungsbonus darf nicht kleiner als {search_request_bonus} sein.', ['search_request_bonus'=>$this->search_request_bonus]));
        }
    }


    public function scenarios() {
        $scenarios=parent::scenarios();

        $scenarios['save']=[
            'title','description','price_from','price_to','bonus', 'country_id', 'zip','city','address','active_till', 'search_request_type', 'scheduled_dt', 'is_active_immediately'
        ];
		
		 $scenarios['saveAdmin']=[
            'title','description','price_from','price_to','bonus', 'country_id', 'zip','city','address','active_till', 'isAdmin', 'search_request_type', 'provider_id','feedback_text_de'
        ];
		
        return $scenarios;
    }

    public function getFiles()
    {
        return $this->hasMany('\app\models\File', ['id' => 'file_id'])->viaTable('search_request_file', ['search_request_id' => 'id'], function($query) {$query->orderBy('sort_order asc');});
    }

    public function getSearchRequestParamValues()
    {
        return $this->hasMany('\app\models\SearchRequestParamValue', ['search_request_id' => 'id'])->joinWith('param')->orderBy('param.interest_id asc,param.sort_order asc');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestActiveOffers()
    {
        return $this->hasMany('\app\models\SearchRequestOffer', ['search_request_id' => 'id'])->andWhere(['search_request_offer.status'=>[SearchRequestOffer::STATUS_NEW,SearchRequestOffer::STATUS_CONTACTED,SearchRequestOffer::STATUS_REJECTED,SearchRequestOffer::STATUS_ACCEPTED]]);
    }

    public function getAcceptedSearchRequestOffers()
    {
        return $this->hasMany('\app\models\SearchRequestOffer', ['search_request_id' => 'id'])->andWhere(['search_request_offer.status'=>SearchRequestOffer::STATUS_ACCEPTED]);
    }

    public function getSearchRequestMyFavorites()
    {
        return $this->hasMany('\app\models\SearchRequestFavorite', ['search_request_id' => 'id'])->andWhere(['user_id'=>Yii::$app->user->id]);
    }

    public function setClosedDtIfNecessary() {
        if (!in_array($this->status,[static::STATUS_ACTIVE]) && !$this->closed_dt) {
            $this->closed_dt=(new \app\components\EDateTime())->sqlDateTime();
        }
    }

    public function afterInsert() {
        $this->refresh();

        if (!in_array($this->validation_status,[static::VALIDATION_STATUS_ACCEPTED,static::VALIDATION_STATUS_NOT_REQUIRED])) {
            return;
        }

        $this->sendFollowerEvent();

        $usersIds=Yii::$app->db->createCommand("
            select distinct ui.user_id
            from search_request_interest sri
            join user_interest ui on (ui.level1_interest_id=sri.level1_interest_id)
            join user u on (u.id=ui.user_id and u.stat_new_offers<100)
            where sri.search_request_id=:search_request_id and ui.user_id!=:user_id
        ",[
            ':search_request_id'=>$this->id,
            ':user_id'=>$this->user_id
        ])->queryColumn();
        \app\models\User::updateAllCounters(['stat_new_search_requests'=>1],['id'=>$usersIds]);
        \app\components\ChatServer::statusUpdate($usersIds);
        $this->sendPush();
    }

    public function sendPush() {
        $usersIds=Yii::$app->db->createCommand("
            select distinct ui.user_id
            from search_request_interest sri
            join user_interest ui on (ui.level1_interest_id=sri.level1_interest_id)
            join user_device ud on (ud.user_id=ui.user_id and ud.setting_notification_all=1 and ud.setting_notification_search_request=1)
            where sri.search_request_id=:search_request_id and ui.user_id!=:user_id
        ",[
            ':search_request_id'=>$this->id,
            ':user_id'=>$this->user_id
        ])->queryColumn();

        \app\components\ChatServer::pushMessageExt([
            'user_ids'=> $usersIds,
            'link'=>'view-searches-details.html?id='.$this->id,
            'title'=> \yii\helpers\StringHelper::truncate($this->title,32),
            'text'=> \yii\helpers\StringHelper::truncate($this->description,128),
            'type'=>'search_request'
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSearchRequestMyOffers()
    {
        return $this->hasMany('\app\models\SearchRequestOffer', ['search_request_id' => 'id'])
            ->andWhere(['search_request_offer.user_id'=>Yii::$app->user->id])
            ->orderBy('search_request_offer.id');
    }

    public function beforeInsert() {
        $needValidation=false;
		
		if(\app\models\Setting::get('VALIDATE_SEARCH_REQUEST')){
			if(Yii::$app->user->identity->publish_search_request_wo_validation){
				$needValidation=false;	
			}else{
                $needValidation = true;

                if(Yii::$app->user->identity->packet == \app\models\User::PACKET_VIP) {
                    if (\app\models\Setting::get('ACCEPTED_AUTO_OFFER_AND_SEARCH_REQUEST_FOR_VIP') == 1) {
                        $needValidation = false;
                    }
                }

                if(Yii::$app->user->identity->packet == \app\models\User::PACKET_VIP_PLUS) {
                    if (\app\models\Setting::get('ACCEPTED_AUTO_OFFER_AND_SEARCH_REQUEST_FOR_VIP_PLUS') == 1) {
                        $needValidation = false;
                    }
                }

                if(Yii::$app->user->identity->packet == \app\models\User::PACKET_STANDART) {
                    if (\app\models\Setting::get('ACCEPTED_AUTO_OFFER_AND_SEARCH_REQUEST_FOR_STANDART') == 1) {
                        $needValidation = false;
                    }
                }
			}
		}else {
			$needValidation=false;	
		}

        if ($needValidation) {
            $this->status=static::STATUS_AWAITING_VALIDATION;
            $this->validation_status=static::VALIDATION_STATUS_AWAITING;
        }

        if((!$this->status || $this->status==static::STATUS_ACTIVE) && $this->scheduled_dt) {
            $this->status = static::STATUS_SCHEDULED;
        }
    }

    public function beforeUpdate() {
        // allow delete
        if ($this->status==static::STATUS_DELETED) {
            return;
        }

        // don't allow set ACTIVE if validation needed
        if ($this->status==static::STATUS_ACTIVE && !in_array($this->validation_status,[static::VALIDATION_STATUS_NOT_REQUIRED,static::VALIDATION_STATUS_ACCEPTED])) {
            $this->status=$this->oldAttributes['status'];
        }

        //if ( !in_array($this->oldAttributes['validation_status'],[static::VALIDATION_STATUS_NOT_REQUIRED,static::VALIDATION_STATUS_ACCEPTED])) {
        //    $this->sendFollowerEvent();
        //}


        if($this->status==static::STATUS_ACTIVE && $this->oldAttributes['status']!=static::STATUS_ACTIVE && $this->scheduled_dt!==NULL && $this->scheduled_dt > date('Y-m-d H:i:s')) {
            $this->status = static::STATUS_SCHEDULED;
        }

    }

    public function level1InterestSpamReportsLimitReached($level1InterestId) {
        $count=Yii::$app->db->createCommand("
                select count(distinct sri.search_request_id)
                from user_spam_report usr
                join search_request_interest sri on (sri.search_request_id=usr.search_request_id and sri.level1_interest_id=:level1_interest_id)
                where usr.second_user_id=:user_id and usr.is_active=1
            ",[
                ':user_id'=>$this->user_id,
                ':level1_interest_id'=>$level1InterestId
            ]
        )->queryScalar();

        return $count>=3;
    }

    public function saveBeforeDeletedStatus() {
        if ($this->status==static::STATUS_DELETED &&
            !in_array($this->oldAttributes['status'],[static::STATUS_DELETED,static::STATUS_UNLINKED])) {
            $this->status_before_deleted=$this->oldAttributes['status'];
            $this->closed_dt_before_deleted=$this->closed_dt;
            $this->closed_dt=(new \app\components\EDateTime())->sqlDateTime();
        }
    }

    public function undelete() {
        if ($this->status==static::STATUS_DELETED) {
            $this->status=$this->status_before_deleted;
            $this->status_before_deleted=null;
            $this->closed_dt=$this->closed_dt_before_deleted;
            $this->closed_dt_before_deleted=null;
            $this->save();
            return true;
        }
        return false;
    }

    public function deleteUnlink() {
        if ($this->status==static::STATUS_DELETED) {
            $this->status=static::STATUS_UNLINKED;
            $this->save();
            return true;
        }
        return false;
    }

    private function sendFollowerEvent() {
        if (in_array($this->validation_status,[static::VALIDATION_STATUS_NOT_REQUIRED,static::VALIDATION_STATUS_ACCEPTED])) {
            \app\models\UserFollowerEvent::addNewSearchRequest($this);
        }
    }


    public static function getCountryList($user_id=null) {
        $squery=new Query();
        $squery->select(['search_request.id','search_request.country_id'])
            ->from('search_request')
            ->innerJoin('search_request_interest','search_request_interest.search_request_id=search_request.id')
            ->innerJoin('user','user.id=search_request.user_id')
            ->leftJoin('user_interest','user_interest.user_id=:user_id and (
                user_interest.level3_interest_id=search_request_interest.level3_interest_id or
                user_interest.level2_interest_id=search_request_interest.level2_interest_id or
                user_interest.level1_interest_id=search_request_interest.level1_interest_id
                )',[':user_id'=>Yii::$app->user->id])
            ->where('search_request.active_till>=CAST(NOW() AS DATE) and search_request.status=:active_status',[':active_status'=>SearchRequest::STATUS_ACTIVE]);

        if($user_id) {
            $squery->andWhere(['search_request.user_id'=>$user_id]);
        }

        $squery->groupBy(['search_request.id','search_request.country_id']);

        $query=(new Query())
            ->select(['COUNT(id) as count', 'country_id'])
            ->from(['items'=>$squery])
            ->groupBy(['country_id'])
            ->all();

        $countryCountData = [];
        foreach ($query as $item) {
            if($item['country_id']){
                $countryCountData[$item['country_id']]=intval($item['count']);
            } else {
                $countryCountData['no_country']=intval($item['count']);
            }
        }

        $data = [];
        foreach (Country::getList() as $country_id=>$country_name) {
            $idata['id']=$country_id;

            if($countryCountData[$country_id] || $countryCountData['no_country']>0) {
                $idata['name']=$country_name.' ('.($countryCountData[$country_id] + $countryCountData['no_country']).')';
            } else {
                $idata['name']=$country_name.' (0)';
            }

            $idata['flag']=Country::getListShort()[$country_id];
            $data[]=$idata;
        }

        return $data;
    }


    public static function updateStatusScheduled() {
        $searchRequests = SearchRequest::find()
            ->where(['status'=>static::STATUS_SCHEDULED])
            ->andWhere('scheduled_dt<=:now', [':now'=>(new EDateTime())->sqlDateTime()])
            ->all();

        foreach ($searchRequests as $searchRequest) {
            $trx=Yii::$app->db->beginTransaction();
            $searchRequest->status = static::STATUS_ACTIVE;
            $searchRequest->save();
            $trx->commit();
        }
    }



}

\yii\base\Event::on(SearchRequest::className(), \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE, function ($event) {
    $event->sender->saveBeforeDeletedStatus();
    $event->sender->setClosedDtIfNecessary();
    $event->sender->beforeUpdate();
});

\yii\base\Event::on(SearchRequest::className(), \yii\db\ActiveRecord::EVENT_BEFORE_INSERT, function ($event) {
    $event->sender->beforeInsert();
});
