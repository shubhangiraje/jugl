<?php

namespace app\models;

use Yii;


class UserFollowerEvent extends \app\models\base\UserFollowerEvent
{
    const TYPE_NEW_OFFER='NEW_OFFER';
    const TYPE_NEW_SEARCH_REQUEST='NEW_SEARCH_REQUEST';
    const TYPE_OFFER_BUY='OFFER_BUY';
    const TYPE_OFFER_BET='OFFER_BET';
    const TYPE_NEW_SEARCH_REQUEST_OFFER='NEW_SEARCH_REQUEST_OFFER';
    const TYPE_NEW_REFERRAL='NEW_REFERRAL';
    const TYPE_NEW_TROLLBOX_MESSAGE='NEW_TROLLBOX_MESSAGE';
    const TYPE_NEW_TROLLBOX_MESSAGE_COMMENT='NEW_TROLLBOX_MESSAGE_COMMENT';
    const TYPE_NEW_INFO_COMMENT='NEW_INFO_COMMENT';

    private static function createUserEvent($data) {
        $data['dt']=(new \app\components\EDateTime())->sql();

        $followers=Yii::$app->db->createCommand("
             select follower_user_id 
             from user_follower 
             where user_id=:user_id
             ",[':user_id'=>$data['user_id']]
        )->queryColumn();

        $rows=[];
        foreach($followers as $follower) {
            $data['follower_user_id']=$follower;
            $rows[]=$data;
        }

        \app\models\User::updateAll(['new_follower_events'=>1],['id'=>$followers]);
        Yii::$app->db->createCommand()->batchInsert(static::tableName(), array_keys($data),$rows)->execute();
    }

    public static function addNewOffer($offer) {
        $data=[
            'user_id'=>$offer->user_id,
            'type'=>static::TYPE_NEW_OFFER,
            'text'=>Yii::t('app','hat eine neue Werbung [offer:{offerId}]\'{offerTitle}\'[/offer] veröffentlicht',[
                'offerId'=>$offer->id,
                'offerTitle'=>$offer->title
            ])
        ];

        static::createUserEvent($data);
    }

    public static function addNewSearchRequest($searchRequest) {
        $data=[
            'user_id'=>$searchRequest->user_id,
            'type'=>static::TYPE_NEW_SEARCH_REQUEST,
            'text'=>Yii::t('app','hat einen neuen Suchauftrag [searchRequest:{searchRequestId}]\'{searchRequestTitle}\'[/searchRequest]',[
                'searchRequestId'=>$searchRequest->id,
                'searchRequestTitle'=>$searchRequest->title
            ])
        ];

        static::createUserEvent($data);
    }

    public static function addNewOfferBuy($offerRequest) {
        $data=[
            'user_id'=>$offerRequest->user_id,
            'type'=>static::TYPE_OFFER_BUY,
            'text'=>Yii::t('app','hat folgendes gekauft: [offer:{offerId}]\'{offerTitle}\'[/offer]',[
                'offerId'=>$offerRequest->offer->id,
                'offerTitle'=>$offerRequest->offer->title
            ])
        ];

        static::createUserEvent($data);
    }

    public static function addNewOfferBet($offerRequest) {
        $data=[
            'user_id'=>$offerRequest->user_id,
            'type'=>static::TYPE_OFFER_BET,
            'text'=>Yii::t('app','hat ein Gebot auf [offer:{offerId}]\'{offerTitle}\'[/offer] abgegeben',[
                'offerId'=>$offerRequest->offer->id,
                'offerTitle'=>$offerRequest->offer->title
            ])
        ];

        static::createUserEvent($data);
    }

    public static function addNewSearchRequestOffer($searchRequestOffer) {
        $data=[
            'user_id'=>$searchRequestOffer->user_id,
            'type'=>static::TYPE_NEW_SEARCH_REQUEST_OFFER,
            'text'=>Yii::t('app','hat ein Angebot auf [searchRequest:{searchRequestId}]\'{searchRequestTitle}\'[/searchRequest] abgegeben',[
                'searchRequestId'=>$searchRequestOffer->searchRequest->id,
                'searchRequestTitle'=>$searchRequestOffer->searchRequest->title
            ])
        ];

        static::createUserEvent($data);
    }

    public static function addNewNetworkMember($user,$newUser) {
        $data=[
            'user_id'=>$user->id,
            'type'=>static::TYPE_NEW_REFERRAL,
            'text'=>Yii::t('app','hat einen neuen Mitglied in seinem Team: [userProfile:{userId}]"{userName}"[/userProfile]',[
                'userId'=>$newUser->id,
                'userName'=>trim($newUser->first_name.' '.$newUser->last_name),
            ])
        ];

        static::createUserEvent($data);
    }

    public static function addNewTrollboxMessage($trollboxMessage) {
        $data=[
            'user_id'=>$trollboxMessage->user_id,
            'type'=>static::TYPE_NEW_TROLLBOX_MESSAGE,
            'text'=>Yii::t('app','hat einen neuen [groupChat:{groupChatId}]Beitrag[/groupChat] im Forum gepostet',[
                'groupChatId'=>$trollboxMessage->id
            ])
        ];

        static::createUserEvent($data);
    }

    public static function addNewInfoComment($infoComment) {
        $data=[
            'user_id'=>$infoComment->user_id,
            'type'=>static::TYPE_NEW_INFO_COMMENT,
            'text'=>Yii::t('app','hat einen neuen [info:{infoView}]Beitrag[/info] in Jugl-Wiki gepostet',[
                'infoView'=>$infoComment->info->view
            ])
        ];

        static::createUserEvent($data);
    }


    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','User ID'),
            'follower_user_id' => Yii::t('app','Follower User ID'),
            'dt' => Yii::t('app','Dt'),
            'text' => Yii::t('app','Text'),
            'type' => Yii::t('app','Type'),
        ];
    }
}
