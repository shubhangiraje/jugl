<?php

namespace app\controllers;

use app\components\ChatServer;
use app\components\EDateTime;
use app\models\RegistrationDataForm;
use app\models\User;
use Yii;
use app\models\File;
use app\components\Helper;


class ApiProfileController extends \app\components\ApiController {

    private function getProfileData() {
        $user=Yii::$app->user->identity;

        $data=array_merge($user->toArray($user->scenarios()['profile']),
            $user->getAttributes(['birthDay','birthMonth','birthYear', 'first_name', 'last_name','is_moderator','allow_country_change'])
        );

        if ($user->avatarFile) {
            $data['avatarFile']=$user->avatarFile->getFrontImageData(['avatarBig']);
            $data['avatar_file_id']=$data['avatarFile']['id'];
        }

        $bankDatas=[];
        foreach($user->userBankDatas as $bankData) {
            $bankDatas[]=$bankData->toArray(['bic','iban','owner']);
        }

        if (empty($bankDatas)) {
            $bankDatas[]=[
                'bic'=>'',
                'iban'=>'',
                'owner'=>''
            ];
        }

        $data['bankDatas']=$bankDatas;

        $data['validation_phone']=$user->validation_phone;
        $data['validation_phone_status']=$user->validation_phone_status;

        $photos = [];
        foreach ($user->userPhotos as $photo) {
            $photos[]=$photo->file->getFrontImageData(['photoSmall']);
        }

        $data['photos']=$photos;


        return [
            'user'=>$data,
            'maritalStatuses'=>$user->getArrMaritalStatusList(),
            'sexes'=>$user->getSexList(),
            'birthDayList'=>Helper::assocToRecords(Helper::getDaysList()),
            'birthMonthList'=>Helper::assocToRecords(Helper::getMonthsList()),
            'birthYearList'=>Helper::assocToRecords(Helper::getYearsList(-12,-100)),
            'countries'=>$user->getCountries()
        ];
    }

    public function actionIndex() {
        return $this->getProfileData();
    }

    public function actionNeedHelp() {
        $model=new RegistrationDataForm();
        $model->setAttributes(Yii::$app->user->identity->getAttributes([
            'first_name','last_name','company_name','birthday','email','phone','sex'
        ]));

        $model->saveHelpRequest(3,Yii::$app->user->id);
    }

    public function actionSaveProfile() {
        $data=Yii::$app->request->getBodyParams()['user'];
        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $user=Yii::$app->user->identity;

        $user->setScenario('profile');

        if(!$user->is_moderator && !$user->allow_country_change) {
            unset($data['country_id']);
        }

        $user->load($data,'');

        // process image
        $user->avatar_file_id=File::getIdFromProtected($data['avatar_file_id']);

        if ($user->validate()) {

            \app\models\UserModifyLog::saveLog($user);

            $user->save();

            // save bank data
            $bankDataIdx=0;
            foreach($data['bankDatas'] as $bd) {
                if (trim(implode('',array_values($bd)))=='') continue;

                $model=count($user->userBankDatas)<=$bankDataIdx ? (new \app\models\UserBankData):$user->userBankDatas[$bankDataIdx];
                $model->sort_order=$bankDataIdx;
                $model->user_id=$user->id;
                $model->load($bd,'');
                $model->save();
                $bankDataIdx++;
            }

            for(;$bankDataIdx<count($user->userBankDatas);$bankDataIdx++) {
                $user->userBankDatas[$bankDataIdx]->delete();
            }

            //save photos
            $user->relinkFilesWithSortOrder($data['photos'],'files','userPhotos');


        } else {
            $data['$errors']=$user->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['user'=>$data];
        }

        $trx->commit();

        return $this->getProfileData();
    }

	public function actionLaterProfileFillup() {
        $trx=Yii::$app->db->beginTransaction();
        $user=Yii::$app->user->identity;
		$now=new EDateTime();
		$user->later_profile_fillup_date=$now->sqlDateTime();
        $user->save(); 
        $trx->commit();
        
		return [
            'result' => true
        ];
    }

    public function actionSaveProfileFillup() {
        $data=Yii::$app->request->getBodyParams()['user'];
        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $user=Yii::$app->user->identity;

        /*if ($user->status!=\app\models\User::STATUS_LOGINED) {
            return [
                'result'=>false,
                'user'=>['$allErrors'=>['Your profile is already full']]
            ];
        }*/

//        if($user->status == \app\models\User::STATUS_LOGINED && $user->packet == \app\models\User::PACKET_VIP) {
//            $user->setScenario('profileFillup2');
//        } else {
//            $user->setScenario('profileFillup');
//        }

        $user->setScenario('profileFillup2');
        $user->load($data,'');

        // process image
        $user->avatar_file_id=File::getIdFromProtected($data['avatar_file_id']);

        if ($user->validate()) {

            $oldStatus=$user->status;
            $user->status=\app\models\User::STATUS_ACTIVE;
            $now=new EDateTime();
            $user->dt_status_active=$now->sqlDateTime();

            $user->save();

            //if ($oldStatus==\app\models\User::STATUS_LOGINED) {

                $user->addReferralToParent();
                Yii::$app->response->on(\yii\web\Response::EVENT_AFTER_SEND, [$user, 'updateStatus']);

                if (Yii::$app->user->identity->parent) {
                    Yii::$app->user->identity->parent->packetCanBeSelected();
                }

                $invitation = \app\models\Invitation::find()->where(['referral_user_id' => Yii::$app->user->id])->one();
                if ($invitation) {
                    \app\models\UserEvent::addRegisteredByInvitation($invitation->user, $invitation->referralUser);
                }
            //}
            /*
                        if ($this->parent) {
                            $this->parent->distributeReferralPayment(
                                Setting::get($this->packet==static::PACKET_VIP ? 'VIP_COST_JUGL':'STANDARD_COST_JUGL'),
                                $this,
                                \app\models\BalanceLog::TYPE_IN_REG_REF,
                                \app\models\BalanceLog::TYPE_IN_REG_REF_REF,
                                \app\models\BalanceLog::TYPE_IN_REG_REF_REF,
                                $this->packet==static::PACKET_VIP ? Yii::t('app','Premium Mitgliedspaket gekauft'):Yii::t('app','Mitglied registriert')
                            );
                        }
            */
            
        } else {
            $data['$errors']=$user->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return [
                'user'=>$data,
                'result' => false
            ];
        }

        $trx->commit();

        /*return $this->getProfileData();*/
        \app\components\ChatServer::statusUpdate([$user->id]);

        return [
            'result' => true
        ];
    }

    public function actionDelete() {
        if(Yii::$app->user->identity->validation_phone_status!=User::VALIDATION_PHONE_STATUS_VALIDATED && Yii::$app->user->identity->parent_registration_bonus==0) {
            Yii::$app->user->identity->delete(true);
            return ['result' => true];
        }
        return ['result' => false];
    }

    public function actionSaveDesktop() {
        $value=Yii::$app->request->getBodyParams()['value'];
        Yii::$app->db->createCommand("UPDATE user SET registration_from_desktop=:registration_from_desktop, status=:status WHERE id=:id", [
            ':id'=>Yii::$app->user->identity->id,
            ':registration_from_desktop'=>1,//$value,
            ':status'=>User::STATUS_ACTIVE
        ])->execute();

        \app\components\ChatServer::statusUpdate(Yii::$app->user->identity->id);

        return [
            'result'=>true,
        ];
    }

    public function actionAutoSaveProfile() {

        $data=Yii::$app->request->getBodyParams()['user'];
        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $user=Yii::$app->user->identity;

        $user->setScenario('autoSave');
        $user->load($data,'');

        // process image
        $user->avatar_file_id=File::getIdFromProtected($data['avatar_file_id']);

        if ($user->validate()) {
            \app\models\UserModifyLog::saveLog($user);

            $user->save();

            // save bank data
            $bankDataIdx=0;
            foreach($data['bankDatas'] as $bd) {
                if (trim(implode('',array_values($bd)))=='') continue;

                $model=count($user->userBankDatas)<=$bankDataIdx ? (new \app\models\UserBankData):$user->userBankDatas[$bankDataIdx];
                $model->sort_order=$bankDataIdx;
                $model->user_id=$user->id;
                $model->load($bd,'');
                $model->save();
                $bankDataIdx++;
            }

            for(;$bankDataIdx<count($user->userBankDatas);$bankDataIdx++) {
                $user->userBankDatas[$bankDataIdx]->delete();
            }

            //save photos
            $user->relinkFilesWithSortOrder($data['photos'],'files','userPhotos');

        } else {
            $data['$errors']=$user->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['user'=>$data];
        }

        $trx->commit();

        return $this->getProfileData();

    }


    public function actionSendValidationPhone() {

        $data=Yii::$app->request->getBodyParams();
        $errors=[];
        $data['$allErrors']=&$errors;

        $user=Yii::$app->user->identity;
        $user->setScenario('validationPhone');
        $user->load($data,'');

        if ($user->validate()) {
            $user->generateCode();
            $text = Yii::t('app', 'Dein Bestätigungscode bei jugl.net lautet: {code}', ['code'=>$user->validation_code]);
            $res = Yii::$app->sms->send($user->validation_phone, $text);
            if ($res === true) {
                $user->validation_phone_status = User::VALIDATION_PHONE_STATUS_SEND_CODE;
                $user->save();
                Yii::$app->db->createCommand("insert into phone_sms_count(phone,count) values (:phone,1) on duplicate key update count=count+1",[
                    ':phone'=>Yii::$app->sms->normalizePhone($user->validation_phone)
                ])->execute();
            } else {
                $errors = [Yii::t('app', 'SMS sending error!')];
            }
        } else {
            $data['$errors']=$user->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            return ['validation'=>$data];
        }

        $data['validation_phone_status']=$user->validation_phone_status;
        return [
            'result'=>true,
            'validation'=>$data
        ];

    }


    public function actionSendValidationCode() {

        $data=Yii::$app->request->getBodyParams();
        $errors=[];
        $data['$allErrors']=&$errors;

        $user=Yii::$app->user->identity;
        $user->setScenario('validationCode');
        $user->load($data,'');

        if ($user->validate()) {
            if($user->validation_code == $user->validation_code_form) {
                $user->validation_phone_status = User::VALIDATION_PHONE_STATUS_VALIDATED;
                $user->save();
                $user->addRegistrationBonusToParent();
            } else {
                $errors = [Yii::t('app', 'Der eingegebene Code ist falsch. Bitte versuche es noch einmal.')];
            }
        } else {
            $data['$errors']=$user->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            return ['validation'=>$data];
        }

        $data['validation_code_send']=true;
        return [
            'validation'=>$data
        ];


    }



}