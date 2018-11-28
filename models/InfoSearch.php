<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Info;
use yii\data\Pagination;

class InfoSearch extends Info {

    public function rules() {
        return [
            [['id'], 'integer'],
            [['view', 'title_de'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params) {
        $query = Info::find();

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
                    'title_de' => SORT_ASC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id
        ]);

        $query->andFilterWhere(['like', 'view', $this->view])
            ->andFilterWhere(['like', 'title_de', $this->title_de]);

        return $dataProvider;
    }
}