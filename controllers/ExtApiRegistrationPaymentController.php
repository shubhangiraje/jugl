<?php

namespace app\controllers;

use Yii;
use app\models\VIPRequestForm;
use app\models\PayInRequest;
use app\components\EDateTime;
use app\models\User;
use yii\web\ForbiddenHttpException;
use app\models\Setting;

class ExtApiRegistrationPaymentController extends \app\components\ExtApiController {

    public function actionIndex() {
        if (in_array(Yii::$app->user->identity->packet,[\app\models\User::PACKET_VIP_PLUS]) &&
            (Yii::$app->user->identity->vip_lifetime ||
                (new \app\components\EDateTime(Yii::$app->user->identity->vip_active_till))>(new \app\components\EDateTime())->modify('+2 weeks'))) {
            throw new ForbiddenHttpException();
        }

        $VIPPrice=Setting::get(Yii::$app->user->identity->packet=='' ? 'VIP_COST_CURRENCY':'VIP_COST_UPGRADE_CURRENCY');
        $VIPPlusPrice=Setting::get(Yii::$app->user->identity->packet=='' ? 'VIP_PLUS_COST_CURRENCY':'VIP_PLUS_UPGRADE_COST_CURRENCY');

        return array_merge(
            [
                'registeredByCode'=>boolval(Yii::$app->user->identity->registrationCode),
                'VIPPrice'=>$VIPPrice,
                'VIPPlusPrice'=>$VIPPlusPrice,
                'currentPacket'=>Yii::$app->user->identity->packet,
                'VIPPrices'=>\app\models\PayInRequest::getVipPacketPrices(),
                'VIPList'=>\app\models\PayInRequest::getVipPacketList(),
            ]
        );
    }

    public function actionSaveStd() {
        $trx=Yii::$app->db->beginTransaction();

        Yii::$app->user->identity->status=\app\models\User::STATUS_ACTIVE;
        Yii::$app->user->identity->packet=\app\models\User::PACKET_STANDART;
        Yii::$app->user->identity->save();
/*
        Yii::$app->user->identity->addReferralToParent();

        if (Yii::$app->user->identity->parent) {
            Yii::$app->user->identity->parent->distributeReferralPayment(
                Setting::get('STANDARD_COST_JUGL'),
                Yii::$app->user->identity,
                \app\models\BalanceLog::TYPE_IN_REG_REF,
                \app\models\BalanceLog::TYPE_IN_REG_REF_REF,
                \app\models\BalanceLog::TYPE_IN_REG_REF_REF,
                Yii::t('app','Mitglied registriert')
            );
        }
*/
        $trx->commit();

        Yii::$app->user->identity->updateChatContactsAfterRegistration();
        //Yii::$app->user->identity->sendWelcomeMessageFromParentUser();

        return ['result'=>true];
    }

    public function actionSaveVip() {
        if (Yii::$app->user->identity->registrationCode) {
            $trx=Yii::$app->db->beginTransaction();

            Yii::$app->user->identity->status=\app\models\User::STATUS_ACTIVE;
            Yii::$app->user->identity->packet=\app\models\User::PACKET_VIP;
            Yii::$app->user->identity->save();
/*
            Yii::$app->user->identity->addReferralToParent();

            if (Yii::$app->user->identity->parent) {
                Yii::$app->user->identity->parent->distributeReferralPayment(
                    Setting::get('VIP_COST_JUGL'),
                    Yii::$app->user->identity,
                    \app\models\BalanceLog::TYPE_IN_REG_REF,
                    \app\models\BalanceLog::TYPE_IN_REG_REF_REF,
                    \app\models\BalanceLog::TYPE_IN_REG_REF_REF,
                    Yii::t('app','Premium Mitgliedspaket gekauft')
                );
            }
*/
            $trx->commit();

            Yii::$app->user->identity->updateChatContactsAfterRegistration();
            //Yii::$app->user->identity->sendWelcomeMessageFromParentUser();

        } else {
            $data=Yii::$app->request->getBodyParams()['VIP'];
            $errors=[];
            $data['$allErrors']=&$errors;

            $form=new VIPRequestForm();
            $form->load($data,'');

            if ($form->validate()) {
                $trx=Yii::$app->db->beginTransaction();

                $payInRequest=new PayInRequest();
                $payInRequest->type=PayInRequest::TYPE_PACKET;
                $payInRequest->packet_duration_months=$form->packet;
                $payInRequest->user_id=Yii::$app->user->id;
                //$payInRequest->jugl_sum=Setting::get('VIP_COST_JUGL');
                //REVERT_PREMIUM_MONTH
                // {
                $payInRequest->currency_sum=\app\models\PayInRequest::getVipPacketPrices()[$payInRequest->packet_duration_months];
                // } else {
                $payInRequest->jugl_sum=Setting::get('VIP_COST_JUGL');
                $payInRequest->currency_sum=Setting::get(Yii::$app->user->identity->packet=='' ? 'VIP_COST_CURRENCY' :'VIP_COST_UPGRADE_CURRENCY');
                // }
                $payInRequest->dt=(new EDateTime())->sqlDateTime();
                $payInRequest->payment_method=$form->payment_method;
                $payInRequest->return_status=PayInRequest::RETURN_STATUS_AWAITING;
                $payInRequest->confirm_status=PayInRequest::CONFIRM_STATUS_AWAITING;
                $payInRequest->save();

                $trx->commit();

                $data['id']=Yii::$app->security->hashData($payInRequest->id,Yii::$app->params['paymentIDSecret']);
                $pd=$payInRequest->getPaymentMethodData();
                $data['message']=$pd['message'];
            } else {
                $data['$errors']=$form->getFirstErrors();
                $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
            }

            return [
                'VIP'=>$data
            ];
        }
    }

    public function actionSaveVipPlus() {
        $data=Yii::$app->request->getBodyParams()['VIP'];
        $errors=[];
        $data['$allErrors']=&$errors;

        $form=new VIPRequestForm();
        $form->load($data,'');

        if ($form->validate()) {
            $trx=Yii::$app->db->beginTransaction();

            $payInRequest=new PayInRequest();
            $payInRequest->type=PayInRequest::TYPE_PACKET_VIP_PLUS;
            $payInRequest->packet_duration_months=$form->packet;
            $payInRequest->user_id=Yii::$app->user->id;
            //$payInRequest->jugl_sum=Setting::get('VIP_COST_JUGL');
            //REVERT_PREMIUM_MONTH
            // {
            $payInRequest->currency_sum=\app\models\PayInRequest::getVipPacketPrices()[$payInRequest->packet_duration_months];
            // } else {
            $payInRequest->jugl_sum=Setting::get('VIP_COST_JUGL');
            $payInRequest->currency_sum=Setting::get(Yii::$app->user->identity->packet=='' ? 'VIP_PLUS_COST_CURRENCY' : 'VIP_PLUS_UPGRADE_COST_CURRENCY');
            // }
            $payInRequest->dt=(new EDateTime())->sqlDateTime();
            $payInRequest->payment_method=$form->payment_method;
            $payInRequest->return_status=PayInRequest::RETURN_STATUS_AWAITING;
            $payInRequest->confirm_status=PayInRequest::CONFIRM_STATUS_AWAITING;
            $payInRequest->save();

            $trx->commit();

            $data['id']=Yii::$app->security->hashData($payInRequest->id,Yii::$app->params['paymentIDSecret']);
            $pd=$payInRequest->getPaymentMethodData();
            $data['message']=$pd['message'];
        } else {
            $data['$errors']=$form->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        return [
            'VIP'=>$data
        ];
    }

    public function actionPaymentStatus() {
        $id=Yii::$app->request->getBodyParams()['id'];
        $id=Yii::$app->security->validateData($id,Yii::$app->params['paymentIDSecret']);
        if (!$id) {
            return ['result'=>false];
        }

        $payInRequest=\app\models\PayInRequest::findOne($id);

        if (!$payInRequest) {
            return ['result'=>false];
        }

        if ($payInRequest->return_status!=\app\models\PayInRequest::RETURN_STATUS_AWAITING) {
            return ['result'=>false];
        }

        return [
            'payInRequest'=>$payInRequest->toArray(['id','return_status','confirm_status']),
            'result'=>true
        ];
    }

}