<?php

namespace app\models;

use app\components\EDateTime;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TrollboxMessageVote;
use yii\data\Pagination;

/**
 * UserSearch represents the model behind the search form about `app\modules\foms\models\User`.
 */
class TrollboxMessageVoteSearch extends TrollboxMessageVote {

    public $create_dt_from;
    public $create_dt_to;
    public $user_email;
    public $user_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_dt_from','create_dt_to','user_email','user_name','user_id','dt'], 'safe']
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
    public function search($trollbox_message_id, $params, $vote=null)
    {
        $query = TrollboxMessageVote::find()
            ->joinWith(['user','trollboxMessage'])
            ->where(['trollbox_message_id'=>$trollbox_message_id]);

        if ($vote) {
            $query->andWhere(['vote'=>$vote]);
        }

        $pagination = new Pagination([
            'defaultPageSize' => 30,
            'totalCount' => $query->count(),
        ]);

        $query = $query->offset($pagination->offset)
            ->limit($pagination->limit);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination,
            'sort' => [
                'defaultOrder' => [
                    'dt' => SORT_DESC,
                ]
            ],
        ]);


        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->create_dt_from!='') {
            $query->andWhere('dt>=:dt_from',[
                ':dt_from'=>(new EDateTime($this->create_dt_from))->sqlDate()
            ]);
        }

        if ($this->create_dt_to!='') {
            $query->andWhere('dt<=:dt_to',[
                ':dt_to'=>(new EDateTime($this->create_dt_to))->modify('+1 day')->sqlDate()
            ]);
        }

        if ($this->user_name!='') {
            $query->andWhere('user.first_name like(:name) or user.last_Name like(:name) or user.company_name like(:name)',[':name'=>'%'.$this->user_name.'%']);
        }

        if ($this->user_email!='') {
            $query->andWhere('user.email=:email ',[':email'=>$this->user_email]);
        }

        return $dataProvider;
    }
}