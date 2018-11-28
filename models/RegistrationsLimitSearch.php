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
class RegistrationsLimitSearch extends User
{
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
        $query = User::find()->with(['avatarFile']);
        $query->select(['user.*','cnt'=>'(select count(*) from user u where u.parent_id=user.id)','lim'=>'COALESCE(user.free_registrations_limit,IF(user.packet=\'VIP_PLUS\',:limit_vip_plus,IF(user.packet=\'VIP\',:limit_vip,:limit_std)))']);
        $query->andWhere('user.status=:status_active',[':status_active'=>\app\models\User::STATUS_ACTIVE])->having('cnt>=lim');
        $query->addParams([
            ':limit_vip_plus'=>\app\models\Setting::get('VIP_PLUS_FREE_REGISTRATIONS_LIMIT'),
            ':limit_vip'=>\app\models\Setting::get('VIP_FREE_REGISTRATIONS_LIMIT'),
            ':limit_std'=>\app\models\Setting::get('STANDART_FREE_REGISTRATIONS_LIMIT'),
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes'=>['cnt','lim','registration_dt','status','balance'],
                'defaultOrder'=>['cnt'=>SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'failed_logins' => $this->failed_logins,
            'status'=>$this->status,
            'spam_reports'=>$this->spam_reports
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