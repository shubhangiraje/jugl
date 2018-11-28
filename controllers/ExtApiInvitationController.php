<?php

namespace app\controllers;

use app\models\InviteMe;
use Yii;
use \app\models\Invitation;
use \app\models\InviteByEmailForm;
use \app\components\EDateTime;
use \yii\helpers\Url;


class ExtApiInvitationController extends \app\components\ExtApiController {

    public function actionInviteBySms() {
        $data=Yii::$app->request->getBodyParams();
        $result=Invitation::getInvitationUrls(Invitation::TYPE_SMS,$data['contacts'],$data['text']);

        return [
            'result'=>$result
        ];
    }

    public function actionAddWhatsappInvitation() {
        \app\models\User::updateAllCounters(['stat_invitations_whatsapp'=>1],['id'=>Yii::$app->user->id]);
    }

    public function actionAddSocialInvitation() {
        \app\models\User::updateAllCounters(['stat_invitations_social'=>1],['id'=>Yii::$app->user->id]);
    }

    public function actionBecomeMemberInvite() {
        $data=Yii::$app->request->getBodyParams();
		//sleep as long user has delay
		sleep(Yii::$app->user->identity->delay_invited_member);
        $trx=Yii::$app->db->beginTransaction();

        $user=\app\models\User::findBySql("select * from user where id=:id for update",[':id'=>$data['id']])->one();

        $ubmi=new \app\models\UserBecomeMemberInvitation;
        $ubmi->user_id=$user->id;
        $ubmi->second_user_id=Yii::$app->user->id;
        $ubmi->is_winner=\app\models\UserBecomeMemberInvitation::find()->where(['user_id'=>$user->id])->count()==0;

        $time=explode(" ",microtime(false));

        $date=new EDateTime();
        $date->setTimestamp($time[1]);
        $ubmi->dt=$date->sql();
        $ubmi->ms=floor($time[0]*1000);
        try {
            $ubmi->save();
        } catch (\Exception $e) {
        }

        $winner=\app\models\UserBecomeMemberInvitation::find()->where(['user_id'=>$user->id,'is_winner'=>1])->one();
        if ($winner) {
            $winnerData=[
                'user_id'=>$winner->user_id,
                'userName'=>$winner->secondUser->name,
                'dt'=>(new EDateTime($winner->dt))->js(),
                'ms'=>$winner->getFormattedMs(),
                'count'=>\app\models\UserBecomeMemberInvitation::find()->where(['user_id'=>$user->id])->count()
            ];
        }

        if (!$ubmi->is_winner) {

            $trx->commit();

            return [
                'message'=>Yii::t('app',"Schade!\n\nLeider warst Du zu langsam.\n\n{user} war schneller als Du.\n\nVersuche es einfach nochmal beim nächsten Mitglied, das eingeladen werden möchte.",[
                    'user'=>$user->parent->name
                ]),
                'winner'=>$winnerData
            ];
        }

        if ($user->show_in_become_member) {
            //check for circurality
            $parentIds=[$user->id=>true];
            $parent=Yii::$app->user->identity;
            while ($parent) {
                if ($parentIds[$parent->id]) {
                    $data['$allErrors']=['Circular referrals not allowed'];
                    $trx->rollBack();
                    return $data;
                }
                $parentIds[$parent->id]=true;
                $parent=$parent->parent;
            }

            //$user->show_in_become_member=0;
            $user->parent_id=Yii::$app->user->id;
            $user->save();

            $user->addReferralToParent();
            $user->addRegistrationBonusToParent(true);

            $user->addParentPacketStats();
			$user->addDelayInviteMember();
            $data['message']=Yii::t('app',"Herzlichen Glückwunsch!\n\n{user} ist nun in Deinem Netzwerk!\n\nNimm jetzt gleich Kontakt auf und sorge dafür, dass sich {user} gut aufgehoben fühlt und Dein Team nicht verlässt.",[
                'user'=>$user->name
            ]);
            $data['winner']=$winnerData;

            //$data['refresh']=true;
        }

        $trx->commit();

        $errors=[];
        $data['$allErrors']=&$errors;


/*
        $form=new InviteByEmailForm();
        $form->load($data,'');

        if ($form->validate()) {
            $result=Invitation::Invite(Invitation::TYPE_EMAIL,$form->emailsAsArray,$form->text);
            $data['result']=$result;
        } else {
            $data['$errors']=$form->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }
*/
        return $data;
    }

    public function actionInviteByEmail() {
        $data=Yii::$app->request->getBodyParams();

        $errors=[];
        $data['$allErrors']=&$errors;

        $form=new InviteByEmailForm();
        $form->load($data,'');

        if ($form->validate()) {
            $result=Invitation::Invite(Invitation::TYPE_EMAIL,$form->emailsAsArray,$form->text);
            $data['result']=$result;
        } else {
            $data['$errors']=$form->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        return $data;
    }

    public function actionGetPhonesStatuses() {
        $data=Yii::$app->request->getBodyParam('phones');

        $phones=[];
        $phoneMap=[];

        $invitation=new \app\models\Invitation();
        $invitation->type=\app\models\Invitation::TYPE_SMS;
        foreach($data as $phone) {
            $invitation->address=$phone;
            $invitation->normalizeAddress();
            if ($invitation->address=='') continue;
            $phones[]=$invitation->address;
            $phoneMap[$invitation->address][]=$phone;
        }

        $res=[];

        $invited=(new \yii\db\Query)->select('address')->from('invitation')
            ->where(['user_id'=>Yii::$app->user->id,'type'=>\app\models\Invitation::TYPE_SMS,'address'=>$phones])->createCommand()->queryColumn();
        
        foreach($invited as $phone) {
            foreach($phoneMap[$phone] as $srcPhone) {
                $res[$srcPhone]=Yii::t('app','Bereits eingeladen');
            }
        }

        $registered=(new \yii\db\Query)->select(['normalized_phone'=>'substr(phone,-10)'])->from('user')
            ->having(['normalized_phone'=>$phones])->createCommand()->queryColumn();

        foreach($registered as $phone) {
            foreach($phoneMap[$phone] as $srcPhone) {
                $res[$srcPhone]=Yii::t('app','bereits bei jugl.net registriert');
            }
        }

        return ['phones'=>$res];
    }

    public function actionResendInvitation() {
        $invitationId=Yii::$app->request->getBodyParams()['invitationId'];

        $invitation=Invitation::findOne($invitationId);

        $message=true;
        $data=['message'=>&$message];

        if ($invitation && $invitation->user_id==Yii::$app->user->id) {
            if ($invitation->status!=Invitation::STATUS_REGISTERED) {
                if ($invitation->type==Invitation::TYPE_EMAIL) {
                    $message = $invitation->send();
                    if ($message===true) {
                        $message=Yii::t('app','Einladung wurde erfolgreich erneut versendet.');
                    }
                }
                if ($invitation->type==Invitation::TYPE_SMS) {
                    $invitation->dt=(new EDateTime())->sqlDateTime();
                    $invitation->save();
                }
            } else {
                $message=Yii::t('app','Someone already registered using this invitation');
            }

            $item=$invitation->toArray(['id','address','type','text']);
            $item['status']=$invitation->statusLabel;
            $item['link']=Url::to(['registration/index','invId'=>$invitation->id],true);
            $item['dt']=(new EDateTime($invitation->dt))->js();
            $data['invitation']=$item;

        } else {
            $message=Yii::t('app','Invalid invitation');
        }

        return $data;
    }

    public function getLog($pageNum,$status) {
        sleep(1);
        $perPage=200;

        $invitationsQuery=Invitation::find()->andWhere(['user_id'=>Yii::$app->user->id])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1)
            ->orderBy('dt desc');

        switch ($status) {
            case 'OPEN':
                $invitationsQuery->andWhere('status=:status',[':status'=>Invitation::STATUS_OPEN]);
                break;
            case 'CLICKED':
                $invitationsQuery->andWhere('status=:status',[':status'=>Invitation::STATUS_CLICKED]);
                break;
            case 'REGISTERED':
                $invitationsQuery->andWhere('status=:status',[':status'=>Invitation::STATUS_REGISTERED]);
                break;
            default:
        }

        $invitations=$invitationsQuery->all();

        $data=[];
        foreach($invitations as $invitation) {
            $item=$invitation->toArray(['id','address','type','name']);
            $item['status']=$invitation->statusLabel;
            $item['dt']=(new EDateTime($invitation->dt))->js();
            $data[]=$item;
        }

        return [
            'items'=>array_slice($data,0,$perPage),
            'hasMore'=>count($data)>$perPage
        ];
    }

    function actionLog($pageNum,$status) {
        return $this->getLog($pageNum,$status);
    }

    public function actionDeleteInvitation() {
        return Yii::$app->db->transaction(function($db) {
            $invitationId=Yii::$app->request->getBodyParams()['invitationId'];
            $invitation=Invitation::find()->where(['id'=>$invitationId])->one();
            if (!$invitation) {
                throw new \yii\web\NotFoundHttpException();
            }
            $invitation->delete();
            return ['result'=>true];
        });
    }




}