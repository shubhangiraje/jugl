<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UserFeedback;

/**
 * UserFeedbackSearch represents the model behind the search form about `app\models\UserFeedback`.
 */
class UserFeedbackSearch extends UserFeedback
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'second_user_id', 'rating'], 'integer'],
            [['feedback', 'create_dt', 'response', 'response_dt'], 'safe'],
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
     * @param $user_id
     * @return ActiveDataProvider
     */
    public function search($user_id, $params)
    {
        $query = UserFeedback::find()->where(['user_id'=>$user_id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes'=>[
                    'id',
                    'create_dt',
                    'rating'
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

        return $dataProvider;
    }
}
