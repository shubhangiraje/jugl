<?php

namespace app\models;

use app\components\EDateTime;
use Yii;
use yii\db\Query;

class OfferActiveQuery extends \yii\db\ActiveQuery
{
    public function init()
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->andWhere(['!=',"$tableName.status", Offer::STATUS_DELETED]);
        $this->andWhere(['!=',"$tableName.status", Offer::STATUS_UNLINKED]);
        parent::init();
    }

}

class Offer extends \app\models\base\Offer
{
    const STATUS_ACTIVE='ACTIVE';
    const STATUS_EXPIRED='EXPIRED';
    const STATUS_CLOSED='CLOSED';
    const STATUS_DELETED='DELETED';
    const STATUS_UNLINKED='UNLINKED';
    const STATUS_PAUSED='PAUSED';
    const STATUS_AWAITING_VALIDATION='AWAITING_VALIDATION';
    const STATUS_REJECTED='REJECTED';
    const STATUS_SCHEDULED='SCHEDULED';

    const VALIDATION_STATUS_NOT_REQUIRED='NOT_REQUIRED';
    const VALIDATION_STATUS_AWAITING='AWAITING';
    const VALIDATION_STATUS_AWAITING_LATER='AWAITING_LATER';
    const VALIDATION_STATUS_ACCEPTED='ACCEPTED';
    const VALIDATION_STATUS_REJECTED='REJECTED';

    const SEX_M = 'M';
    const SEX_F = 'F';
    const SEX_A = 'A';

    const TYPE_AUCTION='AUCTION';
    const TYPE_AD='AD';
    const TYPE_AUTOSELL='AUTOSELL';

    public $offer_view_bonus;
    public $offer_view_total_bonus;

    const VIEW_BONUS_PERCENT_PARENT=10;
    const VIEW_BONUS_PERCENT_JUGL=10;

    // used in search
    public $relevancy;

    // used in create form
    public $without_view_bonus;
    public $country_counts;
    public $country_counts_empty;

    public $is_active_immediately;

    public static function getUfPacketList() {
        return [
            'VIP'=>Yii::t('app','premium-mitglieder'),
            'VIP_PLUS'=>Yii::t('app','premiumPlus-mitglieder'),
            'STANDART'=>Yii::t('app','basis-mitglieder'),
            'ALL'=>Yii::t('app','alle'),
        ];
    }

    public static function getStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::STATUS_ACTIVE=>Yii::t('app','OFFER_STATUS_ACTIVE'),
                static::STATUS_EXPIRED=>Yii::t('app','OFFER_STATUS_EXPIRED'),
                static::STATUS_CLOSED=>Yii::t('app','OFFER_STATUS_CLOSED'),
                static::STATUS_DELETED=>Yii::t('app','OFFER_STATUS_DELETED'),
                static::STATUS_REJECTED=>Yii::t('app','OFFER_STATUS_REJECTED'),
                static::STATUS_SCHEDULED=>Yii::t('app','Zeitvesetzt'),
            ];
        }

        return $items;
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

    public static function getTypeList() {
        static $items;

        if (!isset($items)) {
            $items=[
				static::TYPE_AD=>Yii::t('app','Ohne Kaufmöglichkeit'),
				static::TYPE_AUTOSELL=>Yii::t('app','Sofortkauf'),
                static::TYPE_AUCTION=>Yii::t('app','Bieterverfahren'),
            ];
        }

        return $items;
    }

    public static function getSexList()
    {
        return [
            static::SEX_M => Yii::t('app', 'Man'),
            static::SEX_F => Yii::t('app', 'Woman'),
            static::SEX_A => Yii::t('app', 'Alle'),
        ];
    }

    public function __toString() {
        return $this->title;
    }

    public function getStatusLabel() {
        return static::getStatusList()[$this->status];
    }

    public static function setExpiredStatus() {
        $offers = static::find()
            ->where('status=:status_active and active_till<=CAST(NOW() AS DATE)', ['status_active'=>static::STATUS_ACTIVE])
            ->all();

        foreach ($offers as $offer) {
            $offer->status = static::STATUS_EXPIRED;
            $offer->save();
        }
/*
        Yii::$app->db->createCommand("update ".static::tableName().' set status=:status_expired where status=:status_active and active_till<=CAST(NOW() AS DATE)',[
            ':status_active'=>static::STATUS_ACTIVE,
            ':status_expired'=>static::STATUS_EXPIRED
        ])->query();
*/

    }

    public static function find()
    {
        return new OfferActiveQuery(get_called_class());
    }

    public function canCreateRequest() {
        if ($this->user_id==Yii::$app->user->id || $this->type==static::TYPE_AD) return false;

        $requests=\app\models\OfferRequest::find()->andWhere([
            'offer_id'=>$this->id,
            'user_id'=>Yii::$app->user->id,
        ])->andWhere('(status=:status_active and bet_active_till>=NOW())',[
            ':status_active'=>\app\models\OfferRequest::STATUS_ACTIVE
        ])->count();

        $maxRequests=1;
        if ($this->amount) $maxRequests=$this->amount;
        if ($this->type==static::TYPE_AUTOSELL) $maxRequests=1;

        return $requests<$maxRequests;
    }

    public function canUpdateViewBonusTotal() {
        if ($this->status==static::STATUS_ACTIVE) return true;

        $now=new \app\components\EDateTime();

        return $this->status==static::STATUS_EXPIRED &&
            ($this->amount===null || $this->amount>0);// &&
            //$now->sqlDate()<=$this->active_till;
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'notify_if_price_bigger' => Yii::t('app','Benachrichtigungen bei Geboten ab'),
            'create_dt'=>Yii::t('app','Erfasst am'),
            'active_till'=>Yii::t('app','Aktiv bis'),
            'user_id' => Yii::t('app','User ID'),
            'title' => Yii::t('app','Titel der Anzeige'),
            'description' => Yii::t('app','Beschreibung'),
            'price' => $this->type==static::TYPE_AUTOSELL ? Yii::t('app','Preis'):Yii::t('app','Preisvorstellung'),
            'view_bonus' => Yii::t('app','Werbebonus pro User'),
            'view_bonus_total' => Yii::t('app','Min. Budget für Werbeaktion'),
            'view_bonus_used' => Yii::t('app','Verwendet Budget für Werbebonus'),
            'buy_bonus' => Yii::t('app','Kaufbonus pro User'),
            'country_id' => Yii::t('app','Land'),
            'zip' => Yii::t('app','PLZ'),
            'city' => Yii::t('app','Ort'),
            'address' => Yii::t('app','Straße/Nr.'),
            'pay_allow_bank'=>Yii::t('app','Zahlung per Banküberweisung'),
            'pay_allow_paypal'=>Yii::t('app','Zahlung per Paypal'),
            'pay_allow_jugl'=>Yii::t('app','Zahlung mit Jugls'),
            'pay_allow_pod'=>Yii::t('app','Barzahlung bei Abholung'),
            'delivery_days' => Yii::t('app','Lieferzeit ab Geldeingang'),
            'uf_enabled'=>Yii::t('app','Nutzerkreis weiter einschränken'),
            'uf_packet'=>Yii::t('app','Midglieder'),
            'uf_balance_from'=>Yii::t('app','Kontostand ab'),
            'uf_age_from'=>Yii::t('app','Alter von'),
            'uf_age_to'=>Yii::t('app','Alter bis'),
            'uf_sex'=>Yii::t('app','Sex'),
            'uf_offer_request_completed_interest_id'=>Yii::t('app','Gekaufte Artikel Kategorie'),
            'uf_member_from'=>Yii::t('app','Mitglied seit (in Tagen) von'),
            'uf_member_to'=>Yii::t('app','Mitglied seit (in Tagen) von'),
            'uf_offers_view_buy_ratio_from'=>Yii::t('app','Verhältnis gekaufer Artikel zu gelesener Werbung (Kaufbonus erhalten) 1: von'),
            'uf_offers_view_buy_ratio_to'=>Yii::t('app','Verhältnis gekaufer Artikel zu gelesener Werbung (Kaufbonus erhalten) 1: bis'),
            'uf_balance_from'=>Yii::t('app','Kontostand ab'),
            'uf_country_id'=>Yii::t('app','Land'),
            'uf_city'=>Yii::t('app','Ort'),
            'uf_zip'=>Yii::t('app','Plz'),
            'uf_distance_km'=>Yii::t('app','Umkreis'),
            'uf_offer_year_turnover_from'=>Yii::t('app','Umsatz in &euro; von'),
            'uf_offer_year_turnover_to'=>Yii::t('app','Umsatz in &euro; bis'),
            'uf_active_search_requests_from'=>Yii::t('app','Anzahl Suchanzeigen Online (Min)'),
            'uf_messages_per_day_from'=>Yii::t('app','Durchschnittswert Nachrichten User pro 24Std. von'),
            'uf_messages_per_day_to'=>Yii::t('app','Durchschnittswert Nachrichten User pro 24Std. bis'),
            'amount'=>Yii::t('app','Stückzahl'),
            'show_amount'=>Yii::t('app','Stückzahl anzeigen'),
            'delivery_cost'=>Yii::t('app','Versandkosten'),
            'type'=>Yii::t('app','Typ der Werbung'),
            'allow_contact'=>Yii::t('app','Nachrichten zulassen'),
            'without_view_bonus'=>Yii::t('app','Ohne Werbebonus'),
            'validation_status'=>Yii::t('app', 'Kontrollstatus'),
            'receivers_count'=>Yii::t('app', 'Sichtbar für User'),
            'scheduled_dt'=>Yii::t('app', 'Datum der Veröffentlichung'),

        ];
    }

    public function rules() {
        return array_merge(parent::rules(),[
            [['buy_bonus','zip','country_id','delivery_days', 'amount'],'required','on'=>['saveAuction','saveAutoSell']],
            [['type'], 'required', 'on'=>['saveAuction','saveAutoSell','saveAd'], 'message'=>Yii::t('app', 'Bitte wähle eine Art der Werbung aus.')],
            [['price'], 'required', 'on'=>['saveAutoSell'], 'message'=>Yii::t('app', 'Bitte gib einen Preis ein.')],
            [['price'], 'required', 'on'=>['saveAuction'], 'message'=>Yii::t('app', 'Bitte gib einen Preisvorstellung ein.')],
            [['notify_if_price_bigger'], 'required', 'on'=>['saveAuction'], 'message'=>Yii::t('app', 'Bitte gib einen "Benachrichtigungen bei Geboten ab" ein.')],
            [['active_till'],'date','format'=>'yyyy-MM-dd','message'=>Yii::t('app','Bitte wählen Anzeige aktiv bis Datum')],
            [['price','delivery_days'],'number','min'=>'0'],
            [['buy_bonus'],'number','min'=>'1','on'=>['saveAuction','saveAutoSell','saveAd']],
            [['view_bonus'],'number','min'=>0.5,'on'=>['saveAuction','saveAutoSell','saveAd']],
            [[
                'uf_age_from','uf_age_to','uf_member_from','uf_member_to','uf_offers_view_buy_ratio_from','uf_offers_view_buy_ratio_to',
                'uf_balance_from','uf_distance_km','uf_offer_year_turnover_from','uf_offer_year_turnover_to','uf_active_search_requests_from',
                'uf_messages_per_day_from','uf_messages_per_day_to'
                ],'number','min'=>0],
            [['uf_zip'],'exist','targetClass'=>'\app\models\ZipCoords','targetAttribute'=>'zip'],
            [['pay_allow_bank'],'validatePaymentMethods','skipOnEmpty'=>false,'on'=>['saveAuction','saveAutoSell']],
            ['view_bonus', 'is_offer_view_bonus'],
            ['view_bonus_total', 'is_offer_view_total_bonus'],
            [['view_bonus','view_bonus_total'],'required','when'=>function($model) {
                if (!in_array($model->getScenario(),['saveAutoSell','saveAuction','saveAd','saveAdmin'])) return false;
                return !$this->without_view_bonus;
            }, 'whenClient'=>"function(attribute, value) {
                    return !$('input:checkbox[name=\"Offer[without_view_bonus]\"]').is(':checked');
                }"
            ],
            ['is_active_immediately', 'safe', 'on'=>['saveAuction','saveAutoSell','saveAd']],
            ['scheduled_dt', 'required', 'on'=>['saveAuction','saveAutoSell','saveAd'], 'when'=>function($model) {
                return !$model->is_active_immediately;
            }],
            ['scheduled_dt', 'validateScheduledDt', 'on'=>['saveAuction','saveAutoSell','saveAd'], 'when'=>function($model) {
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

    public function validatePaymentMethods($attribute,$options) {
        if (!$this->pay_allow_bank && !$this->pay_allow_paypal && !$this->pay_allow_jugl && !$this->pay_allow_pod) {
            $this->addError('pay_allow_bank', Yii::t('app', 'Bitte wählen Sie mindestens eine Zahlungsmethode aus.'));
        }

        if ($this->pay_allow_bank && count($this->user->userBankDatas)==0) {
            $this->addError('pay_allow_bank', Yii::t('app', 'Banküberweisung als Zahlungsmethode ist nicht möglich, da Du keine Bankverbindung in Deinem Profil hinterlegt hast. Gehe dazu in der Navigationsleiste auf "Mein Profil" und auf "Daten/Bankdaten ansehen/bearbeiten"'));
        }

        if ($this->pay_allow_paypal && $this->user->paypal_email=='') {
            $this->addError('pay_allow_bank', Yii::t('app', 'Paypal als Zahlungsmethode ist nicht möglich, da Du Deine Paypal E-Mail-Addresse in Deinem Profil nicht hinterlegt hast'));
        }

    }

    public function is_offer_view_total_bonus(){
        if($this->view_bonus_total < $this->view_bonus*(100+static::VIEW_BONUS_PERCENT_JUGL+static::VIEW_BONUS_PERCENT_PARENT)/100){
            $this->addError('view_bonus_total', Yii::t('app', 'Max. Budget für Werbeaktion muss grösser als Werbebonus pro User sein.'));
        }elseif($this->view_bonus_total < $this->offer_view_total_bonus){
            $this->addError('view_bonus_total', Yii::t('app', 'Min. Budget für Werbeaktion muss mindestens {offer_view_total_bonus} Jugl betragen.', ['offer_view_total_bonus'=>$this->offer_view_total_bonus]));
        }
    }

    public function is_offer_view_bonus(){
        if($this->view_bonus < $this->offer_view_bonus){
            $this->addError('view_bonus', Yii::t('app', 'Werbebonus pro User darf nicht kleiner als {offer_view_bonus} sein.', ['offer_view_bonus'=>$this->offer_view_bonus]));
        }
    }

    public function scenarios() {
        $scenarios=parent::scenarios();

        $scenarios['saveAutoSell']=[
            'title','description','price','delivery_days','view_bonus','view_bonus_total','buy_bonus','amount','country_id','zip','city','address','active_till',
            'uf_enabled','uf_age_from','uf_age_to','uf_sex','uf_offer_request_completed_interest_id','uf_member_from','uf_member_to',
            'uf_offers_view_buy_ratio_from','uf_offers_view_buy_ratio_to','uf_balance_from','uf_country_id','uf_city',
            'uf_zip','uf_distance_km','uf_offer_year_turnover_from','uf_offer_year_turnover_to','uf_active_search_requests_from',
            'uf_messages_per_day_from','uf_messages_per_day_to','pay_allow_bank','pay_allow_paypal','pay_allow_jugl','pay_allow_pod',
            'amount','show_amount','delivery_cost','type','allow_contact','without_view_bonus','uf_packet','is_active_immediately','scheduled_dt'
        ];

        $scenarios['saveAuction']=[
            'title','description','price','delivery_days','view_bonus','view_bonus_total','buy_bonus','amount','country_id','zip','city','address','active_till',
            'uf_enabled','uf_age_from','uf_age_to','uf_sex','uf_offer_request_completed_interest_id','uf_member_from','uf_member_to',
            'uf_offers_view_buy_ratio_from','uf_offers_view_buy_ratio_to','uf_balance_from','uf_country_id','uf_city',
            'uf_zip','uf_distance_km','uf_offer_year_turnover_from','uf_offer_year_turnover_to','uf_active_search_requests_from',
            'uf_messages_per_day_from','uf_messages_per_day_to','pay_allow_bank','pay_allow_paypal','pay_allow_jugl','pay_allow_pod',
            'amount','show_amount','delivery_cost','type','allow_contact','notify_if_price_bigger','without_view_bonus','uf_packet',
            'is_active_immediately','scheduled_dt'
        ];

        $scenarios['saveAd']=[
            'title','description','view_bonus','view_bonus_total','active_till',
            'uf_enabled','uf_age_from','uf_age_to','uf_sex','uf_offer_request_completed_interest_id','uf_member_from','uf_member_to',
            'uf_offers_view_buy_ratio_from','uf_offers_view_buy_ratio_to','uf_balance_from','uf_country_id','uf_city',
            'uf_zip','uf_distance_km','uf_offer_year_turnover_from','uf_offer_year_turnover_to','uf_active_search_requests_from',
            'uf_messages_per_day_from','uf_messages_per_day_to',
            'type','allow_contact','without_view_bonus','uf_packet',
            'is_active_immediately','scheduled_dt'
        ];

        $scenarios['saveAdmin']=array_merge($scenarios[static::SCENARIO_DEFAULT], [
            'without_view_bonus'
        ]);

        return $scenarios;
    }

    public function getFiles()
    {
        return $this->hasMany('\app\models\File', ['id' => 'file_id'])->viaTable('offer_file', ['offer_id' => 'id'], function($query) {$query->orderBy('sort_order asc');});
    }

    public function getOfferParamValues()
    {
        return $this->hasMany('\app\models\OfferParamValue', ['offer_id' => 'id'])->joinWith('param')->orderBy('param.interest_id asc,param.sort_order asc');
    }


    public function getOfferMyFavorites()
    {
        return $this->hasMany('\app\models\OfferFavorite', ['offer_id' => 'id'])->andWhere(['user_id'=>Yii::$app->user->id]);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferActiveRequests()
    {
        return $this->hasMany('\app\models\OfferRequest', ['offer_id' => 'id'])->andWhere(['offer_request.status'=>[OfferRequest::STATUS_ACTIVE,OfferRequest::STATUS_ACCEPTED]]);
    }

    public function updateUserStats() {
        \app\models\User::updateStatOfferYearTurnover($this->user_id);
        //\app\models\User::updateStatActiveSearchRequests($this->user_id);
    }

    private function addAffectedUsersConditions($query) {
        //if ($this->uf_enabled) {
            if ($this->uf_age_from!='') {
                $query->andWhere('user.birthday<=:birthday_to',[':birthday_to'=>(new EDateTime())->modify('-'.intval($this->uf_age_from).' year')->sqlDate()]);
            }

            if ($this->uf_age_to!='') {
                $query->andWhere('user.birthday>:birthday_from',[':birthday_from'=>(new EDateTime())->modify('-'.intval($this->uf_age_to+1).' year')->sqlDate()]);
            }

            if (in_array($this->uf_sex,[User::SEX_M,User::SEX_F])) {
                $query->andWhere(['user.sex'=>$this->uf_sex]);
            }

            if ($this->uf_offer_request_completed_interest_id) {
                $query->innerJoin('user_offer_request_completed_interest uorci','uorci.user_id=user.id and uorci.interest_id=:uorci_interest_id',[':uorci_interest_id'=>$this->uf_offer_request_completed_interest_id]);
            }

            if ($this->uf_member_from!='') {
                $query->andWhere('user.registration_dt<=:registration_dt_to',[':registration_dt_to'=>(new EDateTime())->modify('-'.intval($this->uf_member_from).' day')->sqlDate()]);
            }

            if ($this->uf_member_to!='') {
                $query->andWhere('user.registration_dt>:registration_dt_from',[':registration_dt_from'=>(new EDateTime())->modify('-'.intval($this->uf_member_to).' day')->sqlDate()]);
            }

            if ($this->uf_offers_view_buy_ratio_from!='') {
                $query->andWhere('user.stat_offers_view_buy_ratio>=:uf_offers_view_buy_ratio_from',[':uf_offers_view_buy_ratio_from'=>$this->uf_offers_view_buy_ratio_from]);
            }

            if ($this->uf_offers_view_buy_ratio_to!='') {
                $query->andWhere('user.stat_offers_view_buy_ratio<=:uf_offers_view_buy_ratio_to',[':uf_offers_view_buy_ratio_to'=>$this->uf_offers_view_buy_ratio_to]);
            }

            if ($this->uf_city!='') {
                $query->andWhere('user.city=:uf_city',[':uf_city'=>$this->uf_city]);
            }

            if ($this->uf_zip!='' && $this->uf_distance_km=='') {
                $query->andWhere('user.zip=:uf_zip',[':uf_zip'=>$this->uf_zip]);
            }

            if ($this->uf_zip!='' && $this->uf_distance_km!='') {
                $dist=ceil($this->uf_distance_km);
                $zip=\app\models\ZipCoords::findOne(['zip'=>$this->uf_zip,'country_id'=>$this->uf_country_id ? $this->uf_country_id:64]);

                if (!$zip) return 0;

                $longitudeFrom = $zip->longitude-$dist/abs(cos($zip->lattitude/180*3.1415926)*69);
                $longitudeTo = $zip->longitude+$dist/abs(cos($zip->lattitude/180*3.1415926)*69);
                $lattitudeFrom = $zip->lattitude-($dist/69);
                $lattitudeTo = $zip->lattitude+($dist/69);

                $query->innerJoin('zip_coords',"zip_coords.zip=user.zip and zip_coords.country_id=user.country_id and
                    lattitude>=:lattitude_from and lattitude<=:lattitude_to and
                    longitude>=:longitude_from and longitude<=:longitude_to and

                    :dist>=3959 * acos( cos( radians(:lattitude) )
                          * cos( radians(zip_coords.lattitude) )
                          * cos( radians(zip_coords.longitude) - radians(:longitude)) + sin(radians(:lattitude))
                          * sin( radians(zip_coords.lattitude) ))
                ",[
                    ':longitude_from'=>$longitudeFrom,
                    ':longitude_to'=>$longitudeTo,
                    ':lattitude_from'=>$lattitudeFrom,
                    ':lattitude_to'=>$lattitudeTo,
                    ':longitude'=>$zip->longitude,
                    ':lattitude'=>$zip->lattitude,
                    ':dist'=>$dist
                ]);

            }

            if ($this->uf_country_id!='') {
                $query->andWhere('user.country_id=:uf_country_id',[':uf_country_id'=>$this->uf_country_id]);
            }

            if ($this->uf_offer_year_turnover_from!='') {
                $query->andWhere('user.stat_offer_year_turnover>=:uf_offer_year_turnover_from',[':uf_offer_year_turnover_from'=>$this->uf_offer_year_turnover_from]);
            }

            if ($this->uf_offer_year_turnover_to!='') {
                $query->andWhere('user.stat_offer_year_turnover<=:uf_offer_year_turnover_to',[':uf_offer_year_turnover_to'=>$this->uf_offer_year_turnover_to]);
            }

            if ($this->uf_active_search_requests_from!='') {
                $query->andWhere('user.stat_active_search_requests>=:uf_active_search_requests_from',[':uf_active_search_requests_from'=>$this->uf_active_search_requests_from]);
            }

            if ($this->uf_messages_per_day_from!='') {
                $query->andWhere('user.stat_messages_per_day>=:uf_messages_per_day_from',[':uf_messages_per_day_from'=>$this->uf_messages_per_day_from]);
            }

            if ($this->uf_messages_per_day_to!='') {
                $query->andWhere('user.stat_messages_per_day<=:uf_messages_per_day_to',[':uf_messages_per_day_to'=>$this->uf_messages_per_day_to]);
            }

            if ($this->uf_balance_from!='') {
                $query->andWhere('user.balance>=:uf_balance_from',[':uf_balance_from'=>$this->uf_balance_from]);
            }

            if ($this->uf_packet!='ALL' && $this->uf_packet!='') {
                $query->andWhere('user.packet=:packet',[':packet'=>$this->uf_packet]);
            }
        //}
    }

    public function getReceiversCount($level1InterestId,$userId) {
/*
        $query=\app\models\User::find()
            ->innerJoin('(select distinct user_id from user_interest where level1_interest_id=:interest_id) as tmp1','tmp1.user_id=user.id',[':interest_id'=>$level1InterestId]);
        $query->andWhere('user.id!=:user_id',[':user_id'=>$userId]);
        $query->andWhere('user.status=:status',[':status'=>\app\models\User::STATUS_ACTIVE]);

        $this->addAffectedUsersConditions($query);

        return $query->count();
*/
        $query=new \yii\db\Query;
        $query->select(['cnt'=>'(count(distinct user.id))'])
            ->from(['user'])
            ->andWhere('user.id!=:user_id',[':user_id'=>$userId])
            ->andWhere('user.status=:status',[':status'=>\app\models\User::STATUS_ACTIVE])
            ->innerJoin('user_interest','user_interest.user_id=user.id and user_interest.level1_interest_id=:interest_id',[':interest_id'=>$level1InterestId]);

        $this->addAffectedUsersConditions($query);

        // don't use read locks for long query
        $trx=Yii::$app->db->beginTransaction('READ UNCOMMITTED');

        $res=intval($query->scalar());

        $trx->commit();

        return $res;
    }
	
	public function getReceiversAllCount($level1InterestId,$level2InterestId,$level3InterestId,$userId) {
        $query=new \yii\db\Query;
		$innerJoin = '';
		if($level2InterestId != 0){
			$innerJoin .= 'and user_interest.level2_interest_id='.$level2InterestId;
		}
		if($level3InterestId != 0){
			$innerJoin .= ' and user_interest.level3_interest_id='.$level3InterestId;
		}
        $query->select(['cnt'=>'(count(distinct user.id))'])
            ->from(['user'])
            ->andWhere('user.id!=:user_id',[':user_id'=>$userId])
            ->andWhere('user.status=:status',[':status'=>\app\models\User::STATUS_ACTIVE])
            ->innerJoin('user_interest','user_interest.user_id=user.id '.$innerJoin.' and user_interest.level1_interest_id=:interest_id',[':interest_id'=>$level1InterestId]);

        $this->addAffectedUsersConditions($query);

        // don't use read locks for long query
        $trx=Yii::$app->db->beginTransaction('READ UNCOMMITTED');

        $res=intval($query->scalar());

        $trx->commit();

        return $res;
    }

    public function returnUnusedBudget() {
        $reserveBonus = $this->view_bonus_total - $this->view_bonus_used;
        if($reserveBonus != 0) {
            $this->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_IN, $reserveBonus, $this->user, Yii::t('app', 'Rückbuchung Werbebudget [offer:{offerId}]"{offerTitle}"[/offer]',['offerId'=>$this->id,'offerTitle'=>$this->title]), true);
        }
        return true;
    }

    public function setClosedDtIfNecessary() {
        if (!in_array($this->status,[static::STATUS_ACTIVE]) && !$this->closed_dt) {
            $this->closed_dt = (new \app\components\EDateTime())->sqlDateTime();
        }

        if (!in_array($this->status,[static::STATUS_ACTIVE,static::STATUS_AWAITING_VALIDATION]) &&
            in_array($this->oldAttributes['status'],[static::STATUS_ACTIVE,static::STATUS_AWAITING_VALIDATION])) {
            $this->returnUnusedBudget();
        }
    }

    public function getAcceptedOfferRequests()
    {
        return $this->hasMany('\app\models\OfferRequest', ['offer_id' => 'id'])->andWhere(['offer_request.status'=>\app\models\OfferRequest::STATUS_ACCEPTED,'offer_request.pay_status'=>OfferRequest::PAY_STATUS_CONFIRMED]);
    }

    public function afterInsert() {
        $this->refresh();

        // get actual level1 interest
        $level1InterestId=static::findOne($this->id)->offerInterests[0]->level1_interest_id;

        $this->receivers_count = $this->getReceiversCount($level1InterestId,$this->user_id);
        $this->save();

        if (!in_array($this->validation_status,[static::VALIDATION_STATUS_ACCEPTED,static::VALIDATION_STATUS_NOT_REQUIRED])) {
            return;
        }
        
        $this->sendFollowerEvent();

        $query=new \yii\db\Query;
        $query->select('user.id')->distinct()
            ->from(['user'])
            ->andWhere('user.id!=:user_id',[':user_id'=>$this->user_id])
            // don't update inactive users
            ->andWhere('stat_new_offers<100')
            ->innerJoin('user_interest','user_interest.user_id=user.id and user_interest.level1_interest_id=:interest_id',[':interest_id'=>$level1InterestId]);


        $this->addAffectedUsersConditions($query);

        $usersIds=$query->column();

        \app\models\User::updateAllCounters(['stat_new_offers'=>1],['id'=>$usersIds]);
        \app\components\ChatServer::statusUpdate($usersIds);

        $this->sendPush();
    }

    public function sendPush() {
        $this->refresh();
        if (count($this->offerInterests)==0) return;
        $query=\app\models\User::find()->distinct()->select('user.id')
            ->innerJoin('user_interest','user_interest.level1_interest_id=:interest_id and user_interest.user_id=user.id',[':interest_id'=>$this->offerInterests[0]->level1_interest_id])
            ->innerJoin('user_device','user_device.user_id=user.id and user_device.setting_notification_all=1 and user_device.setting_notification_offer=1');
        $query->andWhere('user.id!=:user_id and user.status=:active_status',[':user_id'=>$this->user_id,':active_status'=>\app\models\User::STATUS_ACTIVE]);


        $this->addAffectedUsersConditions($query);

        $user_ids=[];
        foreach ($query->asArray()->all() as $user) {
            $user_ids[]=intval($user['id']);
        }

        \app\components\ChatServer::pushMessageExt([
            'user_ids'=> $user_ids,
            'link'=>'view-offers-details.html?id='.$this->id,
            'title'=> \yii\helpers\StringHelper::truncate($this->title,32),
            'text'=> \yii\helpers\StringHelper::truncate($this->description,128),
            'type'=>'offer'
        ]);
    }

    public function beforeInsert() {
		
		$needValidation=false;

		if(\app\models\Setting::get($this->view_bonus>0 ? 'VALIDATE_OFFER_WITH_BONUS':'VALIDATE_OFFER_WITHOUT_BONUS')){
			if(Yii::$app->user->identity->publish_offer_wo_validation){
				$needValidation=false;	
			}else{
				$needValidation=true;	
			}
		}else if(!\app\models\Setting::get($this->view_bonus>0 ? 'VALIDATE_OFFER_WITH_BONUS':'VALIDATE_OFFER_WITHOUT_BONUS')){
			$needValidation=false;	
		}
		else{
			if(Yii::$app->user->identity->publish_offer_wo_validation){
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
        //   $this->sendFollowerEvent();
        //}

        if($this->status==static::STATUS_ACTIVE && $this->oldAttributes['status']!=static::STATUS_ACTIVE && $this->scheduled_dt!==NULL && $this->scheduled_dt > date('Y-m-d H:i:s')) {
            $this->status = static::STATUS_SCHEDULED;
        }

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


    public function addCountOfferView() {
        $this->count_offer_view++;
        $this->save();
    }

    private function sendFollowerEvent() {
        if (in_array($this->validation_status,[static::VALIDATION_STATUS_NOT_REQUIRED,static::VALIDATION_STATUS_ACCEPTED])) {
            \app\models\UserFollowerEvent::addNewOffer($this);
        }
    }

    public static function getCountryList($user_id=null) {
        $squery=new Query();
        $squery->select(['offer.id','offer.country_id'])
            ->from('offer')
            ->innerJoin('offer_interest','offer_interest.offer_id=offer.id')
            ->innerJoin('user','user.id=offer.user_id')
            ->leftJoin('user_interest','user_interest.user_id=:user_id and (
                user_interest.level3_interest_id=offer_interest.level3_interest_id or
                user_interest.level2_interest_id=offer_interest.level2_interest_id or
                user_interest.level1_interest_id=offer_interest.level1_interest_id
                )',[':user_id'=>Yii::$app->user->id])
            ->where('offer.active_till>=CAST(NOW() AS DATE) and offer.status=:active_status',[':active_status'=>Offer::STATUS_ACTIVE])
            ->andWhere('offer.view_bonus>0');

        if($user_id) {
            $squery->andWhere(['offer.user_id'=>$user_id]);
        }

        $squery->groupBy(['offer.id','offer.country_id']);

        Yii::$app->user->identity->addOfferSearchFilterConditions($squery);

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
        $offers = Offer::find()
            ->where(['status'=>static::STATUS_SCHEDULED])
            ->andWhere('scheduled_dt<=:now', [':now'=>(new EDateTime())->sqlDateTime()])
            ->all();

        foreach ($offers as $offer) {
            $trx=Yii::$app->db->beginTransaction();
            $offer->status = static::STATUS_ACTIVE;
            $offer->save();
            $trx->commit();
        }
    }
}

\yii\base\Event::on(Offer::className(), \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE, function ($event) {
    $event->sender->saveBeforeDeletedStatus();
    $event->sender->setClosedDtIfNecessary();
    $event->sender->beforeUpdate();
});

\yii\base\Event::on(Offer::className(), \yii\db\ActiveRecord::EVENT_AFTER_UPDATE, function ($event) {
    $event->sender->updateUserStats();
});

\yii\base\Event::on(Offer::className(), \yii\db\ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
    $event->sender->updateUserStats();
});

\yii\base\Event::on(Offer::className(), \yii\db\ActiveRecord::EVENT_BEFORE_INSERT, function ($event) {
    $event->sender->beforeInsert();
});
