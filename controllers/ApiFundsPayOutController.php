<?php

namespace app\controllers;

use app\models\UserEvent;
use Yii;
use app\models\PayOutPacket;
use app\models\User;
use app\models\ValidationPhotoForm;
use app\models\File;
use app\models\PayOutRequestForm;
use app\models\PayOutPayPalForm;
use app\models\PayOutELVForm;
use app\models\PayOutRequest;


class ApiFundsPayOutController extends \app\components\ApiController {

    private function getPackets() {
        $packets=PayOutPacket::find()->orderBy('jugl_sum')->asArray()->all();

        return [
            'packets'=>$packets
        ];
    }

    private function getValidationData() {
        $user=Yii::$app->user->identity;
        $userData=$user->toArray(['validation_status','validation_type','validation_failure_reason']);

        if ($user->validation_status!=User::VALIDATION_STATUS_SUCCESS) {
            $userData['validationDetails'] = $user->validationDetailsData;

            if ($user->validationPhoto1File) {
                $userData['validationPhoto1File'] = $user->validationPhoto1File->getFrontImageData(['validationSmall']);
                $userData['validation_photo1_file_id'] = $userData['validationPhoto1File']['id'];
            }

            if ($user->validationPhoto2File) {
                $userData['validationPhoto2File'] = $user->validationPhoto2File->getFrontImageData(['validationSmall']);
                $userData['validation_photo2_file_id'] = $userData['validationPhoto2File']['id'];
            }

            if ($user->validationPhoto3File) {
                $userData['validationPhoto3File'] = $user->validationPhoto3File->getFrontImageData(['validationSmall']);
                $userData['validation_photo3_file_id'] = $userData['validationPhoto3File']['id'];
            }

        }

        return [
            'user'=>$userData
        ];
    }

    public function actionSavePayOutRequest() {
        $data=Yii::$app->request->getBodyParams()['payOutRequest'];
        $errors=[];
        $data['$allErrors']=&$errors;

        $form=new PayOutRequestForm();
        $form->load($data,'');

        if ($form->validate()) {
            switch ($form->payment_method) {
                case 'ELV':
                    $methodForm=new PayOutELVForm();
                    break;
                default:
                    $methodForm=new PayOutPayPalForm();
            }

            $methodForm->load($data['details'],'');
            if ($methodForm->validate()) {

                $trx=Yii::$app->db->beginTransaction();

                $packet=PayOutPacket::findOne($form->packet_id);
                $pRequest=new PayoutRequest;
                $pRequest->type=PayOutRequest::TYPE_JUGLS;
                $pRequest->user_id=Yii::$app->user->id;
                $pRequest->jugl_sum=$packet->jugl_sum;
                $pRequest->currency_sum=$packet->currency_sum;
                $pRequest->dt=new \yii\db\Expression('NOW()');
                $pRequest->payment_method=$form->payment_method;
                $pRequest->status=PayOutRequest::STATUS_NEW;
                $pRequest->detailsData=$methodForm->attributes;
                $pRequest->generatePayoutNum();
                $pRequest->save();

                \app\models\UserEvent::addNewPayoutRequest($pRequest);

                $trx->commit();

                return ['payOutRequest'=>[]];
            } else {
                $data['details']['$errors']=$methodForm->getFirstErrors();
                $errors=array_unique(array_merge($errors,array_values($data['details']['$errors'])));
            }
        } else {
            $data['$errors']=$form->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        return [
            'payOutRequest'=>$data
        ];
    }

    public function actionSaveValidation() {
        $data=Yii::$app->request->getBodyParams()['user'];
        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $user=Yii::$app->user->identity;

        switch($data['validation_type']) {
            case 'PHOTOS':
                $form=new ValidationPhotoForm();
                $form->load($data,'');
                if ($form->validate()) {
                    $user->validation_type=$form->validation_type;
                    $user->validation_photo1_file_id=File::getIdFromProtected($form->validation_photo1_file_id);
                    $user->validation_photo2_file_id=File::getIdFromProtected($form->validation_photo2_file_id);
                    $user->validation_photo3_file_id=File::getIdFromProtected($form->validation_photo3_file_id);
                    $user->validation_status=User::VALIDATION_STATUS_AWAITING;
                    $user->save();

                    UserEvent::addDocumentsVerification(Yii::$app->user);

                } else {
                    $data['$errors']=$form->getFirstErrors();
                    $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
                }

                if (!empty($errors)) {
                    $trx->rollBack();
                    return ['user'=>$data];
                }

                $trx->commit();

                return $this->getValidationData();

            default:
        }
    }

    public function actionIndex() {
        return array_merge(
            $this->getValidationData(),
            $this->getPackets()
        );
    }
}