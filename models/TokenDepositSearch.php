<?php

namespace app\models;

use app\components\EDateTime;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TokenDeposit;

/**
 * UserSearch represents the model behind the search form about `app\modules\foms\models\User`.
 */
class TokenDepositSearch extends TokenDeposit
{

    public $created_at_from;
    public $created_at_to;
    public $completion_dt_from;
    public $completion_dt_to;
    public $user_name;
    public $guarantee_name;


    public function rules() {
        return [
            [['created_at_from','created_at_to','completion_dt_from','completion_dt_to','user_name','sum','period_months',
                'contribution_percentage','guarantee_name','status'],'safe']
        ];
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
        $query = TokenDeposit::find()->joinWith(['user','tokenDepositGuarantee'])->andWhere(['token_deposit.status'=>[
            TokenDeposit::STATUS_ACTIVE,
            TokenDeposit::STATUS_COMPLETED
        ]]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
        ]);

        if ($this->created_at_from!='') {
            $query->andWhere('created_at>=:dt_from',[
                ':dt_from'=>(new EDateTime($this->created_at_from))->sqlDate()
            ]);
        }

        if ($this->created_at_to!='') {
            $query->andWhere('created_at<=:dt_to',[
                ':dt_to'=>(new EDateTime($this->created_at_to))->modify('+1 day')->sqlDate()
            ]);
        }


        if ($this->completion_dt_from!='') {
            $query->andWhere('completion_dt>=:dt2_from',[
                ':dt2_from'=>(new EDateTime($this->completion_dt_from))->sqlDate()
            ]);
        }

        if ($this->completion_dt_to!='') {
            $query->andWhere('completion_dt<=:dt2_to',[
                ':dt2_to'=>(new EDateTime($this->completion_dt_to))->modify('+1 day')->sqlDate()
            ]);
        }

        if ($this->user_name!='') {
            $query->andWhere('user.first_name like(:name) or user.last_name like(:name) or user.company_name like(:name) or user.email like(:name)',[':name'=>'%'.$this->user_name.'%']);
        }

        if ($this->guarantee_name!='') {
            $query->andWhere('token_deposit_guarantee.title_de like(:guarantee_name)',[':guarantee_name'=>'%'.$this->guarantee_name.'%']);
        }

        $query->andFilterWhere([
            'sum'=>$this->sum,
            'period_months'=>$this->period_months,
            'contribution_percentage'=>$this->contribution_percentage,
            'user.status'=>$this->status
        ]);


        return $dataProvider;
    }
}