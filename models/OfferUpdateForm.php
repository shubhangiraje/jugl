<?php

namespace app\models;

use Yii;


class OfferUpdateForm extends \app\components\Model {
    public $offer_id;
    public $view_bonus_total;
    public $view_bonus;
    public $comment;
    public $active_till_day;
    public $active_till_month;
    public $active_till_year;
    public $active_till;
    public $without_bonus;

    const SCENARIO_WITH_BONUS='withBonus';
    const SCENARIO_WITHOUT_BONUS='withoutBonus';

    public function rules() {
        return [
            [['offer_id','comment'],'safe'],
            [['view_bonus','view_bonus_total'],'required','on'=>'withBonus'],
            [['active_till_day','active_till_month','active_till_year'],'number'],
            [['active_till_day'],'activeTillMustBeInFuture'],
            [['view_bonus','view_bonus_total'],'integer','min'=>1],
            [['view_bonus_total'],'validateViewBonusTotal','on'=>'withBonus'],
            ['view_bonus', 'is_view_bonus_total','on'=>'withBonus']
        ];
    }

    public function scenarios() {
        return [
            'withBonus'=>[
                'offer_id',
                'view_bonus_total',
                'view_bonus',
                'comment',
                'active_till_day',
                'active_till_month',
                'active_till_year',
                'active_till'
            ],
            'withoutBonus'=>[
                'offer_id',
                'comment',
                'active_till_day',
                'active_till_month',
                'active_till_year',
                'active_till'
            ],
        ];
    }

    public function activeTillMustBeInFuture() {
        $dt=new \app\components\EDateTime();

        if (!$dt->setDate($this->active_till_year,$this->active_till_month,$this->active_till_day)) {
            $this->addError('active_till',Yii::t('app','Bitte wählen Anzeige aktiv bis Datum'));
            return;
        }
    }

    public function is_view_bonus_total(){
        $view_bonus = 1;
        $model=Offer::find()->where(['id'=>$this->offer_id])->with(['offerInterests'])->one();
        if($model->offerInterests[0]->level2Interest) {
            $interest_view_bonus = $model->offerInterests[0]->level2Interest;
        } else {
            $interest_view_bonus = $model->offerInterests[0]->level1Interest;
        }

        if($interest_view_bonus->offer_view_bonus) {
            $view_bonus = $interest_view_bonus->offer_view_bonus;
        } else {
            if(!empty($interest_view_bonus->parent)) {
                $view_bonus = $interest_view_bonus->parent->offer_view_bonus;
            }
        }

        if($this->view_bonus < $view_bonus){
            $this->addError('view_bonus', Yii::t('app', 'Werbebonus pro User darf nicht kleiner als {view_bonus} sein.', ['view_bonus'=>$view_bonus]));
        }
    }


    public function validateViewBonusTotal($attribute,$options) {
        $offer=\app\models\Offer::findOne(['id'=>$this->offer_id,'user_id'=>Yii::$app->user->id]);

        if (!$offer) {
            $this->addError('offer_id',Yii::t('app','Invalid Werbung'));
            return;
        }

        if ($this->view_bonus_total-$offer->view_bonus_total>$offer->user->balance) {
            $this->addError('view_bonus_total',Yii::t('app','NOT_ENOUGH_JUGL'));
        }

        if ($this->view_bonus_total-$offer->view_bonus_used<$this->view_bonus) {
            $this->addError('view_bonus_total',Yii::t('app','Werbebudget ist zu klein'));
        }
    }

    public function attributeLabels()
    {
        return [
            'view_bonus' => Yii::t('app','Werbebonus pro User'),
            'view_bonus_total' => Yii::t('app','Max. Budget für Werbeaktion'),
        ];
    }
}