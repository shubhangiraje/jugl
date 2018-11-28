<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * UserSearch represents the model behind the search form about `app\modules\foms\models\User`.
 */
class RegistrationHelpRequestSearch extends RegistrationHelpRequest
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'dt',
                'ip',
                'first_name',
                'last_name',
                'nick_name',
                'company_name',
                'birthday',
                'email',
                'phone',
                'sex',
                'step'
            ], 'safe'],
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
        $query = RegistrationHelpRequest::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder'=>['id'=>SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'nick_name', $this->last_name])
            ->andFilterWhere(['like', 'company_name ', $this->company_name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'step', $this->step])
            ->andFilterWhere(['like', 'email', $this->email]);

            return $dataProvider;
    }
}