<?php

namespace app\models;

use Yii;

class UserTeamFeedback extends \app\models\base\UserTeamFeedback {

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


    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['update'] = [
            'feedback', 'rating'
        ];

        $scenarios['response-update'] = [
            'response'
        ];

        return $scenarios;
    }

    public function updateUserRating() {
        $res=$this->db->createCommand("select sum(rating) as ratings_sum,count(*) as ratings_count from user_team_feedback where user_id=:user_id",[':user_id'=>$this->user_id])->queryOne();

        if ($res['ratings_count']>0) {
            $this->user->team_rating=floor(0.5+$res['ratings_sum']/$res['ratings_count']);
        } else {
            $this->user->team_rating=0;
        }

        $this->user->team_feedback_count=$res['ratings_count'];
        $this->user->save();
    }

    public function init() {
        $this->on(static::EVENT_AFTER_DELETE,[$this,'updateUserRating']);
        $this->on(static::EVENT_AFTER_INSERT,[$this,'updateUserRating']);
        $this->on(static::EVENT_AFTER_UPDATE,[$this,'updateUserRating']);
    }

    public function rules() {
        return array_merge(static::cleanRule('rating', parent::rules()),[
            ['rating', 'required', 'message'=>Yii::t('app', 'Klicke zur Bewertung auf die Sterne. 1 Stern = schlecht, 5 Sterne = sehr gut.')],
            ['response','required','on'=>'response-update','message'=>Yii::t('app','Geben Sie bitte Antwort')]
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
