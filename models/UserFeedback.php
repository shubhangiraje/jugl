<?php

namespace app\models;

use Yii;

class UserFeedback extends \app\models\base\UserFeedback {

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','Benutzer'),
            'second_user_id' => Yii::t('app','Benutzer'),
            'feedback' => Yii::t('app','Feedback'),
            'rating' => Yii::t('app','Bewertung'),
            'create_dt' => Yii::t('app','Datum'),
            'response' => Yii::t('app','Antwort'),
            'response_dt' => Yii::t('app','Antwort Datum'),
        ];
    }


    public function getCounterOfferRequests() {
        return $this->hasMany('\app\models\OfferRequest', ['counter_user_feedback_id' => 'id']);
    }

    public function getOfferRequests() {
        return $this->hasMany('\app\models\OfferRequest', ['user_feedback_id' => 'id']);
    }

    public function getCounterSearchRequestOffers() {
        return $this->hasMany('\app\models\SearchRequestOffer', ['counter_user_feedback_id' => 'id']);
    }

    public function getSearchRequestOffers() {
        return $this->hasMany('\app\models\SearchRequestOffer', ['user_feedback_id' => 'id']);
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['update'] = [
            'feedback', 'rating'
        ];

        $scenarios['response-update'] = [
            'response'
        ];

        $scenarios['admin-update'] = [
            'feedback'
        ];

        return $scenarios;
    }

    public function updateUserRating() {
        $res=$this->db->createCommand("select sum(rating) as ratings_sum,count(*) as ratings_count from user_feedback where user_id=:user_id",[':user_id'=>$this->user_id])->queryOne();

        if ($res['ratings_count']>0) {
            $this->user->rating=floor(0.5+$res['ratings_sum']/$res['ratings_count']);
        } else {
            $this->user->rating=0;
        }

        $this->user->feedback_count=$res['ratings_count'];
        $this->user->save();
    }

    public function init() {
        $this->on(static::EVENT_AFTER_DELETE,[$this,'updateUserRating']);
        $this->on(static::EVENT_AFTER_INSERT,[$this,'updateUserRating']);
        $this->on(static::EVENT_AFTER_UPDATE,[$this,'updateUserRating']);
    }

    public function rules() {
        return array_merge(static::cleanRule('rating', parent::rules()),[
            ['rating', 'required', 'message'=>Yii::t('app', 'Klicke zur Bewertung auf die Sterne. 1 Stern = schlecht, 5 Sterne = sehr gut.')]
        ]);
    }

    public static function cleanRule($delRule, $rules) {
        foreach ($rules as $key=>$rule) {
            if($rule[1] == 'required') {
                $keyRule = $key;
                $keyDel = array_search($delRule, $rules[$keyRule][0]);
                unset($rules[$keyRule][0][$keyDel]);
            }
        }
        return $rules;
    }

}
