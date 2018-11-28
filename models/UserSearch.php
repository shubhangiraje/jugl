<?php

namespace app\models;

use app\components\EDateTime;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserSearch represents the model behind the search form about `app\modules\foms\models\User`.
 */
class UserSearch extends User
{
    public $registration_dt_from;
    public $registration_dt_to;

    public $invited;
    public $invited_by;
    public $uf_age_from;
    public $uf_age_to;
    public $uf_sex;
    public $uf_offer_request_completed_interest_id;
    public $uf_member_from;
    public $uf_member_to;
    public $uf_offers_view_buy_ratio_from;
    public $uf_offers_view_buy_ratio_to;
    public $uf_balance_from;
    public $uf_country_id;
    public $uf_city;
    public $uf_zip;
    public $uf_distance_km;
    public $uf_offer_year_turnover_from;
    public $uf_offer_year_turnover_to;
    public $uf_active_search_requests_from;
    public $uf_messages_per_day_from;
    public $uf_messages_per_day_to;
    public $status_action;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent_id', 'failed_logins'], 'integer'],
            [[
                'invited',
                'invited_by',
                'registration_ip',
                'status',
                'email',
                'first_name',
                'last_name',
                'nick_name',
                'company_name',
                'password',
                'access_token',
                'auth_key',
                'registration_dt_from',
                'registration_dt_to',
                'uf_age_from',
                'uf_age_to',
                'uf_sex',
                'uf_offer_request_completed_interest_id',
                'uf_member_from',
                'uf_member_to',
                'uf_offers_view_buy_ratio_from',
                'uf_offers_view_buy_ratio_to',
                'uf_balance_from',
                'uf_country_id',
                'uf_city',
                'uf_zip',
                'uf_distance_km',
                'uf_offer_year_turnover_from',
                'uf_offer_year_turnover_to',
                'uf_active_search_requests_from',
                'uf_messages_per_day_from',
                'uf_messages_per_day_to',
                'stat_buyed_jugl',
                'status_action',
                'dt_status_change',
                'parent_registration_bonus'
            ], 'safe'],
        ];
    }

    public function attributeLabels() {
        return array_merge(parent::attributeLabels(),[
            'invited'=>Yii::t('app','Einladung erhalten'),
            'invited_by'=>Yii::t('app','Einladung erhalten von'),
            'uf_age_from'=>Yii::t('app','Alter von'),
            'uf_age_to'=>Yii::t('app','Alter bis'),
            'uf_sex'=>Yii::t('app','Sex'),
            'uf_offer_request_completed_interest_id'=>Yii::t('app','Gekaufte Art. Kat.'),
            'uf_member_from'=>Yii::t('app','Mitglied von (Tagen)'),
            'uf_member_to'=>Yii::t('app','Mitglied bis (Tagen)'),
            'uf_offers_view_buy_ratio_from'=>Yii::t('app','Gekaufer Artikel/gelesener Werbung 1: von'),
            'uf_offers_view_buy_ratio_to'=>Yii::t('app','Gekaufer Artikel/gelesener Werbung 1: bis'),
            'uf_balance_from'=>Yii::t('app','Kontostand ab'),
            'uf_country_id'=>Yii::t('app','Land'),
            'uf_city'=>Yii::t('app','Ort'),
            'uf_zip'=>Yii::t('app','Plz'),
            'uf_distance_km'=>Yii::t('app','Umkreis'),
            'uf_offer_year_turnover_from'=>Yii::t('app','Umsatz von'),
            'uf_offer_year_turnover_to'=>Yii::t('app','Umsatz bis'),
            'uf_active_search_requests_from'=>Yii::t('app','Suchanz. Online von'),
            'uf_messages_per_day_from'=>Yii::t('app','Durchschnittswert Nachrichten/24Std. von'),
            'uf_messages_per_day_to'=>Yii::t('app','Durchschnittswert Nachrichten/24Std. bis'),
            'status_action'=>Yii::t('app','Gelöscht Status')
        ]);
    }
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    public static function getStatusActionList() {
        return [
            'USER_STATUS_BLOCKED'=>Yii::t('app','Geblockt'),
            'USER_STATUS_DELETED_ADMIN'=>Yii::t('app','Gelöscht'),
            'USER_VALIDATION_STATUS_SUCCESS'=>Yii::t('app','Frei (Ident. best.)'),
            'USER_STATUS_DELETED_USER'=>Yii::t('app','Selbst gelöscht'),
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find()->joinWith(['parent p','userUsedDevice'])->with(['avatarFile']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes'=>[
                    'status',
                    'registration_dt',
                    'registration_ip',
                    'email',
                    'invitations',
                    'network_size',
                    'first_name',
                    'last_name',
                    'nick_name',
                    'company_name',
                    'balance',
                    'payment_complaints',
                    'stat_buyed_jugl',
                    'balance_token',
                    'registered_by_become_member'=>[
                        'asc'=>['user.registered_by_become_member'=>SORT_ASC,'p.first_name'=>SORT_ASC,'p.last_name'=>SORT_ASC],
                        'desc'=>['user.registered_by_become_member'=>SORT_DESC,'p.first_name'=>SORT_DESC,'p.last_name'=>SORT_DESC],
                    ],
                    'user_used_device.device_uuid',
                    'dt_status_change',
                    'parent_registration_bonus'
                ],
                'defaultOrder'=>['registration_dt'=>SORT_DESC]
            ]
        ]);

        //$query->andWhere('user.status!=:status_deleted',[':status_deleted'=>static::STATUS_DELETED]);

        if (!($this->load($params) && $this->validate())) {
            $this->setSimpleTotalCount($dataProvider,$query);
            return $dataProvider;
        }

        $query->andFilterWhere([
            'user.id' => $this->id,
            'user.parent_id' => $this->parent_id,
            'user.failed_logins' => $this->failed_logins,
        ]);

        if ($this->status) {
            $parts=explode('|',$this->status);
            $query->andFilterWhere(['user.status'=>$parts[0]]);
            if (count($parts)>1) {
                $query->andFilterWhere(['user.packet'=>$parts[1]]);
            }
        }
        if ($this->registration_dt_from!='') {
            $query->andWhere('user.registration_dt>=:dt_from',[
                ':dt_from'=>(new EDateTime($this->registration_dt_from))->sqlDate()
            ]);
        }

        if ($this->registration_dt_to!='') {
            $query->andWhere('user.registration_dt<=:dt_to',[
                ':dt_to'=>(new EDateTime($this->registration_dt_to))->modify('+1 day')->sqlDate()
            ]);
        }

        $query->andFilterWhere(['like', 'user.email', $this->email])
            ->andFilterWhere(['like', 'user.first_name', $this->first_name])
            ->andFilterWhere(['like', 'user.last_name', $this->last_name])
            ->andFilterWhere(['like', 'user.nick_name', $this->nick_name])
            ->andFilterWhere(['like', 'user.company_name', $this->company_name])
            ->andFilterWhere(['like', 'user.password', $this->password])
            ->andFilterWhere(['like', 'user.access_token', $this->access_token])
            ->andFilterWhere(['user.registration_ip'=>$this->registration_ip])
            ->andFilterWhere(['like', 'user.auth_key', $this->auth_key]);


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

        if ($this->invited) {
            $query->andWhere('user.parent_id is not null');
        }

        if ($this->invited_by) {
            $query->innerJoin('user pu','pu.id=user.parent_id')->andWhere('(CONCAT(pu.last_name,\' \',pu.first_name) like (:parent_name) or CONCAT(pu.deleted_last_name,\' \',pu.deleted_first_name) like (:parent_name))',[
                ':parent_name'=>'%'.$this->invited_by.'%'
            ]);
        }

        if($this->status_action) {
            switch ($this->status_action) {
                case 'USER_STATUS_BLOCKED':
                    $query->andWhere(['user.status'=>User::STATUS_BLOCKED]);
                    break;
                case 'USER_STATUS_DELETED_ADMIN':
                    $query->andWhere(['user.status'=>User::STATUS_DELETED, 'user.is_user_profile_delete'=>0]);
                    break;
                case 'USER_VALIDATION_STATUS_SUCCESS':
                    $query->andWhere(['user.status'=>User::STATUS_ACTIVE, 'user.validation_status'=>User::VALIDATION_STATUS_SUCCESS]);
                    break;
                case 'USER_STATUS_DELETED_USER':
                    $query->andWhere(['user.status'=>User::STATUS_DELETED, 'user.is_user_profile_delete'=>1]);
                    break;
            }
        }

        if (!$this->parent_id) {
            $this->setSimpleTotalCount($dataProvider,$query);
        }

        return $dataProvider;
    }

    private function setSimpleTotalCount($dataProvider,$query) {

        $query=clone $query;
        $query->join=[];
        $query->joinWith=[];

        // don't use read locks for long query
        $trx=Yii::$app->db->beginTransaction('READ UNCOMMITTED');
        $totalCount=$query->limit(-1)->offset(-1)->orderBy([])->count('*', $this->db);
        $trx->commit();

        $dataProvider->setTotalCount($totalCount);
    }
}