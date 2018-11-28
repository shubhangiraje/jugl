<?php

namespace app\models;

use app\components\EDateTime;
use app\models\Offer;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form about `app\modules\foms\models\User`.
 */
class OfferSearch extends Offer
{
    public $create_dt_from;
    public $create_dt_to;
    public $user_name;
    public $active_till_from;
    public $active_till_to;
    public $level1_interest_id;

    public $on_view_bonus;
    public $off_view_bonus;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_dt_from','create_dt_to','status','user_name','title','active_till_from','active_till_to','level1_interest_id','validation_status','on_view_bonus','off_view_bonus'], 'safe']
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
        $query = Offer::find()->joinWith(['user','offerInterests'])->with(['user','offerInterests.level1Interest'])->where('');

        if($validation) {
            $query->andWhere('offer.status!=:status_deleted and offer.status!=:status_unlinked and (offer.validation_status=:status_awaiting or offer.validation_status=:status_awaiting_later)', [
                ':status_deleted'=>Offer::STATUS_DELETED,
                ':status_unlinked'=>Offer::STATUS_UNLINKED,
                ':status_awaiting'=>Offer::VALIDATION_STATUS_AWAITING,
                ':status_awaiting_later'=>Offer::VALIDATION_STATUS_AWAITING_LATER,
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
            'offer.status' => $this->status,
            'offer.view_bonus' => $this->view_bonus,
            'offer.view_bonus_used' => $this->view_bonus_used,
            'offer.view_bonus_total' => $this->view_bonus_total,
            'offer.buy_bonus' => $this->buy_bonus,
            'offer_interest.level1_interest_id'=>$this->level1_interest_id,
            'offer.validation_status' => $this->validation_status,
            'offer.receivers_count' => $this->receivers_count
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

        if($this->on_view_bonus) {
            $query->andWhere('offer.view_bonus>0 and offer.validation_status=:validation_status',[
                'validation_status'=>Offer::VALIDATION_STATUS_AWAITING
            ]);
        }

        if($this->off_view_bonus) {
            $query->andWhere('offer.view_bonus is null and offer.validation_status=:validation_status', [
                'validation_status'=>Offer::VALIDATION_STATUS_AWAITING
            ]);
        }

        return $dataProvider;
    }
}