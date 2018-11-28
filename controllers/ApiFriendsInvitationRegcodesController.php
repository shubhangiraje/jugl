<?php

namespace app\controllers;

use Yii;
use app\components\EDateTime;
use app\models\BalanceLog;
use app\models\RegistrationCode;
use app\models\RegistrationCodePacket;


class ApiFriendsInvitationRegcodesController extends \app\components\ApiController {

    public function getRegcodes($sort='dt',$statusFilter='',$pageNum=1) {
        $perPage=50;

        $regcodesQuery=RegistrationCode::find()->andWhere(['user_id'=>Yii::$app->user->id])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1)
            ->select(['*']);

        switch ($sort) {
            case 'status':
                $regcodesQuery->addSelect(['IF(referral_user_id,1,0) as status_sort']);
                $regcodesQuery->orderBy('status_sort asc');
                break;
            default:
                $regcodesQuery->orderBy('dt desc');
        }

        switch ($statusFilter) {
            case 'unused':
                $regcodesQuery->andWhere('referral_user_id is null');
                break;
            case 'used':
                $regcodesQuery->andWhere('referral_user_id is not null');
                break;
            default:
        }

        $regcodes=$regcodesQuery->with('referralUser','referralUser.avatarFile')->all();

        $data=[];
        foreach($regcodes as $regcode) {
            $item=$regcode->toArray(['code']);
            $item['dt']=(new EDateTime($regcode->dt))->js();
            if ($regcode->referralUser) {
                $item['referralUser']=$regcode->referralUser->getShortData();
            }
            $data[]=$item;
        }

        return [
            'regcodes'=> [
                'regcodes'=>array_slice($data,0,$perPage),
                'hasMore'=>count($data)>$perPage
            ]
        ];
    }

    public function actionRegcodes($sort,$statusFilter,$pageNum) {
        return $this->getRegcodes($sort,$statusFilter,$pageNum);
    }

    private function getPackets() {
        $packets=RegistrationCodePacket::find()->orderBy('sum')->asArray()->all();

        foreach($packets as $k=>$packet) {
            $packets[$k]['sum']=floatval($packets[$k]['sum']);
        }

        return [
            'packets'=>$packets
        ];
    }

    public function actionBuyRegcodesPacket() {
        $data=Yii::$app->request->getBodyParams()['buyRegcodesPacket'];

        $trx=Yii::$app->db->beginTransaction();

        $packet=RegistrationCodePacket::findOne($data['packet_id']);

        if ($packet) {
            if ($packet->sum<=Yii::$app->user->identity->balance) {
                $regcodesToGenerate=$packet->registration_codes_count;
                $dt=(new EDateTime())->sql();

                while ($regcodesToGenerate>0) {
                    $regcode=new RegistrationCode();
                    $regcode->user_id=Yii::$app->user->id;
                    $regcode->dt=$dt;
                    $regcode->generateCode();

                    try {
                        // do not process validate for eliminate unique check
                        $regcode->save(false);
                    } catch(Exception $e) {
                        continue;
                    }

                    $regcodesToGenerate--;
                }

                Yii::$app->user->identity->addBalanceLogItem(BalanceLog::TYPE_OUT,-$packet->sum,Yii::$app->user->identity,Yii::t('app','Kauf Einladungsgutschein'));

                $data=['message'=>Yii::t('app','Dein Kauf wurde erfolgreich abgeschlossen'),'result'=>true];

            } else {
                $data['message']=Yii::t('app','You have not enough funds');
            }
        } else {
            $data['message']=Yii::t('app','Unknown packet');
        }

        $trx->commit();

        return [
            'buyRegcodesPacket'=>$data
        ];
    }

    public function actionIndex() {
        return array_merge(
            $this->getPackets(),
            $this->getRegcodes()
        );
    }
}