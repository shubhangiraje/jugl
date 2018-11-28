<?php

namespace app\models;

use Yii;

class SearchRequestOfferActiveQuery extends \yii\db\ActiveQuery
{
    public function init()
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->andWhere(['!=',"$tableName.status", SearchRequestOffer::STATUS_DELETED]);
        parent::init();
    }

}


class SearchRequestOffer extends \app\models\base\SearchRequestOffer
{
    const STATUS_NEW='NEW';
    const STATUS_CONTACTED='CONTACTED';
    const STATUS_ACCEPTED='ACCEPTED';
    const STATUS_REJECTED='REJECTED';
    const STATUS_DELETED='DELETED';

    const REJECT_OFFER_NOT_FIT='OFFER_NOT_FIT';
    const REJECT_CHANGED_MY_MIND='CHANGED_MY_MIND';
    const REJECT_OFFER_IS_EXPENSIVE='OFFER_IS_EXPENSIVE';
    const REJECT_OTHERS='OTHERS';

    public static function getRejectReasonList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::REJECT_OFFER_NOT_FIT=>Yii::t('app','Das Angebot passt nicht'),
                static::REJECT_CHANGED_MY_MIND=>Yii::t('app','Ich habe mich anders entschieden'),
                static::REJECT_OFFER_IS_EXPENSIVE=>Yii::t('app','Das Angebot ist zu teuer'),
                static::REJECT_OTHERS=>Yii::t('app','Sonstiges'),
            ];
        }

        return $items;
    }

    public function getRejectReasonLabel() {
        return $this->getRejectReasonList()[$this->reject_reason];
    }

    public static function getStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::STATUS_NEW=>Yii::t('app','SEARCH_REQUEST_OFFER_STATUS_NEW'),
                static::STATUS_CONTACTED=>Yii::t('app','SEARCH_REQUEST_OFFER_STATUS_NEW'),
                static::STATUS_ACCEPTED=>Yii::t('app','SEARCH_REQUEST_OFFER_STATUS_ACCEPTED'),
                static::STATUS_REJECTED=>Yii::t('app','SEARCH_REQUEST_OFFER_STATUS_REJECTED'),
                static::STATUS_DELETED=>Yii::t('app','SEARCH_REQUEST_OFFER_STATUS_DELETED')
            ];
        }

        return $items;
    }

    public function __toString() {
        return $this->description;
    }

    public function getStatusLabel() {
        return static::getStatusList()[$this->status];
    }

    public static function find()
    {
        return new SearchRequestOfferActiveQuery(get_called_class());
    }

    public function rules() {
        return array_merge(SearchRequest::cleanPriceFromRules(parent::rules()),[
            ['reject_reason', 'required', 'on'=>'reject', 'message'=>Yii::t('app', 'Bitte gib den Grund deiner Ablehnung an')],
            ['reject_comment', 'required', 'on'=>'reject', 'message'=>Yii::t('app', 'Bitte gib den Kommentar deiner Ablehnung an')],
            ['price_from','required', 'message'=>Yii::t('app', 'Bitte gib einen Preis ein.')],
            [['price_from'],'is_price'],
        ]);
    }

    public function is_price(){
        if(!empty($this->price_to)) {
            if($this->price_from > $this->price_to){
                $this->addError('price_from', Yii::t('app', 'Bitte kontrolliere die Preise, die Du eingegeben hast'));
            }
        }
    }


    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'search_request_id' => Yii::t('app','Search Request ID'),
            'user_id' => Yii::t('app','User ID'),
            'description' => Yii::t('app','Beschreibung'),
            'price_from' => Yii::t('app','Preis von'),
            'price_to' => Yii::t('app','Preis bis'),
            'reject_reason' => Yii::t('app', 'Reject reason'),
            'reject_comment' => Yii::t('app', 'Reject comment'),
        ];
    }

    public function getDetailsFiles()
    {
        return $this->hasMany('\app\models\File', ['id' => 'file_id'])->viaTable('search_request_offer_file', ['search_request_offer_id' => 'id'],function($query) {$query->orderBy('sort_order asc');});
    }

    public function getFiles()
    {
        return $this->hasMany('\app\models\File', ['id' => 'file_id'])->viaTable('search_request_offer_details_file', ['search_request_offer_id' => 'id'],function($query) {$query->orderBy('sort_order asc');});
    }

    public function scenarios() {
        $scenarios=parent::scenarios();

        $scenarios['save']=[
            'description','price_from','price_to','details'
        ];
		$scenarios['save_advertising']=[
            'description', 'price_from','price_to','details'
        ];
        $scenarios['reject']=[
            'reject_reason','reject_comment'
        ];

        return $scenarios;
    }

    public function setClosedDtIfNecessary() {
        if (!in_array($this->status,[static::STATUS_CONTACTED,static::STATUS_NEW]) && !$this->closed_dt) {
            $this->closed_dt=(new \app\components\EDateTime())->sqlDateTime();
        }
    }


    public static function deleteUserRating($feedback_id) {
        $userFeedback = static::findOne(['user_feedback_id'=>$feedback_id]);
        $counterUserFeedback = static::findOne(['counter_user_feedback_id'=>$feedback_id]);

        if($userFeedback) {
            $userFeedback->user_feedback_id = NULL;
            $userFeedback->save();
        }

        if($counterUserFeedback) {
            $counterUserFeedback->counter_user_feedback_id = NULL;
            $counterUserFeedback->save();
        }

        return true;
    }



}

\yii\base\Event::on(SearchRequestOffer::className(), \yii\db\ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
    $event->sender->searchRequest->user->updateCounters(['stat_new_search_requests_offers'=>1]);
    \app\models\UserFollowerEvent::addNewSearchRequestOffer($event->sender);
    \app\components\ChatServer::statusUpdate([$event->sender->searchRequest->user->id]);
});

\yii\base\Event::on(SearchRequestOffer::className(), \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE, function ($event) {
    if ($event->sender->status==SearchRequestOffer::STATUS_ACCEPTED &&
        $event->sender->oldAttributes['status']!=SearchRequestOffer::STATUS_ACCEPTED) {
        \app\models\UserEvent::addSearchRequestFeedbackNotification($event->sender);
        \app\models\UserEvent::addSearchRequestCounterFeedbackNotification($event->sender);
    }
    if ($event->sender->status==SearchRequestOffer::STATUS_ACCEPTED &&
        $event->sender->oldAttributes['status']!=SearchRequestOffer::STATUS_ACCEPTED ||
        ((!$event->sender->oldAttributes['user_feedback_id'] && $event->sender->user_feedback_id) || (!$event->sender->oldAttributes['counter_user_feedback_id'] && $event->sender->counter_user_feedback_id))) {
        $event->sender->on(\yii\db\ActiveRecord::EVENT_AFTER_UPDATE,function($event) {
            $event->sender->searchRequest->user->updateStatAwaitingFeedbacks();
            $event->sender->user->updateStatAwaitingFeedbacks();
        });
    }
    $event->sender->setClosedDtIfNecessary();
});
