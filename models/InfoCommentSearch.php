<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\InfoComment;
use yii\data\Pagination;

class InfoCommentSearch extends InfoComment {

    public $user_name;

    public function rules() {
        return [
            [['id', 'user_id'], 'integer'],
            [['dt', 'comment', 'lang', 'user_name'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($info_id, $params) {
        $query = InfoComment::find()->where(['info_comment.info_id'=>$info_id])->joinWith(['user']);
        $query->andWhere('info_comment.status!=:status', [':status'=>InfoComment::STATUS_DELETED]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes'=>[
                    'id',
                    'status',
                    'dt'
                ],
                'defaultOrder'=>['id'=>SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->user_name!='') {
            $query->andWhere('user.first_name like(:name) or user.last_Name like(:name) or user.company_name like(:name)',[':name'=>'%'.$this->user_name.'%']);
        }

        $query->andFilterWhere(['like', 'info_comment.comment', $this->comment])
            ->andFilterWhere(['like', 'info_comment.lang', $this->lang])
            ->andFilterWhere(['like', 'info_comment.dt', $this->dt]);

        return $dataProvider;
    }
}
