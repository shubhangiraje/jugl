<?php

namespace app\controllers;

use app\components\EDateTime;
use app\models\PayInRequest;
use Yii;
use app\models\PayInPacket;
use app\models\PayInRequestForm;


class ApiFundsPayInController extends \app\components\ApiController {

    private function getPackets() {
        $packets=PayInPacket::find()->orderBy('jugl_sum')->asArray()->all();

        return [
            'packets'=>$packets
        ];
    }

    public function actionSavePayInRequest() {
        $data=Yii::$app->request->getBodyParams()['payInRequest'];
        $errors=[];
        $data['$allErrors']=&$errors;

        $form=new PayInRequestForm();
        $form->load($data,'');

        if ($form->validate()) {
            $packet=PayInPacket::findOne($form->packet_id);

            $trx=Yii::$app->db->beginTransaction();

            $payInRequest=new PayInRequest();
            $payInRequest->type=PayInRequest::TYPE_PAY_IN;
            $payInRequest->user_id=Yii::$app->user->id;
            $payInRequest->jugl_sum=$packet->jugl_sum;
            $payInRequest->currency_sum=$packet->currency_sum;
            $payInRequest->dt=(new EDateTime())->sqlDateTime();
            $payInRequest->payment_method=$form->payment_method;
            $payInRequest->return_status=PayInRequest::RETURN_STATUS_AWAITING;
            $payInRequest->confirm_status=PayInRequest::CONFIRM_STATUS_AWAITING;
            $payInRequest->save();

            $trx->commit();

            $data['id']=$payInRequest->id;
        } else {
            $data['$errors']=$form->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        return [
            'payInRequest'=>$data
        ];
    }

    public function actionIndex() {
        return array_merge(
            [
            ],
            $this->getPackets()
        );
    }
}