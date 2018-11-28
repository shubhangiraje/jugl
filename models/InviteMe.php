<?php

namespace app\models;

use Yii;

class InviteMe extends \app\models\base\InviteMe {
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'first_name' => Yii::t('app','First Name'),
            'last_name' => Yii::t('app','Last Name'),
            'email' => Yii::t('app','Email'),
            'phone' => Yii::t('app','Phone'),
        ];
    }

    public function rules() {
        return array_merge(parent::rules(),[
            [['email', 'phone'], 'trim'],
            ['email','email'],
            ['phone','match','pattern'=>'%^\d{4} ?\d{6,10}$%','message'=>Yii::t('app','Gib Deine Mobilnummer wie folgt ein: 0151 111222333')]   ,
            ['email','unique','targetClass'=>'app\models\User','message'=>Yii::t('app','User mit dieser Emailadresse existiert bereits.')],
            ['phone','unique','targetClass'=>'app\models\User','message'=>Yii::t('app','User mit dieser Telefonnummer existiert bereits.')],
            ['email','unique','targetClass'=>'app\models\InviteMe','message'=>Yii::t('app','User mit dieser Emailadresse existiert bereits.')],
            ['phone','unique','targetClass'=>'app\models\InviteMe','message'=>Yii::t('app','User mit dieser Telefonnummer existiert bereits.')],
        ]);
    }

    public static function getCountryList() {
        $key=__CLASS__.__FUNCTION__.Yii::$app->language;
        $data=Yii::$app->cache->get($key);
        if ($data===false) {
            $dt = (new \app\components\EDateTime())->modify("-" . \app\models\Setting::get('TEAM_CHANGE_PERIOD_DAYS') . " minute");

            $query = Yii::$app->db->createCommand('
                SELECT COUNT(country_id) as count, country_id
                FROM user 
                WHERE status=:status
                    AND show_in_become_member = 1
                    AND (registration_dt > :dt)
                    AND id!=:id 
                GROUP BY country_id
            ', [
                ':status' => User::STATUS_ACTIVE,
                ':dt' => $dt->sqlDateTime(),
                ':id' => Yii::$app->user->id
            ])->queryAll();

            $countryCountData = [];
            foreach ($query as $item) {
                $countryCountData[$item['country_id']] = intval($item['count']);
            }

            $data = [];
            foreach (Country::getList() as $country_id => $country_name) {
                $idata['id'] = $country_id;

                if ($countryCountData[$country_id]) {
                    $idata['name'] = $country_name . ' (' . $countryCountData[$country_id] . ')';
                } else {
                    $idata['name'] = $country_name . ' (0)';
                }

                $idata['flag'] = Country::getListShort()[$country_id];
                $data[] = $idata;
            }

            Yii::$app->cache->set($key,$data,60);
        }

        return $data;
    }

}
