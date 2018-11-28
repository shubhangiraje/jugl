<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * AdminSearch represents the model behind the search form about `app\modules\foms\models\Admin`.
 */
class UserDeletedSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deleted_email', 'deleted_first_name', 'deleted_last_name', 'is_user_profile_delete'], 'safe'],
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
        $query = User::find()->andWhere(['status'=>User::STATUS_DELETED]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder'=>['deleted_dt'=>SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'is_user_profile_delete' => $this->is_user_profile_delete
        ]);

        $query->andFilterWhere(['like', 'deleted_email', $this->deleted_email])
            ->andFilterWhere(['like', 'deleted_first_name', $this->deleted_first_name])
            ->andFilterWhere(['like', 'deleted_last_name', $this->deleted_last_name]);

        return $dataProvider;
    }
}