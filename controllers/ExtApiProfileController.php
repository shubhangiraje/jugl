<?php

namespace app\controllers;

use app\components\EDateTime;
use app\models\Invitation;
use app\models\User;
use Yii;
use app\models\File;
use app\components\Helper;



class ExtApiProfileController extends \app\components\ExtApiController {

public $already_sms_verified=false;

    private function getProfileData() {
        $user=Yii::$app->user->identity;

        $data=array_merge($user->toArray($user->scenarios()['profile']),
            $user->getAttributes(['birthDay','birthMonth','birthYear', 'first_name', 'last_name','show_in_become_member','status','is_moderator','allow_country_change'])
        );

        if ($user->avatarFile) {
            $data['avatarFile']=$user->avatarFile->getFrontImageData(['avatarMobile']);
            $data['avatar_file_id']=$data['avatarFile']['id'];
        } else {
            $data['avatarFile']['thumbs'] = [
                'avatarMobile' => \app\components\Thumb::createUrl('/static/images/account/default_avatar.png','avatarMobile',true)
            ];
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
        
        $photos = [];
        foreach ($user->userPhotos as $photo) {
            $photos[]=$photo->file->getFrontImageData(['imageBig']);
        }

        $data['photos']=$photos;

        $data['validation_phone']=$user->validation_phone;
        $data['validation_phone_status']=$user->validation_phone_status;

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
            return [
                'user'=>$data,
                'result' => false
            ];
        }

        $trx->commit();

        /*return $this->getProfileData();*/

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

//        if ($user->status!=\app\models\User::STATUS_LOGINED) {
//            return [
//                'result'=>false,
//                'user'=>['$allErrors'=>['Your profile is already full']]
//            ];
//        }


        if($data['saveProfileFillup2'] && $user->status == \app\models\User::STATUS_ACTIVE &&
            in_array($user->packet,[\app\models\User::PACKET_VIP,\app\models\User::PACKET_VIP_PLUS,\app\models\User::PACKET_STANDART]) ||
            ($user->status == \app\models\User::STATUS_LOGINED && in_array($user->packet,[\app\models\User::PACKET_VIP,\app\models\User::PACKET_VIP_PLUS]))) {
            $user->setScenario('profileFillup2');
        } else {
            $user->setScenario('profileFillup');
        }

//        $user->setScenario('profileFillup');

        $user->load($data,'');

        // process image
        $user->avatar_file_id=File::getIdFromProtected($data['avatar_file_id']);

        if ($user->validate()) {
            $oldStatus=$user->status;
            $user->status=\app\models\User::STATUS_ACTIVE;
            $now=new EDateTime();
            $user->dt_status_active=$now->sqlDateTime();

            $user->save();

            if ($oldStatus==\app\models\User::STATUS_ACTIVE) {
                $user->addReferralToParent();
                Yii::$app->response->on(\yii\web\Response::EVENT_AFTER_SEND, [$user, 'updateStatus']);

                if (Yii::$app->user->identity->parent) {
                    Yii::$app->user->identity->parent->packetCanBeSelected();
                }

                $invitation = Invitation::find()->where(['referral_user_id' => Yii::$app->user->id])->one();
                if ($invitation) {
                    \app\models\UserEvent::addRegisteredByInvitation($invitation->user, $invitation->referralUser);
                }
            }

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


    public function actionDelete() {
        if(Yii::$app->user->identity->validation_phone_status!=User::VALIDATION_PHONE_STATUS_VALIDATED && Yii::$app->user->identity->parent_registration_bonus==0) {
            Yii::$app->user->identity->delete(true);
            return ['result'=>true];
        } else {
            return ['result'=>false];
        }
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
            return [
                'user'=>$data,
                'result' => false
            ];
        }

        $trx->commit();

        return [
            'result' => true
        ];
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
	$temp_sms_verified=false;
	
        $data=Yii::$app->request->getBodyParams();
        $errors=[];
        $data['$allErrors']=&$errors;

        $user=Yii::$app->user->identity;
        $user->setScenario('validationCode');
        $user->load($data,'');

        if ($user->validate() && $this->already_sms_verified===false && $temp_sms_verified===false) {
            if($user->validation_code == $user->validation_code_form) {
                $user->validation_phone_status = User::VALIDATION_PHONE_STATUS_VALIDATED;
                $user->save();
                $user->addRegistrationBonusToParent();
				$this->already_sms_verified=true;
				$temp_sms_verified=true;
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

        return [
            'result'=>true
        ];


    }

}