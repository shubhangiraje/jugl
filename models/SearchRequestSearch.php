<?php

namespace app\models;

use app\components\EDateTime;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SearchRequest;

/**
 * UserSearch represents the model behind the search form about `app\modules\foms\models\User`.
 */
class SearchRequestSearch extends SearchRequest
{
    public $create_dt_from;
    public $create_dt_to;
    public $user_name;
    public $active_till_from;
    public $active_till_to;
    public $level1_interest_id;
    public $status_awaiting;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_dt_from','create_dt_to','status','user_name','title','price_from','price_to','bonus','active_till_from','active_till_to','level1_interest_id','validation_status','status_awaiting'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $validation=false)
    {
        $query = SearchRequest::find()->joinWith(['user','searchRequestInterests'])->with(['user','searchRequestInterests.level1Interest'])->where('');

        if($validation) {
            $query->andWhere('search_request.status!=:status_deleted and search_request.status!=:status_unlinked and (search_request.validation_status=:status_awaiting or search_request.validation_status=:status_awaiting_later)',[
                'status_deleted'=>SearchRequest::STATUS_DELETED,
                'status_unlinked'=>SearchRequest::STATUS_UNLINKED,
                ':status_awaiting'=>SearchRequest::VALIDATION_STATUS_AWAITING,
                ':status_awaiting_later'=>SearchRequest::VALIDATION_STATUS_AWAITING_LATER
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder'=>['create_dt'=>SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'search_request.status' => $this->status,
            'search_request.price_from' => $this->price_from,
            'search_request.price_to' => $this->price_to,
            'search_request.bonus' => $this->bonus,
            'search_request_interest.level1_interest_id'=>$this->level1_interest_id,
            'search_request.validation_status' => $this->validation_status
        ]);

        if ($this->create_dt_from!='') {
            $query->andWhere('create_dt>=:dt_from',[
                ':dt_from'=>(new EDateTime($this->create_dt_from))->sqlDate()
            ]);
        }

        if ($this->create_dt_to!='') {
            $query->andWhere('create_dt<=:dt_to',[
                ':dt_to'=>(new EDateTime($this->create_dt_to))->modify('+1 day')->sqlDate()
            ]);
        }

        if ($this->active_till_from!='') {
            $query->andWhere('active_till>=:dt_from',[
                ':dt_from'=>(new EDateTime($this->active_till_from))->sqlDate()
            ]);
        }

        if ($this->active_till_to!='') {
            $query->andWhere('active_till<=:dt_to',[
                ':dt_to'=>(new EDateTime($this->active_till_to))->modify('+1 day')->sqlDate()
            ]);
        }
        
        if ($this->user_name!='') {
            $query->andWhere('user.first_name like(:name) or user.last_Name like(:name) or user.company_name like(:name)',[':name'=>'%'.$this->user_name.'%']);
        }

        $query->andFilterWhere(['like', 'title', $this->title]);


        return $dataProvider;
    }
}