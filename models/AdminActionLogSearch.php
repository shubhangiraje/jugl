<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AdminActionLog;

/**
 * AdminSearch represents the model behind the search form about `app\modules\foms\models\Admin`.
 */
class AdminActionLogSearch extends AdminActionLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id','action'], 'safe'],
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
        $query = AdminActionLog::find()->joinWith(['admin'])->with(['admin']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes'=>[
                    'admin'=>[
                        'asc'=>['admin.first_name'=>SORT_ASC,'admin.last_name'=>SORT_ASC],
                        'desc'=>['admin.first_name'=>SORT_DESC,'admin.last_name'=>SORT_DESC],
                    ],
                    'dt',
                ],
                'defaultOrder'=>['dt'=>SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'admin_id' => $this->admin_id,
        ]);

        if ($this->action!='') {
            $query->andWhere('action regexp :action',[':action'=>$this->action]);
        }

        $query->andFilterWhere(['like','comment',$this->comment]);

        return $dataProvider;
    }
}