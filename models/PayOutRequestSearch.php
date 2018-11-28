<?php

namespace app\models;

use app\components\EDateTime;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PayOutRequest;

/**
 * UserSearch represents the model behind the search form about `app\modules\foms\models\User`.
 */
class PayOutRequestSearch extends PayOutRequest
{
    public $user_name;
    public $packet;

    public function rules()
    {
        return [
            [['status','type','payment_method','user_name','packet'], 'safe'],
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
    public function search($params)
    {
        $query = parent::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes'=>[
                    'dt',
                    'type',
                    'status',
                    'payment_method',
                    'currency_sum',
                    'user.email',
                    'user.balance',
                    'user.balance_buyed',
                    'user.balance_earned',
                    'user.packet',
                    'user.balance_token_deposit_percent',
                    'jugl_sum',
                    'pay_out_method_num'
                ],
                'defaultOrder'=>['dt'=>SORT_ASC]
            ]
        ]);

        $query->joinWith('user');

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
              'pay_out_request.status'=>$this->status,
              'pay_out_request.type'=>$this->type,
              'payment_method'=>$this->payment_method,
              'user.packet'=>$this->packet
        ]);

        if ($this->user_name!='') {
            $query->andWhere('user.first_name like(:name) or user.last_Name like(:name) or user.company_name like(:name)',[':name'=>'%'.$this->user_name.'%']);
        }

        return $dataProvider;
    }
}