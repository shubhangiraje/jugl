<?php

namespace app\models;

use app\components\EDateTime;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserSearch represents the model behind the search form about `app\modules\foms\models\User`.
 */
class UserValidationSearch extends User
{
    public $registration_dt_from;
    public $registration_dt_to;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'failed_logins'], 'integer'],
            [['status','email', 'first_name', 'last_name', 'nick_name', 'password', 'access_token', 'auth_key',
            'registration_dt_from','registration_dt_to'], 'safe'],
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
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder'=>['registration_dt'=>SORT_DESC]
            ]
        ]);

        $query->andWhere(['validation_status'=>User::VALIDATION_STATUS_AWAITING]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'failed_logins' => $this->failed_logins,
            'status'=>$this->status,
        ]);

        if ($this->registration_dt_from!='') {
            $query->andWhere('registration_dt>=:dt_from',[
                ':dt_from'=>(new EDateTime($this->registration_dt_from))->sqlDate()
            ]);
        }

        if ($this->registration_dt_to!='') {
            $query->andWhere('registration_dt<=:dt_to',[
                ':dt_to'=>(new EDateTime($this->registration_dt_to))->modify('+1 day')->sqlDate()
            ]);
        }

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'nick_name', $this->last_name])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'access_token', $this->access_token])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key]);

        return $dataProvider;
    }
}