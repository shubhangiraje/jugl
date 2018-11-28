<?php

namespace app\models;

use Yii;


class InterestSelection extends \yii\base\Model {
    public $level1Interest_id;
    public $level2Interest_id;
    public $level3Interest_ids;

    public $type;

    public function rules() {
        return [
            ['level1Interest_id','required'],
            [['level2Interest_id','level3Interest_ids'],'safe']
        ];
    }

    public function getLevel1List() {
        $models=Interest::find()->where('parent_id is null')->andFilterWhere(['type'=>$this->type])->orderBy('sort_order')->all();
        $data=[];
        foreach($models as $model) {
            $data[$model->id]=$model->title;
        }

        return $data;
    }

    public static function getNestedLevelList($parentId) {
        if (!$parentId) return [];
        $models=Interest::find()->where(['parent_id'=>$parentId])->orderBy('sort_order')->all();
        $data=[];
        foreach($models as $model) {
            $data[$model->id]=$model->title;
        }

        return $data;
    }

    public function loadFromOffer($model) {
        if (count($model->offerInterests)>0) {
            $this->level1Interest_id=$model->offerInterests[0]->level1Interest->id;
            $this->level2Interest_id=$model->offerInterests[0]->level2Interest->id;

            $this->level3Interest_ids=[];
            foreach($model->offerInterests as $sri) {
                $this->level3Interest_ids[]=$sri->level3Interest->id;
            }
        }
    }

    public function loadFromSearchRequest($model) {
        if (count($model->searchRequestInterests)>0) {
            $this->level1Interest_id=$model->searchRequestInterests[0]->level1Interest->id;
            $this->level2Interest_id=$model->searchRequestInterests[0]->level2Interest->id;

            $this->level3Interest_ids=[];
            foreach($model->searchRequestInterests as $sri) {
                $this->level3Interest_ids[]=$sri->level3Interest->id;
            }
        }
    }
	
	public function loadFromAdvertising($model) {
        if (count($model->advertisingInterests)>0) {
            $this->level1Interest_id=$model->advertisingInterests[0]->level1Interest->id;
            $this->level2Interest_id=$model->advertisingInterests[0]->level2Interest->id;

            $this->level3Interest_ids=[];
            foreach($model->advertisingInterests as $sri) {
                $this->level3Interest_ids[]=$sri->level3Interest->id;
            }
        }
    }

    public function saveForOffer($model) {
        \app\models\OfferInterest::deleteAll(['offer_id'=>$model->id]);
        if (!empty($this->level3Interest_ids)) {
            foreach($this->level3Interest_ids as $level3Interest_id) {
                if ($level3Interest_id) {
                    $offerInterest = new \app\models\OfferInterest();
                    $offerInterest->offer_id = $model->id;
                    $offerInterest->level1_interest_id = $this->level1Interest_id;
                    $offerInterest->level2_interest_id = $this->level2Interest_id;
                    $offerInterest->level3_interest_id = $level3Interest_id;
                    $offerInterest->save();
                }
            }
        } else {
            $offerInterest=new \app\models\OfferInterest();
            $offerInterest->offer_id=$model->id;
            $offerInterest->level1_interest_id=$this->level1Interest_id;
            $offerInterest->level2_interest_id=$this->level2Interest_id;
            $offerInterest->save();
        }
    }

    public function saveForSearchRequest($model) {
        \app\models\SearchRequestInterest::deleteAll(['search_request_id'=>$model->id]);
        if (!empty($this->level3Interest_ids)) {
            foreach($this->level3Interest_ids as $level3Interest_id) {
                if ($level3Interest_id) {
                    $searchRequestInterests = new \app\models\SearchRequestInterest();
                    $searchRequestInterests->search_request_id=$model->id;
                    $searchRequestInterests->level1_interest_id=$this->level1Interest_id;
                    $searchRequestInterests->level2_interest_id=$this->level2Interest_id;
                    $searchRequestInterests->level3_interest_id=$level3Interest_id;
                    $searchRequestInterests->save();
                }
            }
        } else {
            $searchRequestInterests=new \app\models\SearchRequestInterest();
            $searchRequestInterests->search_request_id=$model->id;
            $searchRequestInterests->level1_interest_id=$this->level1Interest_id;
            $searchRequestInterests->level2_interest_id=$this->level2Interest_id;
            $searchRequestInterests->save();
        }
    }
	
	public function saveForAdvertising($model) {
        \app\models\AdvertisingInterest::deleteAll(['advertising_id'=>$model->id]);
        if (!empty($this->level3Interest_ids)) {
            foreach($this->level3Interest_ids as $level3Interest_id) {
                if ($level3Interest_id) {
                    $advertisingInterests = new \app\models\AdvertisingInterest();
                    $advertisingInterests->advertising_id=$model->id;
                    $advertisingInterests->level1_interest_id=$this->level1Interest_id;
                    $advertisingInterests->level2_interest_id=$this->level2Interest_id;
                    $advertisingInterests->level3_interest_id=$level3Interest_id;
                    $advertisingInterests->save();
                }
            }
        } else {
			
            $advertisingInterests=new \app\models\AdvertisingInterest();
            $advertisingInterests->advertising_id=$model->id;
            $advertisingInterests->level1_interest_id=$this->level1Interest_id;
            $advertisingInterests->level2_interest_id=$this->level2Interest_id;
            $advertisingInterests->save();
        }
    }

    public function attributeLabels()
    {
        return [
            'level1Interest_id'=>Yii::t('app','Allgemeine Interessekategorie'),
            'level2Interest_id'=>Yii::t('app','Unterkategorie'),
            'level3Interest_ids'=>Yii::t('app','Themenfilter')
        ];
    }
}