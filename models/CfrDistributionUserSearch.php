<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CfrDistributionUser;

/**
 * CfrDistributionUserSearch represents the model behind the search form about `app\models\CfrDistributionUser`.
 */
class CfrDistributionUserSearch extends CfrDistributionUser
{

    public $user_name;
    public $user_email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cfr_distribution_id', 'user_id', 'votes_count', 'processed'], 'integer'],
            [['jugl_sum'], 'number'],
            [['user_name','user_email'], 'safe']
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
    public function search($id, $params)
    {
        $query = CfrDistributionUser::find()->joinWith(['user'])->with(['user'])->where(['cfr_distribution_id'=>$id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes'=>[
                    'votes_count',
                    'jugl_sum'
                ],
                'defaultOrder'=>['id'=>SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->user_name!='') {
            $query->andWhere('user.first_name like(:name) or user.last_Name like(:name) or user.company_name like(:name)',[':name'=>'%'.$this->user_name.'%']);
        }

        if ($this->user_email!='') {
            $query->andWhere('user.email like(:email)',[':email'=>'%'.$this->user_email.'%']);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'votes_count' => $this->votes_count,
            'jugl_sum' => $this->jugl_sum
        ]);



        return $dataProvider;
    }
}
