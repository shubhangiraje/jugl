<?php

namespace app\models;

use app\components\EDateTime;
use Yii;

class UserEvent extends \app\models\base\UserEvent
{
    const TYPE_FRIEND_REQUEST='FRIEND_REQUEST';
    const TYPE_FRIEND_REQUEST_ACCEPTED='FRIEND_REQUEST_ACCEPTED';
    const TYPE_REGISTERED_BY_INVITATION='REGISTERED_BY_INVITATION';
    const TYPE_NEW_NETWORK_MEMBER='NEW_NETWORK_MEMBER';
    const TYPE_SEARCH_REQUEST_OFFER_NEW='SEARCH_REQUEST_OFFER_NEW';
    const TYPE_SEARCH_REQUEST_OFFER_ACCEPTED='SEARCH_REQUEST_OFFER_ACCEPTED';
    const TYPE_SEARCH_REQUEST_OFFER_DECLINED='SEARCH_REQUEST_OFFER_DECLINED';
    const TYPE_SEARCH_REQUEST_OFFER_FEEDBACK='SEARCH_REQUEST_OFFER_FEEDBACK';
    const TYPE_SEARCH_REQUEST_OFFER_COUNTER_FEEDBACK='SEARCH_REQUEST_OFFER_COUNTER_FEEDBACK';
    const TYPE_SEARCH_REQUEST_OFFER_MY_FEEDBACK='SEARCH_REQUEST_OFFER_MY_FEEDBACK';
    const TYPE_SEARCH_REQUEST_OFFER_MY_COUNTER_FEEDBACK='SEARCH_REQUEST_OFFER_MY_COUNTER_FEEDBACK';
    const TYPE_OFFER_REQUEST_NEW='OFFER_REQUEST_NEW';
    const TYPE_OFFER_REQUEST_NEW_BET='OFFER_REQUEST_NEW_BET';
    const TYPE_OFFER_REQUEST_ACCEPTED='OFFER_REQUEST_ACCEPTED';
    const TYPE_OFFER_REQUEST_ACCEPTED_PAYED='OFFER_REQUEST_ACCEPTED_PAYED';
    const TYPE_OFFER_REQUEST_DECLINED='OFFER_REQUEST_DECLINED';
    const TYPE_OFFER_REQUEST_FEEDBACK='OFFER_REQUEST_FEEDBACK';
    const TYPE_OFFER_REQUEST_COUNTER_FEEDBACK='OFFER_REQUEST_COUNTER_FEEDBACK';
    const TYPE_OFFER_REQUEST_MY_FEEDBACK='OFFER_REQUEST_MY_FEEDBACK';
    const TYPE_OFFER_REQUEST_MY_COUNTER_FEEDBACK='OFFER_REQUEST_MY_COUNTER_FEEDBACK';
    const TYPE_BROADCAST_MESSAGE='BROADCAST_MESSAGE';
    const TYPE_OFFER_REQUEST_PAYING_PAYED='OFFER_REQUEST_PAYING_PAYED';
    const TYPE_OFFER_REQUEST_PAYING_PAYED_CONFIRMED='OFFER_REQUEST_PAYING_PAYED_CONFIRMED';
    const TYPE_OFFER_REQUEST_PAYING_CONFIRMED='OFFER_REQUEST_PAYING_CONFIRMED';
    const TYPE_OFFER_MY_REQUEST='OFFER_MY_REQUEST';
    const TYPE_OFFER_MY_REQUEST_BET='OFFER_MY_REQUEST_BET';
    const TYPE_SEARCH_REQUEST_MY_OFFER='SEARCH_REQUEST_MY_OFFER';
    const TYPE_NEW_PAYOUT_REQUEST='NEW_PAYOUT_REQUEST';
    const TYPE_DOCUMENTS_VERIFICATION='DOCUMENTS_VERIFICATION';
    const TYPE_CHANGE_BALANCE_ADMINISTRATION='CHANGE_BALANCE_ADMINISTRATION';
    const TYPE_DOCUMENT_VALIDATION_SUCCESS='DOCUMENT_VALIDATION_SUCCESS';
    const TYPE_OFFER_REQUEST_PAYING_SELLER_NOTIFICATION='OFFER_REQUEST_PAYING_SELLER_NOTIFICATION';
    const TYPE_OFFER_REQUEST_PAYING_BUYER_NOTIFICATION='OFFER_REQUEST_PAYING_BUYER_NOTIFICATION';
    const TYPE_OFFER_REQUEST_PAYING_WARNING='OFFER_REQUEST_PAYING_WARNING';
    const TYPE_OFFER_REQUEST_PAYING_COMPLAINT='OFFER_REQUEST_PAYING_COMPLAINT';
    const TYPE_OFFER_BUGET_USED_90='OFFER_BUGET_USED_90';
    const TYPE_OFFER_BUGET_USED_100='OFFER_BUGET_USED_100';
    const TYPE_NOT_FINISHED_REGISTRATION='NOT_FINISHED_REGISTRATION';
    const TYPE_TEAM_CHANGE='TEAM_CHANGE';
    const TYPE_TEAM_FEEDBACK='TEAM_FEEDBACK';
    const TYPE_SEARCH_REQUEST_FEEDBACK_NOTIFICATION='SEARCH_REQUEST_FEEDBACK_NOTIFICATION';
    const TYPE_OFFER_FEEDBACK_NOTIFICATION='OFFER_FEEDBACK_NOTIFICATION';
    const TYPE_SPAM='SPAM';
    const TYPE_VIP_NOTIFICATION='VIP_NOTIFICATION';
    const TYPE_LIKE='LIKE';
    const TYPE_COMMENT='COMMENT';
    const TYPE_VIDEO_IDENT_UNMATCH='VIDEO_IDENT_UNMATCH';

    const SELECT_WAS_WIRD_MIR_ANGEBOTEN = 'WAS_WIRD_MIR_ANGEBOTEN';
    const SELECT_AKZEPTIERTES_ANGEBOT = 'AKZEPTIERTES_ANGEBOT';
    const SELECT_ICH_WURDE_BEWERTET = 'ICH_WURDE_BEWERTET';
    const SELECT_MY_FEEDBACKS = 'MY_FEEDBACKS';
    const SELECT_OFFER_REQUEST_SOLD = 'OFFER_REQUEST_SOLD';


    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','User ID'),
            'dt' => Yii::t('app','Dt'),
            'type' => Yii::t('app','Type'),
            'second_user_id' => Yii::t('app','Second User ID'),
            'text' => Yii::t('app','Text'),
        ];
    }

    public function getPushTitle() {
        if ($this->secondUser) {
            return $this->secondUser->first_name.' '.$this->secondUser->last_name;
        }

        if (!$this->secondUser) {
            $user=\app\models\User::getAdministrationUser();

            return trim($user->first_name.' '.$user->last_name);
        }
    }

    public function getPushText() {
        $text=str_replace('[br][/br]',"\n",$this->text);
        $text=str_replace('[jugl][/jugl]',"J",$text);
        $text=preg_replace('%\\[([a-zA-Z]+(:\d+)+|/[a-zA-Z]+)\]%','',$text);
        return $text;
    }

    public static function addLikeInfoCommentNotification($infoCommentVote) {
        if ($infoCommentVote->infoComment->user->setting_notification_likes) {
            $event=new self;
            $event->user_id=$infoCommentVote->infoComment->user_id;
            $event->type=static::TYPE_LIKE;

            $event->text=Yii::t('app','Dein Beitrag [info:{infoView}]"{text}"[/info] wurde gerade von dem User {user} gelikt', [
                'text'=>$infoCommentVote->infoComment->comment,
                'infoView'=>$infoCommentVote->infoComment->info->view,
                'user'=>$infoCommentVote->user->name
            ]);

            $event->save();

            return $event->id;
        }
    }

    public static function addLikeTrollboxMessageNotification($trollboxMessageVote) {
        if ($trollboxMessageVote->trollboxMessage->user->setting_notification_likes) {
            $event=new self;
            $event->user_id=$trollboxMessageVote->trollboxMessage->user_id;
            $event->type=static::TYPE_LIKE;

            $event->text=Yii::t('app','Dein Beitrag [groupChat:{groupChatId}]"{text}"[/groupChat] wurde gerade von dem User {user} gelikt', [
                'groupChatId'=>$trollboxMessageVote->trollboxMessage->id,
                'text'=>$trollboxMessageVote->trollboxMessage->text,
                'user'=>$trollboxMessageVote->user->name
            ]);

            $event->save();

            return $event->id;
        }
    }

    public static function addSearchRequestFeedbackNotification($searchRequestOffer) {
        $event=new self;
        $event->user_id=$searchRequestOffer->searchRequest->user_id;
        $event->type=static::TYPE_SEARCH_REQUEST_FEEDBACK_NOTIFICATION;

        $event->text=Yii::t('app','Bewerte den Nutzer {user} für das Handel bezgl. des Suchauftrags [searchRequest:{searchRequestId}]"{searchRequestTitle}"[/searchRequest]. [searchRequestOfferFeedback:{searchRequestOfferId}]', [
            'searchRequestTitle'=>$searchRequestOffer->searchRequest->title,
            'searchRequestOfferId'=>$searchRequestOffer->id,
            'searchRequestId'=>$searchRequestOffer->searchRequest->id,
            'user'=>$searchRequestOffer->user->name
        ]);

        $event->save();

        return $event->id;
    }

    public static function addSearchRequestCounterFeedbackNotification($searchRequestOffer) {
        $event=new self;
        $event->user_id=$searchRequestOffer->user_id;
        $event->type=static::TYPE_SEARCH_REQUEST_FEEDBACK_NOTIFICATION;

        $event->text=Yii::t('app','Bewerte den Nutzer {user} für das Handel bezgl. des Suchauftrags [searchRequest:{searchRequestId}]"{searchRequestTitle}"[/searchRequest]. [searchRequestOfferCounterFeedback:{searchRequestOfferId}]', [
            'searchRequestTitle'=>$searchRequestOffer->searchRequest->title,
            'searchRequestOfferId'=>$searchRequestOffer->id,
            'searchRequestId'=>$searchRequestOffer->searchRequest->id,
            'user'=>$searchRequestOffer->searchRequest->user->name
        ]);

        $event->save();

        return $event->id;
    }


    public static function getFrontData($events) {
        $data=[];
        foreach($events as $event) {
            $data[]=[
                'id'=>$event->id,
                'dt'=>(new EDateTime($event->dt))->js(),
                'type'=>$event->type,
                'text'=>$event->text,
                'user'=>!$event->second_user_id ? \app\models\User::getAdministrationUser()->getShortData():$event->secondUser->getShortData()
            ];
        }

        return $data;
    }

    public static function addOfferFeedbackNotification($offerRequest) {
        $event=new self;
        $event->user_id=$offerRequest->offer->user_id;
        $event->type=static::TYPE_OFFER_FEEDBACK_NOTIFICATION;

        $event->text=Yii::t('app','Bewerte den Nutzer {user} für das Handel bezgl. der Anzeige [offer:{offerId}]"{offerTitle}"[/offer]. [offerRequestFeedback:{offerRequestId}]', [
            'offerTitle'=>$offerRequest->offer->title,
            'offerRequestId'=>$offerRequest->id,
            'offerId'=>$offerRequest->offer->id,
            'user'=>$offerRequest->user->name
        ]);

        $event->save();

        return $event->id;
    }

    public static function addVipPlusBonusNotification($user,$sum,$sum2) {
        $event=new self;
        $event->user_id=$user->id;
        $event->type=static::TYPE_BROADCAST_MESSAGE;

        $event->text=Yii::t('app','Schade, dass Du nur {packet} Mitglied bist, wärst du {packet2} Midglied, hättest Du statt {sum} Jugls, {sum2} Jugls erhalten [upgradePacket]', [
            'packet'=>\app\models\User::getPacketList()[$user->packet],
            'packet2'=>\app\models\User::getPacketList()[User::PACKET_VIP_PLUS],
            'sum'=>\app\components\Helper::formatPrice($sum),
            'sum2'=>\app\components\Helper::formatPrice($sum2),
        ]);

        $event->save();

        return $event->id;
    }

    public static function addNetworkMoveRequest($user,$destUser) {
        $event=new self;
        $event->user_id=$destUser->id;
        $event->type=static::TYPE_TEAM_CHANGE;

        $event->text=Yii::t('app','Dein Teamleader {parentName} möchte Dir den Teammitglied {name} und seine Struktur übergeben. Möchtest Du die Teamführung übernehmen? [networkMoveAccept:{fromParentId}:{toParentId}:{userId}] [networkMoveReject:{fromParentId}:{toParentId}:{userId}]',[
            'parentName'=>$user->parent->name,
            'name'=>$user->name,
            'fromParentId'=>$user->parent_id,
            'toParentId'=>$destUser->id,
            'userId'=>$user->id
        ]);

        $event->save();

        return $event->id;
    }

    public static function addUserStickRequest($userStickRequest) {
        $event=new self;
        $event->user_id=$userStickRequest->user_id;
        $event->type=static::TYPE_TEAM_CHANGE;

        $event->text=Yii::t('app','Dein Teamleader (PremiumPlus-Mitglied) {name} möchte Dich fest in sein Team aufnehmen. PremiumPlus-Mitglieder können Dir aktiv dabei helfen, Dein Netzwerk aufzubauen. Hier die Nachricht von {name}: {text} [stickParentAccept] [stickParentReject]',[
            'name'=>Yii::$app->user->identity->name,
            'text'=>$userStickRequest->text,
        ]);

        $event->save();

        return $event->id;
    }

    private static function clearStickParentEvents($user) {
        $replaceFrom1="[stickParentAccept]";
        $replaceFrom2="[stickParentReject]";


        $mevents=static::findBySql('select * from user_event where user_id=:user_id and type=:type and text like(:like)',[
            ':user_id'=>$user->id,
            ':type'=>static::TYPE_TEAM_CHANGE,
            ':like'=>"%$replaceFrom1%"
        ])->all();

        foreach($mevents as $mevent) {
            $mevent->text=str_replace($replaceFrom1,' ',$mevent->text);
            $mevent->text=str_replace($replaceFrom2,' ',$mevent->text);
            $mevent->save();
        }

        return $mevents;
    }

    public static function addStickParentReject() {
        $event=new self;
        $event->user_id=Yii::$app->user->identity->parent_id;
        $event->second_user_id=Yii::$app->user->id;
        $event->type=static::TYPE_TEAM_CHANGE;

        $event->text=Yii::t('app','hat Deine Anfrage zum Teamfestlegen abgelehnt.');

        $event->save();

        return static::clearStickParentEvents(Yii::$app->user->identity);
    }

    public static function addStickParentAccept() {
        $event=new self;
        $event->user_id=Yii::$app->user->identity->parent_id;
        $event->second_user_id=Yii::$app->user->id;
        $event->type=static::TYPE_TEAM_CHANGE;

        $event->text=Yii::t('app','ist nun fest in Deinem Team.');

        $event->save();

        return static::clearStickParentEvents(Yii::$app->user->identity);
    }

    public static function addStickParentRequestExpired($user) {
        $event=new self;
        $event->user_id=$user->parent_id;
        $event->second_user_id=$user->id;
        $event->type=static::TYPE_TEAM_CHANGE;

        $event->text=Yii::t('app','hat nicht auf Deine Anfrage zum Teamfestlegen geantwortet. Es ist nun wieder für alle Mitglieder von jugl.net zum Abwerben sichtbar.');

        $event->save();

        return static::clearStickParentEvents($user);
    }

    private static function clearNetworkMoveEvents($fromParentUser,$toParentUser,$user) {
        $replaceFrom1="[networkMoveAccept:{$fromParentUser->id}:{$toParentUser->id}:{$user->id}]";
        $replaceFrom2="[networkMoveReject:{$fromParentUser->id}:{$toParentUser->id}:{$user->id}]";


        $mevents=static::findBySql('select * from user_event where user_id=:user_id and type=:type and text like(:like)',[
            ':user_id'=>$toParentUser->id,
            ':type'=>static::TYPE_TEAM_CHANGE,
            ':like'=>"%$replaceFrom1%"
        ])->all();

        foreach($mevents as $mevent) {
            $mevent->text=str_replace($replaceFrom1,' ',$mevent->text);
            $mevent->text=str_replace($replaceFrom2,' ',$mevent->text);
            $mevent->save();
        }

        return $mevents;
    }

    public static function addNetworkMoveReject($fromParentUser,$toParentUser,$user) {
        $event=new self;
        $event->user_id=$fromParentUser->id;
        $event->second_user_id=$toParentUser->id;
        $event->type=static::TYPE_TEAM_CHANGE;

        $event->text=Yii::t('app','hat die Teamleaderübernahme für {name} abgelehnt.',[
            'name'=>$user->name
        ]);

        $event->save();

        return static::clearNetworkMoveEvents($fromParentUser,$toParentUser,$user);
    }

    public static function addNetworkMoveAccept($fromParentUser,$toParentUser,$user) {
        $event=new self;
        $event->user_id=$fromParentUser->id;
        $event->second_user_id=$toParentUser->id;
        $event->type=static::TYPE_TEAM_CHANGE;

        $event->text=Yii::t('app','übernimmt die Teamleaderschaft für {name}.',[
            'name'=>$user->name
        ]);

        $event->save();

        return static::clearNetworkMoveEvents($fromParentUser,$toParentUser,$user);
    }

    public static function addFreeRegistrationsLimitReached($user) {
        $event=new self;
        $event->user_id=$user->id;
        $event->type=static::TYPE_BROADCAST_MESSAGE;

        switch($user->packet) {
            case \app\models\User::PACKET_VIP_PLUS:
                $event->text=Yii::t('app','Du hast deinen Einladungskontingent leider erschöpft. [upgradePacket]');
                break;
            case \app\models\User::PACKET_VIP:
                $event->text=Yii::t('app','Du hast deinen Einladungskontingent leider erschöpft. Jetzt auf PremiumPlus upgraden und viel mehr Einladungen abschicken. [upgradePacket]');
                break;
            default:
                $event->text=Yii::t('app','Du hast deinen Einladungskontingent leider erschöpft. Jetzt auf Premium/PremiumPlus upgraden und viel mehr Einladungen abschicken. [upgradePacket]');
                break;
        }

        $event->save();

        return $event->id;
    }

    public static function addOfferCounterFeedbackNotification($offerRequest) {
        $event=new self;
        $event->user_id=$offerRequest->user_id;
        $event->type=static::TYPE_OFFER_FEEDBACK_NOTIFICATION;

        $event->text=Yii::t('app','Bewerte den Nutzer {user} für das Handel bezgl. der Anzeige [offer:{offerId}]"{offerTitle}"[/offer]. [offerRequestCounterFeedback:{offerRequestId}]', [
            'offerTitle'=>$offerRequest->offer->title,
            'offerRequestId'=>$offerRequest->id,
            'offerId'=>$offerRequest->offer->id,
            'user'=>$offerRequest->offer->user->name
        ]);

        $event->save();

        return $event->id;
    }

    public static function addTeamFeedback($feedback) {
        $event=new self;
        $event->user_id=$feedback->user_id;
        $event->second_user_id=$feedback->second_user_id;
        $event->type=static::TYPE_TEAM_FEEDBACK;

        if ($feedback->isNewRecord) {
            $event->text=Yii::t('app','{user} hat Dein Teamleading bewertet. [teamFeedbacks]', ['user'=>$feedback->secondUser->name]);
        } else {
            $event->text=Yii::t('app','{user} hat seine Teamleadingbewertung geändert. [teamFeedbacks]', ['user'=>$feedback->secondUser->name]);
        }
        $event->save();

        return $event->id;
    }
/*
    public static function addSpam($userId,$secondUserId,$text) {
        $event=new self;
        $event->user_id=$userId;
        $event->second_user_id=$secondUserId;
        $event->text=$text;
        $event->type=static::TYPE_SPAM;
        $event->save();

        return $event->id;
    }
*/
    public static function addTeamChange($userId,$secondUserId,$text) {
        $event=new self;
        $event->user_id=$userId;
        $event->second_user_id=$secondUserId;
        $event->text=$text;
        $event->type=static::TYPE_TEAM_CHANGE;
        $event->save();

        return $event->id;
    }

    public static function NotFinishedRegistrationNotification($user) {
        $event=new self;
        $event->user_id=$user->parent_id;
        $event->second_user_id=$user->id;
        $event->type=static::TYPE_NOT_FINISHED_REGISTRATION;
        $event->text=Yii::t('app',"Wenn Du Dich wunderst, warum Du noch keinen Bonus für die Einladung von {user} bekommen hast, welcher sich direkt über Deinen Registrationslink angemeldet hat, erkläre ihm bitte, dass er folgende Punkte erfüllen muss:[br][/br][br][/br] 1. sich die App herunterladen und sich einloggen[br][/br] 2. seine Daten vervollständigen[br][/br] 3. eine Aktion in der App ausführen[br][/br] 4. Mitgliedschaft auswählen[br][/br] 5. seine Telefonnummer via SMS Verifizierung bestätigen[br][/br][br][/br] Sobald {user} diese Punkte erfüllt hat, wobei hier die Reihenfolge unerheblich ist, erhältst Du den Bonus.",[
            'user'=>$user->getName()
        ]);
        $event->save();
    }

    public static function addOfferBudgetUsed90($offer) {
        $event=new self;
        $event->user_id=$offer->user->id;
        $event->type=static::TYPE_OFFER_BUGET_USED_90;
        $event->text=Yii::t('app',"Das Budget für Dein Angebot '{title}' ist zu 90% verbraucht. [myOffer:{offerId}]Jetzt Budget aufladen[/myOffer]",[
            'title'=>$offer->title,
            'offerId' => $offer->id
        ]);
        $event->save();
    }

    public static function addOfferBudgetUsed100($offer) {
        $event=new self;
        $event->user_id=$offer->user->id;
        $event->type=static::TYPE_OFFER_BUGET_USED_100;
        $event->text=Yii::t('app',"Das Budget für Dein Angebot '{title}' ist verbraucht und es ist abgelaufen. Du kannst es unter 'Meine Werbung' jederzeit reaktivieren. [myOffer:{offerId}]Jetzt Budget aufladen[/myOffer]",[
            'title'=>$offer->title,
            'offerId' => $offer->id
        ]);
        $event->save();
    }

    public static function addDocumentValidationSuccess($user) {
        $event=new self;
        $event->user_id=$user->id;
        $event->type=static::TYPE_DOCUMENT_VALIDATION_SUCCESS;
        $event->text=Yii::t('app',"Ihre Identitätsprüfung wurde erfolgreich abgeschlossen. Sie können sich nun Jugls auszahlen lassen, unter: 'Konto' -> 'Jugls Auszahlen' -> 'Packet auswählen'.");
        $event->save();
    }

    public static function addNewPayoutRequest($payoutRequest) {
        $event=new self;
        $event->user_id=$payoutRequest->user_id;
        $event->second_user_id=$payoutRequest->user_id;
        $event->type=static::TYPE_NEW_PAYOUT_REQUEST;

        switch($payoutRequest->type) {
            case \app\models\PayOutRequest::TYPE_JUGLS:
                $event->text=Yii::t('app','Deine Anfrage zur Auszahlung über {sum_jugl} Jugl-Punkte ({sum_currency}€) ist bei uns eingegangen und wird schnellstmöglich bearbeitet.',[
                    'sum_jugl'=>$payoutRequest->jugl_sum,
                    'sum_currency'=>$payoutRequest->currency_sum
                ]);
                break;
            case \app\models\PayOutRequest::TYPE_TOKEN_DEPOSIT:
                $event->text=Yii::t('app','Deine Anfrage zur Auszahlung des Tokenbetrags {sum_currency}€ ist bei uns eingegangen und wird schnellstmöglich bearbeitet.',[
                    'sum_currency'=>$payoutRequest->currency_sum
                ]);
                break;
            case \app\models\PayOutRequest::TYPE_TOKEN_DEPOSIT_PERCENT:
                $event->text=Yii::t('app','Deine Anfrage zur Auszahlung des Zinsertrags {sum_jugl} Jugl-Punkte ({sum_currency}€) ist bei uns eingegangen und wird schnellstmöglich bearbeitet.',[
                    'sum_jugl'=>$payoutRequest->jugl_sum,
                    'sum_currency'=>$payoutRequest->currency_sum
                ]);
                break;
        }
        $event->save();
    }

    public static function addNewNetworkMember($user,$secondUser) {
        $event=new self;
        $event->user_id=$user->id;
        $event->second_user_id=$secondUser->id;
        $event->type=static::TYPE_NEW_NETWORK_MEMBER;
        $event->text=Yii::t('app','ist jetzt in Deinem Netzwerk. Bitte nimm über den Messenger Kontakt zu Deinem neuen Mitglied auf und erkläre ihm, wie Jugl funktioniert.');
        $event->save();
    }

    public static function addRegisteredByInvitation($user,$secondUser) {
        $event=new self;
        $event->user_id=$user->id;
        $event->second_user_id=$secondUser->id;
        $event->type=static::TYPE_REGISTERED_BY_INVITATION;
        $event->text=Yii::t('app','hat Deine Einladung akzeptiert und sich bei jugl.net registriert');
        $event->save();
    }

    public static function addFriendshipRequest($user,$secondUser) {
        $event=new self;
        $event->user_id=$user->id;
        $event->second_user_id=$secondUser->id;
        $event->type=static::TYPE_FRIEND_REQUEST;
        $event->text=Yii::t('app','hat Dir eine Kontaktanfrage gesendet');
        $event->save();
    }

    public static function addFriendshipRequestAccepted($user,$secondUser) {
        $event=new self;
        $event->user_id=$user->id;
        $event->second_user_id=$secondUser->id;
        $event->type=static::TYPE_FRIEND_REQUEST_ACCEPTED;
        $event->text=Yii::t('app','hat Deine Kontaktanfrage bestätigt');
        $event->save();
    }

    public static function addSearchRequestMyOffer($searchRequestOffer) {
        $event=new self;
        $event->user_id=$searchRequestOffer->user_id;
        $event->second_user_id=$searchRequestOffer->searchRequest->user_id;
        $event->type=static::TYPE_SEARCH_REQUEST_MY_OFFER;
        $event->text=Yii::t('app','Ich habe ein [searchRequestOffer:{searchRequestId}:{searchRequestOfferId}]Angebot[/searchRequestOffer] auf die Suchanzeige [searchRequest:{searchRequestId}]"{searchRequestTitle}"[/searchRequest] abgegeben',[
            'searchRequestOfferId'=>$searchRequestOffer->id,
            'searchRequestId'=>$searchRequestOffer->search_request_id,
            'searchRequestTitle'=>$searchRequestOffer->searchRequest->title
        ]);
        $event->save();
    }

    public static function addNewSearchRequestOffer($searchRequestOffer) {
        $event=new self;
        $event->user_id=$searchRequestOffer->searchRequest->user_id;
        $event->second_user_id=$searchRequestOffer->user_id;
        $event->type=static::TYPE_SEARCH_REQUEST_OFFER_NEW;
        $event->text=Yii::t('app','hat Dir ein [searchRequestOffer:{searchRequestId}:{searchRequestOfferId}]Angebot[/searchRequestOffer] auf Deiner Suchanzeige [searchRequest:{searchRequestId}]"{searchRequestTitle}"[/searchRequest] geschickt',[
            'searchRequestOfferId'=>$searchRequestOffer->id,
            'searchRequestId'=>$searchRequestOffer->search_request_id,
            'searchRequestTitle'=>$searchRequestOffer->searchRequest->title
        ]);
        $event->save();

        static::addSearchRequestMyOffer($searchRequestOffer);
    }

    public static function addSearchRequestOfferDeclined($searchRequestOffer) {
        $event=new self;
        $event->user_id=$searchRequestOffer->user_id;
        $event->second_user_id=$searchRequestOffer->searchRequest->user_id;
        $event->type=static::TYPE_SEARCH_REQUEST_OFFER_DECLINED;

        $rejectComment=$searchRequestOffer->reject_comment;
        if ($rejectComment!='') {
            $rejectComment.='.';
        }

        $event->text=Yii::t('app','hat Dein [searchRequestOffer:{searchRequestId}:{searchRequestOfferId}]Angebot[/searchRequestOffer] auf die Suchanzeige [searchRequest:{searchRequestId}]"{searchRequestTitle}"[/searchRequest] abgelehnt. Grund: {rejectReason}. {rejectComment}',[
            'searchRequestOfferId'=>$searchRequestOffer->id,
            'searchRequestId'=>$searchRequestOffer->search_request_id,
            'searchRequestTitle'=>$searchRequestOffer->searchRequest->title,
            'rejectReason'=>$searchRequestOffer->rejectReasonLabel,
            'rejectComment'=>$rejectComment
        ]);
        $event->save();
    }

    public static function addOfferRequestNotifyNoPaymentBuyer($offerRequest)
    {
        if (!$offerRequest->offer->id) {
            return;
        }

        $event=new self;
        $event->user_id=$offerRequest->user_id;
        //$event->second_user_id=$offerRequest->offer->user_id;
        $event->type=static::TYPE_OFFER_REQUEST_PAYING_BUYER_NOTIFICATION;
        $event->text=Yii::t('app','Du hast noch offene Zahlungsabläufe. Bitte kontrolliere ob du das
Angebot [offer:{offerId}]"{offerTitle}"[/offer] bereits bezahlt hast. Der Verkäufer hat den Geldeingang noch
nicht bestätigt.',[
            'offerId'=>$offerRequest->offer->id,
            'offerTitle'=>$offerRequest->offer->title,
        ]);
        $event->save();
    }

    public static function addMahnungWarning($offerRequest) {
        $event=new self;
        $event->user_id=$offerRequest->user_id;
        //$event->second_user_id=$offerRequest->offer->user_id;
        $event->type=static::TYPE_OFFER_REQUEST_PAYING_WARNING;
        $event->text=Yii::t('app','Allerletzte Zahlungserinnerung! Du hast bereits dreimal nicht bezahlt. Bitte bezahle umgehend alle Deinen offenen Käufe sonst wirst du gesperrt!',[
        ]);
        $event->save();
    }

    public static function addOfferRequestNotifyNoPaymentSeller($offerRequest)
    {
        $event=new self;
        $event->user_id=$offerRequest->offer->user_id;
        //$event->second_user_id=$offerRequest->offer->user_id;
        $event->type=static::TYPE_OFFER_REQUEST_PAYING_SELLER_NOTIFICATION;
        $event->text=Yii::t('app','Du hast den Geldeingang für das Angebot [offer:{offerId}]"{offerTitle}"[/offer] von {user} noch nicht bestätigt. Wenn der Käufer bereits bezahlt hat, dann bestätige dies jetzt. [offerPayConfirm:{offerId}:{offerRequestId}] Wenn der Käufer noch nicht bezahlt hat, kannst Du ihn hiermit abmahnen [offerPayNotifyBuyer:{offerId}:{offerRequestId}]',[
            'offerId'=>$offerRequest->offer->id,
            'offerRequestId'=>$offerRequest->id,
            'offerTitle'=>$offerRequest->offer->title,
            'user'=>$offerRequest->user->name
        ]);
        $event->save();
    }

    public static function addOfferRequestPaymentComplaint($offerRequest,$text) {
        $event=new self;
        $event->user_id=$offerRequest->user_id;
        $event->second_user_id=$offerRequest->offer->user_id;
        $event->type=static::TYPE_OFFER_REQUEST_PAYING_COMPLAINT;
        $event->text=$text.($offerRequest->pay_status==OfferRequest::PAY_STATUS_INVITED ?" [offerPay:{$offerRequest->offer->id}:{$offerRequest->id}]":"");
        $event->save();

        $replaceFrom="[offerPayNotifyBuyer:{$offerRequest->offer->id}:{$offerRequest->id}]";
        $mevents=static::findBySql('select * from user_event where user_id=:user_id and type=:type and text like(:like)',[
            ':user_id'=>$offerRequest->offer->user_id,
            ':type'=>static::TYPE_OFFER_REQUEST_PAYING_SELLER_NOTIFICATION,
            ':like'=>"%$replaceFrom%"
        ])->all();

        foreach($mevents as $mevent) {
            $mevent->text=str_replace($replaceFrom,' ',$mevent->text);
            $mevent->save();
        }

        return $mevents;
    }

    public static function addVipNotification($user) {
        $event=new self;
        $event->user_id=$user->id;
        $event->type=static::TYPE_VIP_NOTIFICATION;

        $now=new \app\components\EDateTime();
        $vipActiveTill=new \app\components\EDateTime($user->vip_active_till);
        if ($now<$vipActiveTill) {
            $event->text=Yii::t('app','ACHTUNG: Deine Premium-Mitgliedschaft läuft bald ab. [vipProlongation]');
        } else {
            $event->text=Yii::t('app','Deine Premium-Mitgliedschaft ist abgelaufen. [vipUpgrade]');
        }

        $event->save();
    }

    public static function addOfferPayConfirmed($offerRequest) {
        $event=new self;
        $event->user_id=$offerRequest->user_id;
        $event->second_user_id=$offerRequest->offer->user_id;
        $event->type=static::TYPE_OFFER_REQUEST_PAYING_CONFIRMED;
        $event->text=Yii::t('app','hat Geldeingang bestätigt. Deine Ware wird geliefert.',[
            'pay_data'=>$offerRequest->pay_data,
            'delivery_address'=>$offerRequest->delivery_address,
            'offerId'=>$offerRequest->offer->id,
            'offerTitle'=>$offerRequest->offer->title,
            'offerRequestId'=>$offerRequest->id
        ]);
        $event->save();

        $replaceFrom=" [offerPayConfirm:{$offerRequest->offer->id}:{$offerRequest->id}]";
        $replaceFrom2=" [offerPayNotifyBuyer:{$offerRequest->offer->id}:{$offerRequest->id}]";
        $mevents=static::findBySql('select * from user_event where user_id=:user_id and type=:type and (text like(:like) or text like(:like2))',[
            ':user_id'=>$offerRequest->offer->user_id,
            ':type'=>static::TYPE_OFFER_REQUEST_PAYING_SELLER_NOTIFICATION,
            ':like'=>"%$replaceFrom%",
            ':like2'=>"%$replaceFrom2%"
        ])->all();

        foreach($mevents as $mevent) {
            $mevent->type=static::TYPE_OFFER_REQUEST_PAYING_SELLER_NOTIFICATION;
            $mevent->text=str_replace($replaceFrom,' ',$mevent->text);
            $mevent->text=str_replace($replaceFrom2,' ',$mevent->text);
            $mevent->save();
        }

        $replaceFrom=" [offerPayConfirm:{$offerRequest->offer->id}:{$offerRequest->id}]";
        $mevents2=static::findBySql('select * from user_event where user_id=:user_id and type=:type and text like(:like)',[
            ':user_id'=>$offerRequest->offer->user_id,
            ':type'=>static::TYPE_OFFER_REQUEST_PAYING_PAYED,
            ':like'=>"%$replaceFrom%"
        ])->all();

        foreach($mevents2 as $mevent) {
            $mevent->offerPayConfirm($offerRequest,Yii::t('app','Ich habe den Geldeingang bestätigt'));
        }


        return array_merge($mevents,$mevents2);
    }

    public function offerPayConfirm($offerRequest,$replaceTo='') {
        $replaceFrom=" [offerPayConfirm:{$offerRequest->offer->id}:{$offerRequest->id}]";
        $this->type=static::TYPE_OFFER_REQUEST_PAYING_PAYED_CONFIRMED;
        $this->text=str_replace($replaceFrom,' '.$replaceTo,$this->text);
        $this->save();
    }

    public static function addOfferPayed($offerRequest) {
        $event=new self;
        $event->user_id=$offerRequest->offer->user_id;
        $event->second_user_id=$offerRequest->user_id;
        $event->type=static::TYPE_OFFER_REQUEST_PAYING_PAYED;

        $user=$offerRequest->user;
        if ($offerRequest->pay_method==\app\models\OfferRequest::PAY_METHOD_POD) {
            $event->text=Yii::t('app','Bitte setze dich mit dem Kunden [userProfile:{userId}]"{userName}"[/userProfile] zwecks der Terminabsprache in Verbindung. Er möchte den Artikel [offer:{offerId}]"{offerTitle}"[/offer] bei Abholung in Bar bezahlen. [offerPayConfirm:{offerId}:{offerRequestId}]',[
                'delivery_address'=>$offerRequest->delivery_address,
                'offerId'=>$offerRequest->offer->id,
                'offerTitle'=>$offerRequest->offer->title,
                'userId'=>$user->id,
                'userName'=>trim($user->first_name.' '.$user->last_name),
                'offerRequestId'=>$offerRequest->id
            ]);
        } else {
            if ($offerRequest->pay_method==\app\models\OfferRequest::PAY_METHOD_JUGLS) {
                $msg=Yii::t('app', 'hat Dein Angebot [offer:{offerId}]"{offerTitle}"[/offer] per {pay_data} bezahlt. Die Lieferadresse lautet: {delivery_address}.');
            } else {
                $msg=Yii::t('app', 'hat Dein Angebot [offer:{offerId}]"{offerTitle}"[/offer] per {pay_data} bezahlt. Die Lieferadresse lautet: {delivery_address}. [offerPayConfirm:{offerId}:{offerRequestId}]');
            }
            $event->text=Yii::t('app',$msg,[
                'pay_data'=>$offerRequest->pay_data,
                'delivery_address'=>$offerRequest->delivery_address,
                'offerId'=>$offerRequest->offer->id,
                'offerTitle'=>$offerRequest->offer->title,
                'offerRequestId'=>$offerRequest->id
            ]);
        }
        $event->save();

        $replaceFrom=" [offerPay:{$offerRequest->offer->id}:{$offerRequest->id}]";
        $mevents=static::findBySql('select * from user_event where user_id=:user_id and (type=:type or type=:type2) and text like(:like)',[
            ':user_id'=>$offerRequest->user_id,
            ':type'=>static::TYPE_OFFER_REQUEST_ACCEPTED,
            ':type2'=>static::TYPE_OFFER_REQUEST_PAYING_COMPLAINT,
            ':like'=>"%$replaceFrom%"
        ])->all();

        foreach($mevents as $mevent) {
            $mevent->type=static::TYPE_OFFER_REQUEST_ACCEPTED_PAYED;
            if ($offerRequest->pay_method!=\app\models\OfferRequest::PAY_METHOD_POD) {
                $mevent->text = str_replace($replaceFrom, '. ' . Yii::t('app', 'Ich habe das Angebot bezahlt'), $mevent->text);
            } else {
                $mevent->text = str_replace($replaceFrom, ' '.Yii::t('app','und wird sich mit dir in Verbindung setzen.'), $mevent->text);
            }
            $mevent->save();
        }

        return $event;
    }

    public static function addSearchRequestOfferAccepted($searchRequestOffer) {
        $event=new self;
        $event->user_id=$searchRequestOffer->user_id;
        $event->second_user_id=$searchRequestOffer->searchRequest->user_id;
        $event->type=static::TYPE_SEARCH_REQUEST_OFFER_ACCEPTED;
        $event->text=Yii::t('app','hat Dein [searchRequestOffer:{searchRequestId}:{searchRequestOfferId}]Angebot[/searchRequestOffer] auf die Suchanzeige [searchRequest:{searchRequestId}]"{searchRequestTitle}"[/searchRequest] akzeptiert',[
            'searchRequestOfferId'=>$searchRequestOffer->id,
            'searchRequestId'=>$searchRequestOffer->search_request_id,
            'searchRequestTitle'=>$searchRequestOffer->searchRequest->title
        ]);
        $event->save();
    }

    public static function addOfferMyRequest($offerRequest) {
        $event=new self;
        $event->user_id=$offerRequest->user_id;
        $event->second_user_id=$offerRequest->user_id;
        $event->type=static::TYPE_OFFER_MY_REQUEST;
        $event->text=Yii::t('app','Ich habe den Artikel [offer:{offerId}]"{offerTitle}"[/offer] gekauft',[
            'offerId'=>$offerRequest->offer_id,
            'offerTitle'=>$offerRequest->offer->title
        ]);
        $event->save();
    }

    public static function addNewOfferMyBet($offerRequest,$oldPrice) {
        $event=new self;
        $event->user_id=$offerRequest->user_id;
        $event->second_user_id=$offerRequest->user_id;
        $event->type=static::TYPE_OFFER_MY_REQUEST_BET;

        if ($oldPrice) {
            $event->text = Yii::t('app', 'Ich habe mein Gebot auf das Angebot [offer:{offerId}]"{offerTitle}"[/offer] von {oldBetPrice}€ auf {betPrice}€ verändert.', [
                'betPrice' => \app\components\Helper::formatPrice($offerRequest->bet_price),
                'oldBetPrice' => \app\components\Helper::formatPrice($oldPrice),
                'offerId'=>$offerRequest->offer_id,
                'offerTitle'=>$offerRequest->offer->title,
            ]);
        } else {
            $event->text=Yii::t('app','Ich habe {betPrice}€ auf das Angebot [offer:{offerId}]"{offerTitle}"[/offer] geboten.',[
                'betPrice' => $offerRequest->bet_price,
                'offerId'=>$offerRequest->offer_id,
                'offerTitle'=>$offerRequest->offer->title
            ]);
        }

        $event->save();
    }

    public static function addYourBetIsNowNotBest($offerRequest,$bestOfferRequest) {
        $event=new self;
        $event->user_id=$offerRequest->user_id;
        $event->type=static::TYPE_OFFER_MY_REQUEST_BET;
        $event->text=Yii::t('app','Ein anderer Nutzer hat Dich gerade beim Angebot [offer:{offerId}]"{offerTitle}"[/offer] überboten',[
            'offerId'=>$offerRequest->offer_id,
            'offerTitle'=>$offerRequest->offer->title
        ]);
        $event->save();
    }

    public static function addNewOfferBet($offerRequest,$oldPrice) {
        $event=new self;
        $event->user_id=$offerRequest->offer->user_id;
        $event->second_user_id=$offerRequest->user_id;
        $event->type=static::TYPE_OFFER_REQUEST_NEW_BET;

        $params=[
            'offerRequestId' => $offerRequest->id,
            'betPrice' => \app\components\Helper::formatPrice($offerRequest->bet_price),
            'oldBetPrice' => \app\components\Helper::formatPrice($oldPrice),
            'offerId'=>$offerRequest->offer_id,
            'offerTitle'=>$offerRequest->offer->title,
        ];

        if ($oldPrice) {
            $event->text=Yii::t('app','hat sein [myOfferRequest:{offerRequestId}]Gebot[/myOfferRequest] auf das Angebot [offer:{offerId}]"{offerTitle}"[/offer] von {oldBetPrice}€ auf {betPrice}€ verändert.',$params);
        } else {
            $event->text=Yii::t('app','hat ein [myOfferRequest:{offerRequestId}]Gebot in Höhe von {betPrice}€[/myOfferRequest] auf das Angebot [offer:{offerId}]"{offerTitle}"[/offer] abgegeben.',$params);
        }

        $event->save();
    }


    public static function addNewOfferRequest($offerRequest) {
        $event=new self;
        $event->user_id=$offerRequest->offer->user_id;
        $event->second_user_id=$offerRequest->user_id;
        $event->type=static::TYPE_OFFER_REQUEST_NEW;
        $event->text=Yii::t('app','hat Dein Angebot [offer:{offerId}]"{offerTitle}"[/offer] angenommen',[
            'offerId'=>$offerRequest->offer_id,
            'offerTitle'=>$offerRequest->offer->title
        ]);
        $event->save();

        static::addOfferMyRequest($offerRequest);
    }

    public static function addOfferRequestAccepted($offerRequest) {
        $event=new self;
        $event->user_id=$offerRequest->user_id;
        $event->second_user_id=$offerRequest->offer->user_id;
        $event->type=static::TYPE_OFFER_REQUEST_ACCEPTED;
        $event->text=Yii::t('app','hat Dein Interesse an dem Angebot [offer:{offerId}]"{offerTitle}"[/offer] angenommen [offerPay:{offerId}:{offerRequestId}]',[
            'offerId'=>$offerRequest->offer_id,
            'offerTitle'=>$offerRequest->offer->title,
            'offerRequestId'=>$offerRequest->id
        ]);
        $event->save();
    }

    public static function addDocumentsVerification($user) {
        $event=new self;
        $event->user_id=$user->id;
        $event->second_user_id=$user->id;
        $event->type=static::TYPE_DOCUMENTS_VERIFICATION;
        $event->text=Yii::t('app','Du hast Deine Unterlagen zur Verifikation erfolgreich zugesendet. Diese befinden sich in der Prüfung.');
        $event->save();
    }

    public static function addChangeBalanceAdministration($admin, $user, $sum) {
        $event=new self;
        $event->user_id=$user->id;
        $event->type=static::TYPE_CHANGE_BALANCE_ADMINISTRATION;
        $event->text=Yii::t('app','Administrator {user} hat Dir gerade {sum} Jugls überwiesen', [
            'user' => $admin->first_name.' '.$admin->last_name,
            'sum' => $sum
        ]);
        $event->save();
    }

    public static function addChangeBalanceTokenAdministration($admin, $user, $sum) {
        $event=new self;
        $event->user_id=$user->id;
        $event->type=static::TYPE_CHANGE_BALANCE_ADMINISTRATION;
        $event->text=Yii::t('app','Administrator {user} hat Dir gerade {sum} Tokens überwiesen', [
            'user' => $admin->first_name.' '.$admin->last_name,
            'sum' => $sum
        ]);
        $event->save();
    }

    public static function addSystemMessage($user_id,$text) {
        $event=new self;
        $event->user_id=$user_id;
        $event->type=static::TYPE_BROADCAST_MESSAGE;
        $event->text=$text;
        $event->save();

        \app\models\User::updateAllCounters(['new_events'=>1],['id'=>$user_id]);
        \app\components\ChatServer::newEvent($event,$user_id);
    }

    public static function addBroadcastMessage($user_ids,$text) {
        if (empty($user_ids)) return;

        foreach($user_ids as $user_id) {
            $event=new self;
            $event->user_id=$user_id;
            $event->type=static::TYPE_BROADCAST_MESSAGE;
			$event->text=$text;
            $event->save();
        }

        \app\models\User::updateAllCounters(['new_events'=>1],['id'=>$user_ids]);
        \app\components\ChatServer::newEvent($event,$user_ids);
    }

    public static function addVideoIdentUnmatchMessage($trollboxMessage,$user_ids) {
        if (empty($user_ids)) return;

        foreach($user_ids as $user_id) {
            $event=new self;
            $event->user_id=$user_id;
            $event->type=static::TYPE_VIDEO_IDENT_UNMATCH;
            $event->text=Yii::t('app','Leider lagst Du mit Deiner „Echt / Nicht echt“ Entscheidung für den Nutzer {user} falsch. Bitte sei vorsichtiger bei der Bewertung der Videoidentifikationen anderer Nutzer',['user'=>$trollboxMessage->user->name]);
            $event->save();
        }

        \app\models\User::updateAllCounters(['new_events'=>1],['id'=>$user_ids]);
        \app\components\ChatServer::newEvent($event,$user_ids);
    }


    public static function addDealFeedback($deal) {

        if ($deal->className()=='app\models\SearchRequestOffer') {
            $event=new self;

            $event->user_id=$deal->user_id;
            $event->second_user_id=$deal->searchRequest->user_id;
            $event->type = static::TYPE_SEARCH_REQUEST_OFFER_FEEDBACK;
            $event->text = Yii::t('app', 'hat Dein Handel [searchRequest:{searchRequestId}]"{searchRequestTitle}"[/searchRequest] mit {rating} bewertet', [
                'searchRequestId' => $deal->searchRequest->id,
                'searchRequestTitle' => $deal->searchRequest->title,
                'rating' => $deal->userFeedback->rating/20
            ]);

            $event->save();

            $event=new self;

            $event->user_id=$deal->searchRequest->user_id;
            $event->second_user_id=$deal->searchRequest->user_id;
            $event->type = static::TYPE_SEARCH_REQUEST_OFFER_MY_FEEDBACK;
            $event->text = Yii::t('app', 'Ich habe den Handel [searchRequest:{searchRequestId}]"{searchRequestTitle}"[/searchRequest] mit {rating} bewertet', [
                'searchRequestId' => $deal->searchRequest->id,
                'searchRequestTitle' => $deal->searchRequest->title,
                'rating' => $deal->userFeedback->rating/20
            ]);

            $event->save();

        } else {
            $event=new self;

            $event->user_id=$deal->user_id;
            $event->second_user_id=$deal->offer->user_id;
            $event->type = static::TYPE_OFFER_REQUEST_FEEDBACK;
            $event->text = Yii::t('app', 'hat Dein Handel [offer:{offerId}]"{offerTitle}"[/offer] mit {rating} bewertet', [
                'offerId' => $deal->offer->id,
                'offerTitle' => $deal->offer->title,
                'rating' => $deal->userFeedback->rating/20
            ]);

            $event->save();

            $event=new self;

            $event->user_id=$deal->offer->user_id;
            $event->second_user_id=$deal->offer->user_id;
            $event->type = static::TYPE_OFFER_REQUEST_MY_FEEDBACK;
            $event->text = Yii::t('app', 'Ich habe den Handel [offer:{offerId}]"{offerTitle}"[/offer] mit {rating} bewertet', [
                'offerId' => $deal->offer->id,
                'offerTitle' => $deal->offer->title,
                'rating' => $deal->userFeedback->rating/20
            ]);

            $event->save();

        }
    }

    public static function addDealCounterFeedback($deal) {

        if ($deal->className()=='app\models\SearchRequestOffer') {
            $event=new self;

            $event->user_id=$deal->searchRequest->user_id;
            $event->second_user_id=$deal->user_id;
            $event->type = static::TYPE_SEARCH_REQUEST_OFFER_FEEDBACK;
            $event->text = Yii::t('app', 'hat Dein Handel [searchRequest:{searchRequestId}]"{searchRequestTitle}"[/searchRequest] mit {rating} bewertet', [
                'searchRequestId' => $deal->searchRequest->id,
                'searchRequestTitle' => $deal->searchRequest->title,
                'rating' => $deal->counterUserFeedback->rating/20
            ]);

            $event->save();

            $event=new self;

            $event->user_id=$deal->user_id;
            $event->second_user_id=$deal->user_id;
            $event->type = static::TYPE_SEARCH_REQUEST_OFFER_MY_FEEDBACK;
            $event->text = Yii::t('app', 'Ich habe den Handel [searchRequest:{searchRequestId}]"{searchRequestTitle}"[/searchRequest] mit {rating} bewertet', [
                'searchRequestId' => $deal->searchRequest->id,
                'searchRequestTitle' => $deal->searchRequest->title,
                'rating' => $deal->counterUserFeedback->rating/20
            ]);

            $event->save();

        } else {
            $event=new self;

            $event->user_id=$deal->offer->user_id;
            $event->second_user_id=$deal->user_id;
            $event->type = static::TYPE_OFFER_REQUEST_FEEDBACK;
            $event->text = Yii::t('app', 'hat Dein Handel [offer:{offerId}]"{offerTitle}"[/offer] mit {rating} bewertet', [
                'offerId' => $deal->offer->id,
                'offerTitle' => $deal->offer->title,
                'rating' => $deal->counterUserFeedback->rating/20
            ]);

            $event->save();

            $event=new self;

            $event->user_id=$deal->user_id;
            $event->second_user_id=$deal->user_id;
            $event->type = static::TYPE_OFFER_REQUEST_MY_FEEDBACK;
            $event->text = Yii::t('app', 'Ich habe den Handel [offer:{offerId}]"{offerTitle}"[/offer] mit {rating} bewertet', [
                'offerId' => $deal->offer->id,
                'offerTitle' => $deal->offer->title,
                'rating' => $deal->counterUserFeedback->rating/20
            ]);

            $event->save();

        }
    }


    /*
    public static function duplicatePayEvents() {
        $offerRequests=\app\models\OfferRequest::find()->where([
            'pay_status'=>[\app\models\OfferRequest::PAY_STATUS_INVITED,\app\models\OfferRequest::PAY_STATUS_PAYED]
        ])->andWhere('pay_remindered_dt<DATE_SUB(NOW(),INTERVAL 1 DAY)')->all();

        foreach($offerRequests as $offerRequest) {
            switch($offerRequest->pay_status) {
                case \app\models\OfferRequest::PAY_STATUS_INVITED:
                    static::addOfferRequestAccepted($offerRequest);
                    break;
                case \app\models\OfferRequest::PAY_STATUS_PAYED:
                    static::addOfferPayed($offerRequest);
                    break;
            }
            $offerRequest->pay_remindered_dt=(new \app\components\EDateTime)->sqlDateTime();
            $offerRequest->save();
        }
    }
    */

    public function beforeSave($insert) {
        if ($insert) {
            $this->dt=(new EDateTime())->sqlDateTime();
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);

        if ($insert && !in_array($this->type,[static::TYPE_BROADCAST_MESSAGE,static::TYPE_VIDEO_IDENT_UNMATCH])) {
            \app\models\User::updateAllCounters(['new_events'=>1],['id'=>$this->user_id]);
            \app\components\ChatServer::newEvent($this);
        }
    }

}
