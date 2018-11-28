<?php

namespace app\controllers;

use app\models\Setting;
use app\models\BalanceLog;
use app\models\Invitation;
use app\components\EDateTime;
use Yii;
use yii\web\NotFoundHttpException;
use yii\db\Query;


class ApiFriendsInvitationInvitationsController extends \app\components\ApiController {

    private function getStats() {
        $data=[];

        $data['sent']=intval(Yii::$app->db->createCommand(
            'select count(*) from invitation where user_id=:user_id',
            [':user_id'=>Yii::$app->user->id])->queryScalar());

        $data['clicked']=intval(Yii::$app->db->createCommand(
            'select count(*) from invitation where user_id=:user_id and status=:status',
            [':user_id'=>Yii::$app->user->id,':status'=>Invitation::STATUS_CLICKED])->queryScalar());

        $data['registered']=intval(Yii::$app->db->createCommand(
            'select count(*) from invitation where user_id=:user_id and status=:status',
            [':user_id'=>Yii::$app->user->id,':status'=>Invitation::STATUS_REGISTERED])->queryScalar());

        $data['inRegRef']=floatval(Yii::$app->db->createCommand(
            'select sum(`sum`) from balance_log where user_id=:user_id and type=:type',
            [':user_id'=>Yii::$app->user->id,':type'=>BalanceLog::TYPE_IN_REG_REF])->queryScalar());

        $data['inReg']=$data['inRegRef']+floatval(Yii::$app->db->createCommand(
            'select sum(`sum`) from balance_log where user_id=:user_id and type=:type',
            [':user_id'=>Yii::$app->user->id,':type'=>BalanceLog::TYPE_IN_REG_REF_REF])->queryScalar());

        return [
            'stats'=>$data
        ];
    }

    private function getSettings() {
        $regCostJugl=Setting::get('VIP_COST_JUGL');
        if (in_array(Yii::$app->user->identity->packet,[\app\models\User::PACKET_VIP,\app\models\User::PACKET_VIP_PLUS])) {
            $pdParentsPercent=Setting::get('VIP_PROFIT_DISTRIBUTION_PARENTS_PERCENT');
            $pdJuglPercent=Setting::get('VIP_PROFIT_DISTRIBUTION_JUGL_PERCENT');
        } else {
            $pdParentsPercent=Setting::get('PROFIT_DISTRIBUTION_PARENTS_PERCENT');
            $pdJuglPercent=Setting::get('PROFIT_DISTRIBUTION_JUGL_PERCENT');
        }

        return [
            'settings'=>[
                'directEarn'=>$regCostJugl*(100-$pdJuglPercent-$pdParentsPercent)/100
            ]
        ];
    }

    public function getInvitations($sort='dt',$statusFilter='',$pageNum=1) {
        $perPage=50;

        $invitationsQuery=Invitation::find()->andWhere(['user_id'=>Yii::$app->user->id])->with(['referralUser'])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        switch ($sort) {
            case 'status':
                $invitationsQuery->orderBy('status asc');
                break;
            default:
                $invitationsQuery->orderBy('dt desc');
        }

        switch ($statusFilter) {
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
            $item=$invitation->toArray(['id','address','name','type']);
            $item['status']=$invitation->statusLabel;
            $item['dt']=(new EDateTime($invitation->dt))->js();
            if($invitation->referralUser) {
                $item['user'] = $invitation->referralUser->toArray(['id','first_name','last_name','nick_name']);
            }
            $data[]=$item;

        }

        return [
            'invitations'=> [
                'invitations'=>array_slice($data,0,$perPage),
                'hasMore'=>count($data)>$perPage,
                'test' => true
            ]
        ];
    }

    public function actionResendInvitation() {
        $invitationId=Yii::$app->request->getBodyParams()['invitationId'];

        $invitation=Invitation::findOne($invitationId);

        $message=true;

        if ($invitation && $invitation->user_id==Yii::$app->user->id) {
            if ($invitation->status!=Invitation::STATUS_REGISTERED) {
                $message=$invitation->send();
            } else {
                $message=Yii::t('app','Someone already registered using this invitation');
            }

            $item=$invitation->toArray(['id','address','type']);
            $item['status']=$invitation->statusLabel;
            $item['dt']=(new EDateTime($invitation->dt))->js();
        } else {
            $message=Yii::t('app','Invalid invitation');
        }

        if ($message===true) {
            $message=Yii::t('app','Einladung wurde erfolgreich erneut versendet.');
        }

        return ['resendInvitation'=>['message'=>$message],'invitation'=>$item];
    }

    public function actionInvitations($sort,$statusFilter,$pageNum) {
        return $this->getInvitations($sort,$statusFilter,$pageNum);
    }

    public function actionIndex() {
        return array_merge(
            $this->getStats(),
            $this->getSettings(),
            $this->getInvitations()
        );
    }

    public function actionDeleteInvitation() {
        return Yii::$app->db->transaction(function($db) {
            $invitationId=Yii::$app->request->getBodyParams()['invitationId'];
            $invitation=Invitation::find()->where(['id'=>$invitationId])->one();
                if (!$invitation) {
                    throw new NotFoundHttpException();
                }
            $invitation->delete();
            return ['result'=>true];
        });
    }



}