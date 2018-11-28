<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\components\EDateTime;
use app\models\Invitation;
use app\models\RegistrationCode;


class ExtApiRegistrationController extends \app\components\ExtApiController {

    public function actionIndex() {

//        $data=Yii::$app->request->getBodyParams()['code'];
//        $errors=[];
        $data['$allErrors']=&$errors;

//        $model=new \app\models\RegistrationCodeForm();
//        $model->load($data, '');
//
//        if(!$model->validate()) {
//            $data['$errors']=$model->getFirstErrors();
//            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
//        }
//
//        if (!empty($errors)) {
//            return ['error'=>$data];
//        }
//
//        return [
//            'result'=>true
//        ];

        $data['$allErrors'] = [
            Yii::t('app', 'Um unsere App nutzen zu können, registriere Dich auf unserer Website www.jugl.net')
        ];

        return ['error'=>$data];

    }

    /*
    public function actionDataNew() {

        $data=Yii::$app->request->getBodyParams()['user'];
        $errors=[];
        $data['$allErrors']=&$errors;

        $model=new \app\models\RegistrationCodeForm();
        //$model->setScenario('mergedRegistrationForm');
        $model->load($data, '');

        $code = '';
        if(!$model->validate()) {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        } else {
            $code = $model->code;
        }

        if (!empty($errors)) {
            return ['user'=>$data];
        }

        $model=new \app\models\RegistrationDataForm();
        $model->load($data, '');

        $trx=Yii::$app->db->beginTransaction();

        if($model->validate()) {

            if ($code=='' && \app\models\KnownDevice::isDeviceUsed(Yii::$app->request->getHeaders()->get('X-Ext-Api-Auth-Device-Uuid'))) {
                $data['$errors']=$data['$allErrors']=[Yii::t('app',"Über dieses Gerät wurde bereits ein Jugl-Profil erstellt.\nPro User ist nur eine Mitgliedschaft gestattet.\nWenn Du Mitglied werden möchtest und kein Smartphone hast, lass Dir von einem Freund einen Einladungsgutschein für Jugl zusenden.\nDiesen erhält er unter www.jugl.net.")];

                $trx->rollBack();
                return ['user'=>$data];
            }

            $user=new User;
            $user->setAttributes($model->attributes);

            $now=new EDateTime();
            $user->registration_dt=$now->sqlDateTime();
            $user->auth_key=Yii::$app->security->generateRandomString(32);
            $user->access_token=Yii::$app->security->generateRandomString(32);
            $user->encryptPwd();
            $user->status=User::STATUS_REGISTERED;

            if ($code!='') {
                $rc = RegistrationCode::findOne(['code' => $code]);
                $user->parent_id = $rc->user_id;
                $user->registration_code_id = $rc->id;
                $user->packet = \app\models\User::PACKET_VIP;
                $user->status = User::STATUS_LOGINED;
                $user->save();
                $rc->referral_user_id = $user->id;
                $rc->save();
            } else {
                $user->show_in_become_member=true;
                $user->status=\app\models\User::STATUS_REGISTERED;
                $user->registered_by_become_member=1;
                $user->save();
            }

            \app\models\KnownDevice::registerForUser(Yii::$app->request->getHeaders()->get('X-Ext-Api-Auth-Device-Uuid'),$user);

            $userInterest=new \app\models\UserInterest();
            $userInterest->user_id=$user->id;
            $userInterest->level1_interest_id=\app\models\Interest::COMMON_INTEREST_ID;
            $userInterest->save();

            $sModel=new Invitation();
            $sModel->type=Invitation::TYPE_EMAIL;
            $sModel->address=$model->email;
            $sModel->normalizeAddress();
            Invitation::deleteAll(['status'=>Invitation::STATUS_OPEN, 'address'=>$sModel->address]);

            if ($user->parent && $user->parent->getRegistrationsCount()==$user->parent->getRegistrationsLimit()) {
                Yii::$app->mailer->sendEmail($user->parent,'free-registrations-limit-reached',['user'=>$user->parent]);
                Yii::$app->mailer->sendEmail(Yii::$app->params['emailFrom'],'free-registrations-limit-reached-admin',['user'=>$user->parent]);
            }

        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }


        if (!empty($errors)) {
            $trx->rollBack();
            return ['user'=>$data];
        }

        $trx->commit();

        return [
            'result'=>true
        ];


    }

    public function actionData() {

        $data=Yii::$app->request->getBodyParams()['user'];
        $errors=[];
        $data['$allErrors']=&$errors;

        $model=new \app\models\RegistrationCodeForm();
        $model->setScenario('mergedRegistrationForm');
        $model->load($data, '');

        $code = '';
        if(!$model->validate()) {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        } else {
            $code = $model->code;
        }

        if (!empty($errors)) {
            return ['user'=>$data];
        }

        $model=new \app\models\RegistrationDataForm();
        $model->load($data, '');

        $trx=Yii::$app->db->beginTransaction();

        if($model->validate()) {

            if ($code=='' && \app\models\KnownDevice::isDeviceUsed(Yii::$app->request->getHeaders()->get('X-Ext-Api-Auth-Device-Uuid'))) {
                $data['$errors']=$data['$allErrors']=[Yii::t('app',"Über dieses Gerät wurde bereits ein Jugl-Profil erstellt.\nPro User ist nur eine Mitgliedschaft gestattet.\nWenn Du Mitglied werden möchtest und kein Smartphone hast, lass Dir von einem Freund einen Einladungsgutschein für Jugl zusenden.\nDiesen erhält er unter www.jugl.net.")];

                $trx->rollBack();
                return ['user'=>$data];
            }

            $user=new User;
            $user->setAttributes($model->attributes);

            $now=new EDateTime();
            $user->registration_dt=$now->sqlDateTime();
            $user->auth_key=Yii::$app->security->generateRandomString(32);
            $user->access_token=Yii::$app->security->generateRandomString(32);
            $user->encryptPwd();
            $user->status=User::STATUS_REGISTERED;

            if ($code!='') {
                $rc = RegistrationCode::findOne(['code' => $code]);
                $user->parent_id = $rc->user_id;
                $user->registration_code_id = $rc->id;
                $user->packet = \app\models\User::PACKET_VIP;
                $user->status = User::STATUS_LOGINED;
                $user->save();
                $rc->referral_user_id = $user->id;
                $rc->save();
            } else {
                $user->show_in_become_member=true;
                $user->status=\app\models\User::STATUS_REGISTERED;
                $user->registered_by_become_member=1;
                $user->save();
            }

            \app\models\KnownDevice::registerForUser(Yii::$app->request->getHeaders()->get('X-Ext-Api-Auth-Device-Uuid'),$user);

            $userInterest=new \app\models\UserInterest();
            $userInterest->user_id=$user->id;
            $userInterest->level1_interest_id=\app\models\Interest::COMMON_INTEREST_ID;
            $userInterest->save();

            $sModel=new Invitation();
            $sModel->type=Invitation::TYPE_EMAIL;
            $sModel->address=$model->email;
            $sModel->normalizeAddress();
            Invitation::deleteAll(['status'=>Invitation::STATUS_OPEN, 'address'=>$sModel->address]);

            if ($user->parent && $user->parent->getRegistrationsCount()==$user->parent->getRegistrationsLimit()) {
                Yii::$app->mailer->sendEmail($user->parent,'free-registrations-limit-reached',['user'=>$user->parent]);
                Yii::$app->mailer->sendEmail(Yii::$app->params['emailFrom'],'free-registrations-limit-reached-admin',['user'=>$user->parent]);
            }

        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }


        if (!empty($errors)) {
            $trx->rollBack();
            return ['user'=>$data];
        }

        $trx->commit();

        return [
            'result'=>true
        ];


    }
*/

}
