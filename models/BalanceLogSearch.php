<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BalanceLog;
use app\components\EDateTime;

class BalanceLogSearch extends BalanceLog {

    public $username_and_email;
    public $regist_dt_from;
    public $regist_dt_to;
    public $type;
    public $comment;

   

    public function rules() {
        return [
            [['id'], 'integer'],
            [['username_and_email','dt','type','comment','regist_dt_from','regist_dt_to'],'safe'],
        ];
    }

    

    public function attributeLabels() {
        parent::attributeLabels();
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($model, $params) {
        $query = BalanceLog::find()
            ->select(['{{balance_log}}.*',/*'{{bls}}.*'*/])
            ->joinWith(['initiatorUser'])
            ->where(['user_id'=>$model->id])
            //->join('left outer join',[
            //    'bls'=>(new \yii\db\Query())->select(['initiator_user_id','stat_count'=>'count(*)','stat_sum_plus'=>'sum(if(sum>0,sum,0))','stat_sum_minus'=>'sum(if(sum<0,sum,0))'])
            //        ->from('balance_log')->where(['user_id'=>$model->id])->groupBy('initiator_user_id')
            //],'bls.initiator_user_id=balance_log.initiator_user_id')
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => ['dt','type','comment','sum',
                    'stat_count'=>[SORT_ASC=>'bls.stat_count',SORT_DESC=>'bls.stat_count desc'],
                    'stat_sum_plus'=>[SORT_ASC=>'bls.stat_sum_plus',SORT_DESC=>'bls.stat_sum_plus desc'],
                    'stat_sum_minus'=>[SORT_ASC=>'bls.stat_sum_minus',SORT_DESC=>'bls.stat_sum_minus desc'],
                    'username_and_email'=>[
                        'asc'=>['user.email'=>SORT_ASC,'user.first_name'=>SORT_DESC,'user.last_name'=>SORT_DESC],
                        'desc'=>['user.email'=>SORT_DESC,'user.first_name'=>SORT_DESC,'user.last_name'=>SORT_DESC],
                    ],
                ],
                'defaultOrder'=>['dt'=>SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if($this->type!='EVE')
         {
            $query->andWhere([
                 'type'=>$this->type
                 //  'type'=>'%'.$this->type.'%'
        ]);
         }

         if($this->comment!='')
         {
            $query->andWhere([
            'comment'=>$this->comment
           ]);
         }
       
        if ($this->username_and_email) {
            $query->andWhere("CONCAT(user.first_name,'|',user.last_name,'|',user.email) like(:username_and_email)",[
                ':username_and_email'=>'%'.$this->username_and_email.'%'
            ]);
        }

        if ($this->regist_dt_from!='') {
            $query->andWhere('dt>=:dt_from',[
                ':dt_from'=>(new EDateTime($this->regist_dt_from))->sqlDate()
            ]);
        }

        if ($this->regist_dt_to!='') {
            $query->andWhere('dt<=:dt_to',[
                ':dt_to'=>(new EDateTime($this->regist_dt_to))->modify('+1 day')->sqlDate()
            ]);
        }

        //$query->andFilterWhere(['comment' => $this->comment]);

        return $dataProvider;
    }
}