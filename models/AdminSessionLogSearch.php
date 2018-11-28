<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AdminSessionLog;

/**
 * AdminSearch represents the model behind the search form about `app\modules\foms\models\Admin`.
 */
class AdminSessionLogSearch extends AdminSessionLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id'], 'safe'],
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
        $query = AdminSessionLog::find()->joinWith(['admin'])->with(['admin']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes'=>[
                    'admin'=>[
                        'asc'=>['admin.first_name'=>SORT_ASC,'admin.last_name'=>SORT_ASC],
                        'desc'=>['admin.first_name'=>SORT_DESC,'admin.last_name'=>SORT_DESC],
                    ],
                    'dt_start',
                    'dt_end'
                ],
                'defaultOrder'=>['dt_start'=>SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'admin_id' => $this->admin_id,
        ]);

        return $dataProvider;
    }
}