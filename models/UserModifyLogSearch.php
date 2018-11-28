<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class UserModifyLogSearch extends UserModifyLog  {

    public function rules() {
        return [
            [['id', 'user_id'], 'integer'],
            [['modify_dt','description'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($model, $params) {
        $query = UserModifyLog::find()->where(['user_id'=>$model->id]);

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