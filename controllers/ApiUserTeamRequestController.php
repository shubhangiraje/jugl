<?php

namespace app\controllers;

use app\components\EDateTime;
use app\models\UserModifyLog;
use Yii;
use \app\models\UserTeamRequest;
use \app\models\UserEvent;


class ApiUserTeamRequestController extends \app\components\ApiController {

    public function actionGetType() {
        $toUser=Yii::$app->user->identity;
        $fromUser=\app\models\User::findOne(Yii::$app->request->getQueryParams()['fromUserId']);

        if (!$fromUser) {
            return ['result'=>Yii::t('app','Invalid user specified')];
        }

        $trx=Yii::$app->db->beginTransaction();

        Yii::$app->user->identity->lockForUpdate();

        $userTeamRequest=\app\models\UserTeamRequest::find()->where([
            'user_id'=>$fromUser->id,
            'second_user_id'=>$toUser->id
        ])->one();

        if (!$userTeamRequest) {
            return ['result'=>Yii::t('app','Request doesn\'t exist or expired')];
        }

        return ['result'=>true,'type'=>$userTeamRequest->type];
    }

    public function actionAccept() {
        $toUser=Yii::$app->user->identity;
        $fromUser=\app\models\User::findOne(Yii::$app->request->getBodyParams()['fromUserId']);

        if (!$fromUser) {
            return ['result'=>Yii::t('app','Invalid user specified')];
        }

        $trx=Yii::$app->db->beginTransaction();

        Yii::$app->user->identity->lockForUpdate();

        $userTeamRequest=\app\models\UserTeamRequest::find()->where([
            'user_id'=>$fromUser->id,
            'second_user_id'=>$toUser->id
        ])->one();

        if (!$userTeamRequest) {
            return ['result'=>Yii::t('app','Request doesn\'t exist or expired')];
        }

        $parentIds=[Yii::$app->user->id=>true];
        $parent=Yii::$app->user->identity->parent;

        // check circurality
        while ($parent) {
            if ($parentIds[$parent->id]) {
                throw new \yii\web\ServerErrorHttpException();
            }
            $parentIds[$parent->id]=true;
            $parent=$parent->parent;
        }

        if ($userTeamRequest->type==\app\models\UserTeamRequest::TYPE_REFERRAL_TO_PARENT) {

            if ($userTeamRequest->user->is_stick_to_parent) {
                return ['result'=>Yii::t('app','Diese Anfrage ist leider nicht mehr gültig, da dieser Nutzer sich inzwischen anders überlegt hat und in seinem derzeitigen Team bleiben will.')];
            }

            if ($userTeamRequest->user->stickRequestInProgress()) {
                return ['result'=>Yii::t('app','Diese Anfrage kann erst dann beantwortet werden, wenn dieser Nutzer sich endgültig entscheidet, ob er sein derzeitiges Team tatsächlich verlassen will. Dazu wurde er bereits benachrichtigt.')];
            }

            if(!$userTeamRequest->user->dt_parent_change) {
                $ubmi = \app\models\UserBecomeMemberInvitation::find()
                    ->where(['user_id'=>$userTeamRequest->user->id, 'is_winner'=>1])
                    ->one();

                if($ubmi->dt && ((new EDateTime()) < (new EDateTime($ubmi->dt))->modify("+".\app\models\Setting::get('TEAM_CHANGE_FIRST_TIME')." minute"))) {
                    return ['result'=>Yii::t('app','Dieser Nutzer kann momentan kein Teamwechsel vornehmen. Bitte versuche später noch einmal.')];
                }
            }

            if($userTeamRequest->user->dt_parent_change && ((new EDateTime()) < (new EDateTime($userTeamRequest->user->dt_parent_change))->modify('+1 day'))) {
                return ['result'=>Yii::t('app','Dieser Jugler hat heute bereits ein Teamwechsel vorgenommen.  Ein Teamwechsel ist nur alle 24h möglich. Bitte versuche es zu einem späteren Zeitpunkt')];
            }

            if(!$userTeamRequest->user->getTeamChangeFinishTime()) {
                return ['result'=>Yii::t('app','Bitte beachte, dass der Zeitraum für einen Teamwechsel bereits ausgelaufen ist. Somit ist der erneute Teamwechsel nicht mehr möglich.')];
            }

            if ($userTeamRequest->user->parent_id) {
                $userTeamRequest->user->removeReferralFromParent();
            }

            $oParent=$userTeamRequest->user->parent;
            $userTeamRequest->user->parent_id=$userTeamRequest->second_user_id;
            \app\models\UserModifyLog::saveLogAddReferralToParent($userTeamRequest->user, $userTeamRequest->user->parent, $userTeamRequest->secondUser);
            //$userTeamRequest->user->show_in_become_member=0;
            $userTeamRequest->user->save();
            $userTeamRequest->user->refresh();
            $userTeamRequest->user->addReferralToParent();
            $userTeamRequest->userEvent->text=preg_replace('%\[userTeamRequestAccept.*\[/userTeamRequestDecline\](\s*\[toggleBlockParentTeamRequests\])?%',Yii::t('app','Du hast die Anfrage angenommen'),$userTeamRequest->userEvent->text);
            $userTeamRequest->userEvent->save();
            $userTeamRequest->delete();

            $userTeamRequests=\app\models\UserTeamRequest::find()->where(['user_id'=>$fromUser->id,'type'=>\app\models\UserTeamRequest::TYPE_REFERRAL_TO_PARENT])->with(['userEvent'])->all();
            foreach($userTeamRequests as $utr) {
                $utr->userEvent->text=preg_replace('%\[userTeamRequestAccept.*\[/userTeamRequestDecline\](\s*\[toggleBlockParentTeamRequests\])?%',Yii::t('app','Andere Nutzer hast anfrage angenommen'),$userTeamRequest->userEvent->text);
                $utr->userEvent->save();
                $utr->delete();
            }

            $userTeamRequest->user->recalcHierarchyNetworkStats();

            if($oParent) {
                $oParent->recalcHierarchyNetworkStats();
            }

            UserEvent::addTeamChange($fromUser->id,$toUser->id,Yii::t('app','hat Deine Anfrage zum Teamwechsel angenommen. Du bist jetzt im Team von {name}.',[
                'name'=>$toUser->name
            ]));

            if ($oParent) {
                UserEvent::addTeamChange($oParent->id,$userTeamRequest->user_id,Yii::t('app','hat Dein Team verlassen.'));
            }
        } else {

            if ($userTeamRequest->secondUser->is_stick_to_parent) {
                return ['result'=>Yii::t('app','Du hast der festen Teilnahme an dem Team Deines derzeitigen Teamleaders zugestimmt. Dementsprechend kannst Du nun Dein Team nicht mehr wechseln')];
            }

            if ($userTeamRequest->secondUser->stickRequestInProgress()) {
                return ['result'=>Yii::t('app','Dein Teamleader hat Dir eine Anfrage bezgl. einer festen Teilnahme an seinem Team geschickt. Bitte antworte zuerst auf dieser Anfrage')];
            }

            if(!$userTeamRequest->secondUser->dt_parent_change) {
                $ubmi = \app\models\UserBecomeMemberInvitation::find()
                    ->where(['user_id'=>$userTeamRequest->secondUser->id, 'is_winner'=>1])
                    ->one();

                if($ubmi->dt && ((new EDateTime()) < (new EDateTime($ubmi->dt))->modify("+".\app\models\Setting::get('TEAM_CHANGE_FIRST_TIME')." minute"))) {
                    return ['result'=>Yii::t('app','Der nächste Teamwechsel ist erst nach {minute} Minuten möglich. Gib Deinem Teamleader die Chance, Dir alles zu erklären.', ['minute'=>\app\models\Setting::get('TEAM_CHANGE_FIRST_TIME')])];
                }
            }

            if($userTeamRequest->secondUser->dt_parent_change && ((new EDateTime()) < (new EDateTime($userTeamRequest->secondUser->dt_parent_change))->modify('+1 day'))) {
                return ['result'=>Yii::t('app','Lieber Jugler, ein Teamwechsel ist nur alle 24h möglich.')];
            }

            if(!$userTeamRequest->secondUser->getTeamChangeFinishTime()) {
                return ['result'=>Yii::t('app','Bitte beachte, dass der Zeitraum für einen Teamwechsel bereits ausgelaufen ist. Somit ist der erneute Teamwechsel nicht mehr möglich.')];
            }

            if ($userTeamRequest->secondUser->parent_id) {
                $userTeamRequest->secondUser->removeReferralFromParent();
            }

            $oParent=$userTeamRequest->secondUser->parent;
            $userTeamRequest->secondUser->parent_id=$userTeamRequest->user_id;
            \app\models\UserModifyLog::saveLogAddReferralToParent($userTeamRequest->secondUser, $userTeamRequest->secondUser->parent, $userTeamRequest->user);
            //$userTeamRequest->secondUser->show_in_become_member=0;
            $userTeamRequest->secondUser->save();
            $userTeamRequest->secondUser->refresh();
            $userTeamRequest->secondUser->addReferralToParent();
            $userTeamRequest->userEvent->text=preg_replace('%\[userTeamRequestAccept.*\[/userTeamRequestDecline\](\s*\[toggleBlockParentTeamRequests\])?%',Yii::t('app','Du hast die Anfrage angenommen'),$userTeamRequest->userEvent->text);
            $userTeamRequest->userEvent->save();
            $userTeamRequest->delete();

            $userTeamRequest->secondUser->recalcHierarchyNetworkStats();
            $oParent->recalcHierarchyNetworkStats();

            UserEvent::addTeamChange($fromUser->id,$toUser->id,Yii::t('app','hat Deine Anfrage zum Teamwechsel akzeptiert.'));

            if ($oParent) {
                UserEvent::addTeamChange($oParent->id,$userTeamRequest->second_user_id,Yii::t('app','hat Dein Team verlassen.'));
            }
        }

        $trx->commit();

        $event=$userTeamRequest->userEvent;
        return ['result'=>true,'events'=>[[
            'id'=>$event->id,
            'dt'=>(new EDateTime($event->dt))->js(),
            'type'=>$event->type,
            'text'=>$event->text,
            'user'=>!$event->second_user_id ? \app\models\User::getAdministrationUser()->getShortData():$event->secondUser->getShortData()
        ]]];
    }

    public function actionDecline() {
        $toUser=Yii::$app->user->identity;
        $fromUser=\app\models\User::findOne(Yii::$app->request->getBodyParams()['fromUserId']);

        if (!$fromUser) {
            return ['result'=>Yii::t('app','Invalid user specified')];
        }

        $userTeamRequest=\app\models\UserTeamRequest::find()->where([
            'user_id'=>$fromUser->id,
            'second_user_id'=>$toUser->id
        ])->one();

        if (!$userTeamRequest) {
            return ['result'=>Yii::t('app','Request doesn\'t exist or expired')];
        }

        $trx=Yii::$app->db->beginTransaction();

        if ($userTeamRequest->type==\app\models\UserTeamRequest::TYPE_REFERRAL_TO_PARENT) {
            $userTeamRequest->userEvent->text=preg_replace('%\[userTeamRequestAccept.*\[/userTeamRequestDecline\](\s*\[toggleBlockParentTeamRequests\])?%',Yii::t('app','Du hast die Anfrage abgelehnt'),$userTeamRequest->userEvent->text);
            $userTeamRequest->userEvent->save();
            $userTeamRequest->delete();

            UserEvent::addTeamChange($fromUser->id,$toUser->id,Yii::t('app','hat Deinen Antrag auf Aufnahme ins Team abgelehnt. Möglicherweise sind derzeit keine freien Kapazitäten vorhanden. Wir bitten um Verständnis. [teamChangeUserSearch]Hier findest Du ein anderes Team[/teamChangeUserSearch]'));
        } else {
            $userTeamRequest->userEvent->text=preg_replace('%\[userTeamRequestAccept.*\[/userTeamRequestDecline\](\s*\[toggleBlockParentTeamRequests\])?%',Yii::t('app','Du hast die Anfrage abgelehnt'),$userTeamRequest->userEvent->text);
            $userTeamRequest->userEvent->save();
            $userTeamRequest->delete();

            UserEvent::addTeamChange($fromUser->id,$toUser->id,Yii::t('app','hat Deine Anfrage zum Teamwechsel abgelehnt.'));
        }

        $trx->commit();

        $event=$userTeamRequest->userEvent;
        return ['result'=>true,'events'=>[[
            'id'=>$event->id,
            'dt'=>(new EDateTime($event->dt))->js(),
            'type'=>$event->type,
            'text'=>$event->text,
            'user'=>!$event->second_user_id ? \app\models\User::getAdministrationUser()->getShortData():$event->secondUser->getShortData()
        ]]];
    }

    public function actionSaveReferralToParent() {
        $data=Yii::$app->request->getBodyParams()['userTeamRequest'];

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $userTeamRequest=new UserTeamRequest();
        $userTeamRequest->setScenario('saveReferralToParent');

        $userTeamRequest->load($data,'');
        $userTeamRequest->type=UserTeamRequest::TYPE_REFERRAL_TO_PARENT;
        $userTeamRequest->user_id=Yii::$app->user->id;


        if (Yii::$app->user->identity->getTeamChangeFinishTime()!==false) {
            if ($userTeamRequest->validate()) {
                $userTeamRequest->user_event_id=UserEvent::addTeamChange($userTeamRequest->second_user_id,Yii::$app->user->id,Yii::t('app',
                    'Möchte in Dein Team aufgenommen werden. Seine/Ihre Nachricht: „{text}“. [userTeamRequestAccept:{user_id}][/userTeamRequestAccept][userTeamRequestDecline:{user_id}][/userTeamRequestDecline]',[
                        'text'=>$userTeamRequest->text,
                        'user_id'=>$userTeamRequest->user_id
                    ]));

                UserEvent::addTeamChange(Yii::$app->user->id,Yii::$app->user->id,Yii::t('app',
                    'Du hast eine Teamleaderanfrage an {name} gesendet. Warte nun auf eine Antwort. Teamanfragen über den Messenger sind nicht gestattet.',[
                        'name'=>$userTeamRequest->secondUser->name
                    ]));

                $userTeamRequest->dt=(new EDateTime())->sql();

                $userTeamRequest->save();
            } else {
                $data['$errors']=$userTeamRequest->getFirstErrors();
                $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
            }
        } else {
            $errors[]=Yii::t('app','Time for Teamwechsel is out');
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['userTeamRequest'=>$data];
        }

        $trx->commit();

        return ['result'=>true];
    }

    public function actionSaveParentToReferral() {
        $data=Yii::$app->request->getBodyParams()['userTeamRequest'];

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $userTeamRequest=new UserTeamRequest();
        $userTeamRequest->setScenario('saveParentToReferral');

        $userTeamRequest->load($data,'');
        $userTeamRequest->type=UserTeamRequest::TYPE_PARENT_TO_REFERRAL;
        $userTeamRequest->user_id=Yii::$app->user->id;


        if ($userTeamRequest->user && $userTeamRequest->secondUser->getTeamChangeFinishTime()!==false && !$userTeamRequest->secondUser->block_parent_team_requests) {
            if ($userTeamRequest->validate()) {
                $userTeamRequest->user_event_id=UserEvent::addTeamChange($userTeamRequest->second_user_id,Yii::$app->user->id,Yii::t('app',
                    'Team {name} möchte Dich als neues Mitglied werben. Seine/Ihre Nachricht: „{text}“. [userTeamRequestAccept:{user_id}][/userTeamRequestAccept][userTeamRequestDecline:{user_id}][/userTeamRequestDecline]',[
                        'text'=>$userTeamRequest->text,
                        'user_id'=>$userTeamRequest->user_id,
                        'name'=> $userTeamRequest->user->name
                    ]).($userTeamRequest->secondUser->parent_id ? '[toggleBlockParentTeamRequests]':''));

                UserEvent::addTeamChange(Yii::$app->user->id, Yii::$app->user->id, Yii::t('app',
                        'Du hast {name} in Dein Team eingeladen. Warte nun auf eine Antwort. Weitere Nachrichten über den Messenger zum Teamwechsel ist nicht zulässig. Ebenso weisen wir darauf hin, dass das Abwerben von Mitgliedern zu anderen Websites außerhalb von jugl.net zur Löschung des Profils führt. Dies ist nur als kostenpflichtige Werbung (mit Werbebonus) gestattet.',[
                            'name'=> $userTeamRequest->secondUser->name
                        ]));

                $userTeamRequest->dt=(new EDateTime())->sql();

                $userTeamRequest->save();
            } else {
                $data['$errors']=$userTeamRequest->getFirstErrors();
                $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
            }
        } else {
            $errors[]=Yii::t('app','Der User hat Teamanfragen blockiert oder die Zeit zum Teamwechsel ist abgelaufen');
        }

        if (!in_array(Yii::$app->user->identity->packet,[\app\models\User::PACKET_VIP,\app\models\User::PACKET_VIP_PLUS])) {
            $errors[]=Yii::t('app','Diese Funktion steht nur Premium Mitgliedern zur Verfügung.');
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['userTeamRequest'=>$data];
        }

        $trx->commit();

        return ['result'=>true];
    }

}
