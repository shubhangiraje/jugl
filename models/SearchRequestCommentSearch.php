<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SearchRequestComment;


class SearchRequestCommentSearch extends SearchRequestComment {

    public $user_name;

    public function rules() {
        return [
            [['id'], 'integer'],
            [['comment', 'create_dt', 'response', 'response_dt', 'user_name'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($id, $params) {
        $query = SearchRequestComment::find()->where(['search_request_id'=>$id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes'=>[
                    'id',
                    'create_dt'
                ],
                'defaultOrder'=>['id'=>SORT_DESC]
            ]
        ]);
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}
