<?php

namespace app\models;

use Yii;

class OfferRequest extends \app\models\base\OfferRequest
{
    const STATUS_ACTIVE='ACTIVE';
    const STATUS_EXPIRED='EXPIRED';
    const STATUS_ACCEPTED='ACCEPTED';
    const STATUS_REJECTED='REJECTED';
    const STATUS_DELETED='DELETED';

    const PAY_STATUS_INVITED='INVITED';
    const PAY_STATUS_PAYED='PAYED';
    const PAY_STATUS_CONFIRMED='CONFIRMED';

    const PAY_METHOD_PAYPAL='PAYPAL';
    const PAY_METHOD_JUGLS='JUGLS';
    const PAY_METHOD_BANK='BANK';
    const PAY_METHOD_POD='POD';

    public function rules() {
        return array_merge(parent::rules(),[
            ['description','enoughMoneyValidator','on'=>'save','skipOnEmpty'=>false]
        ]);
    }

    public function enoughMoneyValidator() {
        if (in_array($this->offer->type,[Offer::TYPE_AUCTION,Offer::TYPE_AUTOSELL]) &&
            $this->offer->pay_allow_jugl &&
            !$this->offer->pay_allow_bank &&
            !$this->offer->pay_allow_paypal &&
            !$this->offer->pay_allow_pod &&
            $this->offer->price*\app\models\Setting::get('EXCHANGE_JUGLS_PER_EURO')>Yii::$app->user->identity->balance) {
                $this->addError('offer_id',Yii::t('app','Du hast nicht genug Jugls'));
        }
    }

    public static function getPayStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::PAY_STATUS_INVITED=>Yii::t('app','Waiting for payment'),
                static::PAY_STATUS_PAYED=>Yii::t('app','Payed, waiting for confirmation'),
                static::PAY_STATUS_CONFIRMED=>Yii::t('app','Payed and confirmed'),
            ];
        }

        return $items;
    }

    public static function getPayMethodList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::PAY_METHOD_PAYPAL=>Yii::t('app','PayPal'),
                static::PAY_METHOD_JUGLS=>Yii::t('app','Jugls'),
                static::PAY_METHOD_BANK=>Yii::t('app','Banküberweisung'),
                static::PAY_METHOD_POD=>Yii::t('app','Barzahlung bei Abholung'),
            ];
        }

        return $items;
    }

    public static function getStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::STATUS_ACTIVE=>Yii::t('app','OFFER_REQUEST_STATUS_ACTIVE'),
                static::STATUS_EXPIRED=>Yii::t('app','OFFER_REQUEST_STATUS_EXPIRED'),
                static::STATUS_ACCEPTED=>Yii::t('app','OFFER_REQUEST_STATUS_ACCEPTED'),
                static::STATUS_REJECTED=>Yii::t('app','OFFER_REQUEST_STATUS_REJECTED'),
                static::STATUS_DELETED=>Yii::t('app','OFFER_REQUEST_STATUS_DELETED')
            ];
        }

        return $items;
    }

    public function __toString() {
        return $this->description;
    }

    public function getStatusLabel() {
        $status=$this->status;
        if ($status==static::STATUS_ACTIVE && $this->offer->type==\app\models\Offer::TYPE_AUCTION && $this->getIsExpired()) {
            $status=static::STATUS_EXPIRED;
        }

        return static::getStatusList()[$status];
    }

    public function attributeLabels()
    {
        return [
            'description' => Yii::t('app','Kommentar'),
        ];
    }

    public function scenarios() {
        $scenarios=parent::scenarios();

        $scenarios['save']=[
            'description'
        ];

        return $scenarios;
    }

    public function getBetCanBeChanged() {
        if ($this->user_id!=Yii::$app->user->id) {
            return false;
        }

        if ($this->offer->type!=\app\models\Offer::TYPE_AUCTION || $this->offer->status!=\app\models\Offer::STATUS_ACTIVE) {
            return false;
        }

        if ($this->status!=static::STATUS_ACTIVE || $this->getIsExpired()) {
            return false;
        }

        return true;
    }

    public function getIsExpired() {
        return (new \app\components\EDateTime())->sqlDateTime()>$this->bet_active_till || $this->bet_active_till=='0000-00-00 00:00:00';
    }

    public function setClosedDtIfNecessary() {
        if (!in_array($this->status,[static::STATUS_ACTIVE]) && !$this->closed_dt) {
            $this->closed_dt=(new \app\components\EDateTime())->sqlDateTime();
        }
    }

    public function decreaseBuyerOfferRequestNoPaymentNotifications() {
        if ($this->oldAttributes['payment_status']!=static::PAY_STATUS_CONFIRMED
            && $this->pay_status==static::PAY_STATUS_CONFIRMED
            && $this->payment_complaint==1) {
            $this->user->updateCounters(['payment_complaints'=>-1]);
        }
    }

    public function notifyBuyer() {
        UserEvent::addOfferRequestNotifyNoPaymentBuyer($this);
        $this->no_payment_buyer_notified=1;
        $this->save();
    }

    public static function notifyBuyers() {
        $query=static::find()->where('pay_status!=:pay_status_confirmed and no_payment_buyer_notified=0 and closed_dt<DATE_SUB(NOW(),INTERVAL 3 DAY)',[':pay_status_confirmed'=>static::PAY_STATUS_CONFIRMED]);

        foreach ($query->batch() as $offerRequests) {
            foreach ($offerRequests as $offerRequest) {
                Yii::$app->db->transaction(function($db) use ($offerRequest) {
                    $offerRequest->notifyBuyer();
                });
            }
        }
    }

    public function notifySeller() {
        // if offer was deleted this may fail with error
        if ($this->offer) {
            UserEvent::addOfferRequestNotifyNoPaymentSeller($this);
        }
        $this->no_payment_seller_notified=1;
        $this->save();
    }

    public static function notifySellers() {
        $query=static::find()->where('pay_status!=:pay_status_confirmed and no_payment_seller_notified=0 and closed_dt<DATE_SUB(NOW(),INTERVAL 6 DAY)',[':pay_status_confirmed'=>static::PAY_STATUS_CONFIRMED]);

        foreach ($query->batch() as $offerRequests) {
            foreach ($offerRequests as $offerRequest) {
                Yii::$app->db->transaction(function($db) use ($offerRequest) {
                    $offerRequest->notifySeller();
                });
            }
        }
    }

    public function getOfferRequestModifications()
    {
        return $this->hasMany('\app\models\OfferRequestModification', ['offer_request_id' => 'id'])->orderBy('id desc');
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

\yii\base\Event::on(OfferRequest::className(), \yii\db\ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
    if ($event->sender->offer->type==\app\models\Offer::TYPE_AUCTION) {
        $event->sender->offer->user->updateCounters(['stat_new_offers_requests' => 1]);
        \app\models\UserFollowerEvent::addNewOfferBet($event->sender);
        \app\components\ChatServer::statusUpdate([$event->sender->offer->user->id]);
    }
});

\yii\base\Event::on(OfferRequest::className(), \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE, function ($event) {
    $event->sender->setClosedDtIfNecessary();
    $event->sender->decreaseBuyerOfferRequestNoPaymentNotifications();

    $changedStatus=$event->sender->oldAttributes['status']!=$event->sender->status;
    $changedPayStatus=$event->sender->oldAttributes['pay_status']!=$event->sender->pay_status;

    if ($event->sender->status==OfferRequest::STATUS_ACCEPTED &&
        $event->sender->pay_status==OfferRequest::PAY_STATUS_CONFIRMED && ($changedStatus || $changedPayStatus)) {
        \app\models\UserEvent::addOfferFeedbackNotification($event->sender);
        \app\models\UserEvent::addOfferCounterFeedbackNotification($event->sender);
        \app\models\UserFollowerEvent::addNewOfferBuy($event->sender);
    }

    if ($event->sender->status==OfferRequest::STATUS_ACCEPTED &&
        $event->sender->pay_status==OfferRequest::PAY_STATUS_CONFIRMED && ($changedStatus || $changedPayStatus) ||
        ((!$event->sender->oldAttributes['user_feedback_id'] && $event->sender->user_feedback_id) || (!$event->sender->oldAttributes['counter_user_feedback_id'] && $event->sender->counter_user_feedback_id))) {
        $event->sender->on(\yii\db\ActiveRecord::EVENT_AFTER_UPDATE,function($event) {
            $event->sender->offer->user->updateStatAwaitingFeedbacks();
            $event->sender->user->updateStatAwaitingFeedbacks();
        });
    }
});

\yii\base\Event::on(OfferRequest::className(), \yii\db\ActiveRecord::EVENT_AFTER_UPDATE, function ($event) {
    \app\models\User::updateUserOfferRequestCompletedInterest($event->sender->user_id);
});

