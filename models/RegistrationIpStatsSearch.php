<?php

namespace app\models;

use yii\db\Query;
use \app\components\EDateTime;


class RegistrationIpStatsSearch extends \yii\base\Model {
    public $date_from;
    public $date_to;

    public function rules() {
        return [
            [['date_from','date_to'],'safe']
        ];
    }
    public function search($params) {
        $query=new Query();
        $query->select('registration_ip,count(*) as cnt')->from('user')->where('registration_ip is not null')->groupBy('registration_ip');

        if ($this->load($params) && $this->validate()) {
            if ($this->date_from) {
                $query->andWhere('registration_dt>=:date_from',[':date_from'=>(new EDateTime($this->date_from))->sqlDate()]);
            }
            if ($this->date_to) {
                $query->andWhere('registration_dt<:date_to',[':date_to'=>(new EDateTime($this->date_to))->modify('+1 day')->sqlDate()]);
            }
        }

        $dataProvider=new \yii\data\SqlDataProvider([
            'sql'=> $query->createCommand()->getRawSql(),
            'totalCount'=>$query->count(),
            'sort'=>[
                'attributes'=>[
                    'cnt'
                ],
                'defaultOrder'=>['cnt'=>SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => 500,
            ],
        ]);

        return $dataProvider;
    }

}