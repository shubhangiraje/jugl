<?php

namespace app\controllers;

use app\models\InviteMe;
use Yii;
use \app\models\Invitation;
use \app\models\InviteByEmailForm;
use \app\components\EDateTime;
use \yii\helpers\Url;


class ApiInvitationController extends \app\components\ApiController {

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

        return $data;
    }

}