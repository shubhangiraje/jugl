<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DefaultText;


class DefaultTextSearch extends DefaultText {

    public function rules() {
        return [
            [['id'], 'integer'],
            [['category', 'text'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($category, $params) {
        $query = DefaultText::find()->where(['category'=>$category]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder'=>['id'=>SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}