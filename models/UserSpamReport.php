<?php

namespace app\models;

use Yii;

class UserSpamReport extends \app\models\base\UserSpamReport
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app','User ID'),
            'second_user_id' => Yii::t('app','Second User ID'),
            'comment' => Yii::t('app','Grund'),
            'dt' => Yii::t('app','Dt'),
        ];
    }

    public function rules() {
        return array_merge(parent::rules(),[
            [['object'],'validateSpamReportUniqueness']
        ]);
    }

    public function setSearchRequestObject($searchRequest) {
        $this->second_user_id = $searchRequest->user_id;
        $this->search_request_id = $searchRequest->id;
        $this->object = \yii\helpers\Html::a("Suchanzeige \"{$searchRequest->title}\"", ['admin-search-request/update', 'id' => $searchRequest->id]);
    }

    public function setOfferObject($offer) {
        $this->second_user_id = $offer->user_id;
        $this->offer_id = $offer->id;
        $this->object = \yii\helpers\Html::a("Allgemeine Angebot \"{$offer->title}\"", ['admin-offer/update', 'id' => $offer->id]);
    }

    public function setUserObject($user) {
        $this->second_user_id = $user->id;
        $this->object = \yii\helpers\Html::a("Chat");
    }

    public function validateSpamReportUniqueness($attribute,$options) {
        if ($this->$attribute=='<a>Chat</a>') return;
        if ($this->isNewRecord && static::find()->where(['user_id'=>$this->user_id,'object'=>$this->object])->count()>0) {
            $this->addError('object',Yii::t('app','You already reported this item as spam'));
        }
    }

    public function updateUserSpamReports() {
        $this->secondUser->spam_reports=\app\models\UserSpamReport::find()->where(['second_user_id'=>$this->second_user_id,'is_active'=>1])->count();
        $this->secondUser->save();
    }
	
	public function checkUserPoints($user_id){
		$user = \app\models\User::findOne($user_id);
		if($user->spam_points <= \app\models\Setting::get('SPAM_MAX_POINTS_MINUS')){
			return false;
		}else{
			return true;
		}
	}
	
	public function addSpamUserPoints($offer_id = '', $search_request_id = ''){

		if($offer_id != ''){
			$model=\app\models\UserSpamReport::find()->where(['offer_id'=>$offer_id,'is_active'=>1])->all();
		}
		if($search_request_id != ''){
			$model=\app\models\UserSpamReport::find()->where(['search_request_id'=>$search_request_id,'is_active'=>1])->all();
		}

		foreach($model as $key => $val){

			$user = \app\models\User::findOne($val['user_id']);
			if($user->spam_points < \app\models\Setting::get('SPAM_MAX_POINTS_PLUS')){
				$user->spam_points = $user->spam_points + 1;
				$user->save();
			}
		}
	}
	
	public function removeSpamUserPoints($offer_id = '', $search_request_id = ''){
		if($offer_id != ''){
			$model=\app\models\UserSpamReport::find()->where(['offer_id'=>$offer_id,'is_active'=>1])->all();
		}
		if($search_request_id != ''){
			$model=\app\models\UserSpamReport::find()->where(['search_request_id'=>$search_request_id,'is_active'=>1])->all();
		}

		foreach($model as $key => $val){
			$user = \app\models\User::findOne($val['user_id']);
			if($user->spam_points > \app\models\Setting::get('SPAM_MAX_POINTS_MINUS')){
				$user->spam_points = $user->spam_points - 1;
				$user->save();
			}
		}
	}
}

\yii\base\Event::on(UserSpamReport::className(), \yii\db\ActiveRecord::EVENT_AFTER_UPDATE, function ($event) {
    $event->sender->updateUserSpamReports();
});
