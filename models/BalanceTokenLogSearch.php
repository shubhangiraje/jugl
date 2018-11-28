<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BalanceTokenLog;
use app\components\EDateTime;




class BalanceTokenLogSearch extends BalanceTokenLog {

    public $username_and_email;
    public $registration_dt_from;
    public $registration_dt_to;
    public $type;
    public $comment;

    public function rules() {
        return [
            [['id'], 'integer'],
            [['username_and_email','dt','type','comment','registration_dt_from','registration_dt_to'], 'safe'],
        ];
    }
  
 /*public function comparedate()
   {
    
    $start_date = $("#balancetokenlogsearch-registration_dt_to").val();
    $end_date =  $("#balancetokenlogsearch-registration_dt_from").val();*/
    /*if($end_date>= $start_date)
    {
        $this->addError("from date should not be less than to date");
    }
   }*/
    
     public static function getSumActionList() {
        return [
            'USER_STATUS_BLOCKED'=>Yii::t('app','Geblockt'),
            'USER_STATUS_DELETED_ADMIN'=>Yii::t('app','Gelöscht'),
            'USER_VALIDATION_STATUS_SUCCESS'=>Yii::t('app','Frei (Ident. best.)'),
            'USER_STATUS_DELETED_USER'=>Yii::t('app','Selbst gelöscht'),
        ];
    }

    public function attributeLabels() {
        parent::attributeLabels();
    }

    public function scenarios() {
        return Model::scenarios();
    }

   

    public function search($model, $params) {

        $query = BalanceTokenLog::find()
            ->select(['{{balance_token_log}}.*',/*'{{bls}}.*'*/])
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
                ///    'registration_dt'=>[SORT_DESC],
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

         if ($this->registration_dt_from!='') {
            $query->andWhere('dt>=:dt_from',[
                ':dt_from'=>(new EDateTime($this->registration_dt_from))->sqlDate()
            ]);
        }

        if ($this->registration_dt_to!='') {
            $query->andWhere('dt<=:dt_to',[
                ':dt_to'=>(new EDateTime($this->registration_dt_to))->modify('+1 day')->sqlDate()
            ]);
        }

         $query->andFilterWhere(['comment' => $this->comment]);
           //echo $query->createCommand()->getRawSql();die();
        return $dataProvider;
    }
}