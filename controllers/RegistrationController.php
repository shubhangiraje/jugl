<?php

namespace app\controllers;

use app\components\Language;
use app\models\Interest;
use app\models\Invitation;
use Yii;
use yii\web\Cookie;
use yii\web\Session;
use yii\web\Controller;
use app\models\RegistrationCodeForm;
use app\models\RegistrationDataForm;
use app\models\RegistrationActivationCodeForm;
use app\models\RegistrationCode;
use app\models\User;
use app\models\Country;
use app\components\EDateTime;


class RegistrationController extends Controller {

    public function beforeAction($action) {
        // for activation code link
        if (isset($_GET['SID'])) {
            Yii::$app->session->setId($_GET['SID']);
            if (isset($_GET['code'])) {
                $_REQUEST['RegistrationActivationCodeForm']['code']=$_GET['code'];
            }
        }
        $res=parent::beforeAction($action);

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        Language::setLanguage();

        return $res;
    }

    private function initSessionObject() {
        $_SESSION['registration']=[
            'RegistrationCodeForm'=>new RegistrationCodeForm,
            'RegistrationDataForm'=>new RegistrationDataForm,
            'RegistrationActivationCodeForm'=>new RegistrationActivationCodeForm,
            'activePage'=>0
        ];
    }

    private function &getSessionObject() {
        //$session = new Session;
        //$session->open();
        if (!isset($_SESSION['registration'])) {
            $this->initSessionObject();
        }

        return $_SESSION['registration'];
    }

    /*
     * function validates all previous wizard steps and
     * current step if $validateCurrentStep is true
     */
    private function validateSteps($validateCurrentStep) {
        $data=$this->getSessionObject();

        $res1=$data['RegistrationCodeForm']->validate();
        $res2=$data['RegistrationDataForm']->validate();
        if (!$res1 || !$res2) {
            return false;
        }

        return true;
/*
        if (!$data['RegistrationDataForm']->validate()) {
            return false;//return $this->action->id=='data' ? false:$this->redirect(['data']);
        }


        $steps=[
            'index',
            'data',
            'activation-code'
        ];

        $step=array_search($this->action->id,$steps);
        $validateStep=$step-($validateCurrentStep ? 0:1);

        $data=$this->getSessionObject();

        if ($step>$data['activePage']) {
            $data['activePage']=0;
            $this->redirect(['index']);
        }

        if ($validateStep>=0) {
            if (!$data['RegistrationCodeForm']->validate()) {
                return $this->action->id=='index' ? false:$this->redirect(['index']);
            }

            if ($data['RegistrationCodeForm']->refId) {
                if (!$data['RegistrationCodeForm']->code) {
                    $parentUser=\app\models\User::findOne($data['RegistrationCodeForm']->refId);

                    if ($parentUser && $parentUser->getRegistrationsCount()>=$parentUser->getRegistrationsLimit()) {
                        $data['RegistrationCodeForm']->addError('code', Yii::t('app', 'Link nicht mehr aktiv. Bitte wende dich an deinen einladenden Kontakt'));
                        return $this->action->id=='index' ? false:$this->redirect(['index']);
                    }
                }

                if (!User::findOne($data['RegistrationCodeForm']->refId)) {
                    $this->initSessionObject();
                    return $this->redirect('index');
                }
            }
        }

        $data['RegistrationDataForm']->setScenario(User::SCENARIO_DEFAULT);
        if ($validateStep>=1) {
            if (!$data['RegistrationDataForm']->validate()) {
                return false;//return $this->action->id=='data' ? false:$this->redirect(['data']);
            }
        }
*/
        /*
        $model=$data['RegistrationActivationCodeForm'];
        $model->setScenario($model->tries>=3 && $step==2 ? 'withCaptcha':User::SCENARIO_DEFAULT);
        if ($validateStep>=2) {
            if (!$model->validate()) {
                $model->tries++;
                $model->setScenario($model->tries>=3 && $step==2 ? 'withCaptcha':User::SCENARIO_DEFAULT);
                return $this->action->id=='activation-code' ? false:$this->redirect(['activation-code']);
            }
        }
        */

        //return true;
    }

    public function actionIndexApp() {
        $session = new \yii\web\Session();
        $session->open();

        $session['registrationFromApp']=true;

        return $this->redirect(array_merge(['registration/index'],$_GET));
    }
	
	public function actionRegisterWithFacebook() {
        
		$social = Yii::$app->getModule('social');
		$fb = $social->getFb(); // gets facebook object based on module settings
		$rc=$this->getSessionObject()['RegistrationCodeForm'];
		try {
			$helper = $fb->getRedirectLoginHelper();
			$accessToken = $helper->getAccessToken();
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			if($rc['refId']!=null){ return $this->redirect('/registration/index?refId='.$rc['refId']); };
			if($rc['invId']!=null){ return $this->redirect('/registration/index?invId='.$rc['invId']); };				
		}
		if (isset($accessToken)) { // you got a valid facebook authorization token
			$response = $fb->get('/me?fields=id,first_name,last_name,email,gender,birthday,location', $accessToken);
			$facebook_user= $response->getDecodedBody();	
			Yii::$app->session['facebook_infos']=$facebook_user;
			if($rc['refId']!=null){ return $this->redirect('/registration/index?refId='.$rc['refId']); };
			if($rc['invId']!=null){ return $this->redirect('/registration/index?invId='.$rc['invId']); };
		} 
		else{
			if($rc['refId']!=null){ return $this->redirect('/registration/index?refId='.$rc['refId']); };
			if($rc['invId']!=null){ return $this->redirect('/registration/index?invId='.$rc['invId']); };
		}
	
    }

    public function actionIndex() {
        $dataCode=$this->getSessionObject()['RegistrationCodeForm'];
		$facebookData = Yii::$app->session['facebook_infos'];
		$submitted=false;

        if (isset($_REQUEST['refId'])) {

            if (!Yii::$app->request->cookies->has('refId')) {
                Yii::$app->response->cookies->add(new Cookie([
                    'name' => 'refId',
                    'value' => $_REQUEST['refId'],
                    'expire' => time() + 900
                ]));
                $redId = $_REQUEST['refId'];
            } else {
                $redId = Yii::$app->request->cookies->get('refId')->value;
            }

            $dataCode->setAttributes([
                'refId'=>$redId,
                'invId'=>null,
                'code'=>null
            ],false);
        }

        if (isset($_REQUEST['invId'])) {

            if (!Yii::$app->request->cookies->has('invId')) {
                Yii::$app->response->cookies->add(new Cookie([
                    'name' => 'invId',
                    'value' => $_REQUEST['invId'],
                    'expire' => time() + 900
                ]));
                $invId = $_REQUEST['refId'];
            } else {
                $invId = Yii::$app->request->cookies->get('invId')->value;
            }

            $dataCode->setAttributes([
                'invId'=>$invId,
                'code'=>null
            ],false);
        }

        $data=$this->getSessionObject()['RegistrationCodeForm'];
        $data->load($_REQUEST);

        $data->setScenario(\yii\db\ActiveRecord::SCENARIO_DEFAULT);

        if ($dataCode->code!='') {
            $dataCode->refId=null;
            $dataCode->invId=null;
        }
		
        $dataData=$this->getSessionObject()['RegistrationDataForm'];
		$submitted=$dataData->load($_REQUEST);
		if($facebookData!=null){
			$dataData->first_name		=	$facebookData['first_name']? $facebookData['first_name'] : null;
			$dataData->last_name		=	$facebookData['last_name']? $facebookData['last_name'] : null;
			$dataData->sex				=	$facebookData['gender']? strtoupper($facebookData['gender'][0]) : null;
			$birthdate					=	explode('/',$facebookData['birthday']);
			$dataData->birth_day		=	$birthdate[1]? ltrim($birthdate[1], '0') : null;
			$dataData->birth_month		=	$birthdate[0]? $birthdate[0] : null;
			$dataData->birth_year		=	$birthdate[2]? $birthdate[2] : null;
			$dataData->city				=	$facebookData['location']? $facebookData['location']['name'] : null;
			$dataData->email			=	$facebookData['email']? $facebookData['email'] : null;
			$dataData->email_repeat		=	$facebookData['email']? $facebookData['email'] : null;
			//$model->country_id		=	$facebookData['country']? $facebookData['first_name'] : null;
			$dataData->facebook_id		=	$facebookData['id'];
			$dataData->password			= 	Yii::$app->security->generateRandomString(30).'b1';
			$dataData->password_repeat	= 	$dataData->password;
			$submitted=true;
		}

		$dataData->country_id=Country::getId();

        if ($submitted) {
            $res=$this->validateSteps($submitted);

            if (!is_bool($res)) return $res;
        }

        /*
        if ($res && $submitted) {
            $this->getSessionObject()['activePage']=1;

            return $this->redirect(['data']);
        }
        */

        if ($res && $submitted) {

            $trx=Yii::$app->db->beginTransaction();

            $dm=$this->getSessionObject()['RegistrationDataForm'];
            // saving user to database
            $user=new User;

            $user->setAttributes($dm->attributes);

            $now=new EDateTime();
            $user->registration_dt=$now->sqlDateTime();
			if($dataData->facebook_id){
				$user->facebook_id=$dataData->facebook_id;
			}
			if($dataData->birth_day && $dataData->birth_month && $dataData->birth_year){
				$user->setBirthDay($dataData->birth_day);
				$user->setBirthMonth($dataData->birth_month);
				$user->setBirthYear($dataData->birth_year);	
			}
            $user->auth_key=Yii::$app->security->generateRandomString(32);
            $user->access_token=Yii::$app->security->generateRandomString(32);
            $user->encryptPwd();

            $rc=$this->getSessionObject()['RegistrationCodeForm'];
            $user->status=User::STATUS_REGISTERED;
            if ($rc->refId) {
                $user->parent_id=$rc->refId;
                $user->save();

                $invitation=Invitation::findOne($rc->invId);
                if ($invitation) {
                    $invitation->status=Invitation::STATUS_REGISTERED;
                    $invitation->referral_user_id=$user->id;
                    $invitation->save();
                }

                Invitation::deleteAll(['status'=>Invitation::STATUS_OPEN, 'address'=>$invitation->address]);
				$this->afterRegistrationReferral($user);
            } else {
                if ($rc->code) {
                    $rc=RegistrationCode::findOne(['code'=>$rc->code]);
                    $user->parent_id=$rc->user_id;
                    $user->registration_code_id=$rc->id;
                    $user->packet=\app\models\User::PACKET_VIP;
                    $user->vip_active_till=(new \app\components\EDateTime())->modify('+1 month')->sqlDateTime();
                    $user->status=User::STATUS_ACTIVE;

                    // REVERT_PREMIUM_MONTH
                    $user->vip_active_till='2035-01-01 00:00:00';
                    $user->vip_lifetime=1;

                    $user->save();
                    $rc->referral_user_id=$user->id;
                    $rc->save();
					$this->afterRegistrationReferral($user);
                } else {
                    $user->show_in_become_member=true;
                    //$user->status=\app\models\User::STATUS_REGISTERED;
                    $user->registered_by_become_member=1;
                    $user->save();
                }
            }

            $user->sendEmailValidation();

//            $userInterest=new \app\models\UserInterest();
//            $userInterest->user_id=$user->id;
//            $userInterest->level1_interest_id=\app\models\Interest::COMMON_INTEREST_ID;
//            $userInterest->type=\app\models\Interest::TYPE_OFFER;
//            $userInterest->save();
//
//            $userInterest=new \app\models\UserInterest();
//            $userInterest->user_id=$user->id;
//            $userInterest->level1_interest_id=\app\models\Interest::COMMON_INTEREST_ID2;
//            $userInterest->type=\app\models\Interest::TYPE_SEARCH_REQUEST;
//            $userInterest->save();


            Yii::$app->db->createCommand("
                INSERT INTO user_interest (user_id, level1_interest_id, type) 
                    SELECT :user_id, id, type FROM interest WHERE parent_id is null", [
                ':user_id'=>$user->id
            ])->execute();


            $sModel=new Invitation();
            $sModel->type=Invitation::TYPE_EMAIL;
            $sModel->address=$dm->email;
            $sModel->normalizeAddress();
            Invitation::deleteAll(['status'=>Invitation::STATUS_OPEN, 'address'=>$sModel->address]);

            $this->initSessionObject();

            $trx->commit();

            //Yii::$app->user->login($user);
/*
            if ($user->parent && $user->parent->getRegistrationsCount()==$user->parent->getRegistrationsLimit()) {
                Yii::$app->mailer->sendEmail($user->parent,'free-registrations-limit-reached',['user'=>$user->parent]);
                Yii::$app->mailer->sendEmail(Yii::$app->params['emailFrom'],'free-registrations-limit-reached-admin',['user'=>$user->parent]);
            }
*/
            Yii::$app->session->setFlash('validation-popup',true);
            Yii::$app->user->login($user);
			//Yii::$app->session->setFlash('validation-popup',true);
			return $this->redirect(['site/my']);
        }

		$dataData->country_id=Country::getId();

        return $this->render('index',['model'=>$data,'modelData'=>$dataData]);
    }

    public function actionData() {
        $data=$this->getSessionObject()['RegistrationDataForm'];

        $submitted=$data->load($_REQUEST);
        $res=$this->validateSteps($submitted);

        if (!is_bool($res)) return $res;

        if ($res && $submitted) {

            $trx=Yii::$app->db->beginTransaction();

            $dm=$this->getSessionObject()['RegistrationDataForm'];
            // saving user to database
            $user=new User;

            $user->setAttributes($dm->attributes);

            $now=new EDateTime();
            $user->registration_dt=$now->sqlDateTime();

            $user->auth_key=Yii::$app->security->generateRandomString(32);
            $user->access_token=Yii::$app->security->generateRandomString(32);
            $user->encryptPwd();

            $rc=$this->getSessionObject()['RegistrationCodeForm'];
            $user->status=User::STATUS_REGISTERED;
            if ($rc->refId) {
                $user->parent_id=$rc->refId;
                $user->save();

                $invitation=Invitation::findOne($rc->invId);
                if ($invitation) {
                    $invitation->status=Invitation::STATUS_REGISTERED;
                    $invitation->referral_user_id=$user->id;
                    $invitation->save();
                }

                Invitation::deleteAll(['status'=>Invitation::STATUS_OPEN, 'address'=>$invitation->address]);
            } else {
                if ($rc->code) {
                    $rc=RegistrationCode::findOne(['code'=>$rc->code]);
                    $user->parent_id=$rc->user_id;
                    $user->registration_code_id=$rc->id;
                    $user->packet=\app\models\User::PACKET_VIP;
                    $user->status=User::STATUS_ACTIVE;
                    $user->save();
                    $rc->referral_user_id=$user->id;
                    $rc->save();
                } else {
                    $user->show_in_become_member=true;
                    $user->status=\app\models\User::STATUS_REGISTERED;
                    $user->registered_by_become_member=1;
                    $user->save();
                }
            }

//            $userInterest=new \app\models\UserInterest();
//            $userInterest->user_id=$user->id;
//            $userInterest->level1_interest_id=\app\models\Interest::COMMON_INTEREST_ID;
//            $userInterest->type=\app\models\Interest::TYPE_OFFER;
//            $userInterest->save();
//
//            $userInterest=new \app\models\UserInterest();
//            $userInterest->user_id=$user->id;
//            $userInterest->level1_interest_id=\app\models\Interest::COMMON_INTEREST_ID2;
//            $userInterest->type=\app\models\Interest::TYPE_SEARCH_REQUEST;
//            $userInterest->save();


            Yii::$app->db->createCommand("
                INSERT INTO user_interest (user_id, level1_interest_id, type) 
                    SELECT :user_id, id, type FROM interest WHERE parent_id is null", [
                ':user_id'=>$user->id
            ])->execute();


            $sModel=new Invitation();
            $sModel->type=Invitation::TYPE_EMAIL;
            $sModel->address=$dm->email;
            $sModel->normalizeAddress();
            Invitation::deleteAll(['status'=>Invitation::STATUS_OPEN, 'address'=>$sModel->address]);

            $this->initSessionObject();

            $trx->commit();
/*
            if ($user->parent && $user->parent->getRegistrationsCount()==$user->parent->getRegistrationsLimit()) {
                Yii::$app->mailer->sendEmail($user->parent,'free-registrations-limit-reached',['user'=>$user->parent]);
                Yii::$app->mailer->sendEmail(Yii::$app->params['emailFrom'],'free-registrations-limit-reached-admin',['user'=>$user->parent]);
            }
*/
            if ($user->status!=User::STATUS_REGISTERED) {
                Yii::$app->user->login($user);

                return $this->redirect(['site/my','#'=>'']);
            } else {
                Yii::$app->session->setFlash('validation-popup',true);
                return $this->redirect('/');
            }
        }

        return $this->render('data',['model'=>$data]);
    }

/*
    public function actionActivationCode() {
        $data=$this->getSessionObject()['RegistrationActivationCodeForm'];

        $submitted=$data->load($_REQUEST);

        if ($data->request_help) {
            $data->request_help=0;
            $this->getSessionObject()['RegistrationDataForm']->saveHelpRequest(2);
            Yii::$app->session->setFlash('help_requested',true);
            return $this->render('activation-code',['model'=>$data]);
        }

        $res=$this->validateSteps($submitted);

        if (!is_bool($res)) return $res;

        if ($res && $submitted) {
            $trx=Yii::$app->db->beginTransaction();

            $dm=$this->getSessionObject()['RegistrationDataForm'];
            // saving user to database
            $user=new User;

            $user->setAttributes($dm->attributes);

            $now=new EDateTime();
            $user->registration_dt=$now->sqlDateTime();

            $date=new EDateTime();
            $date->setDate($dm->birth_year,$dm->birth_month,$dm->birth_day);
            $user->birthday=$date->sqlDate();
            $user->phone=$dm->phone;

            $user->auth_key=Yii::$app->security->generateRandomString(32);
            $user->access_token=Yii::$app->security->generateRandomString(32);
            $user->encryptPwd();

            $rc=$this->getSessionObject()['RegistrationCodeForm'];
            $user->status=User::STATUS_AWAITING_MEMBERSHIP_PAYMENT;
            if ($rc->refId) {
                $user->parent_id=$rc->refId;
                $user->save();

                $invitation=Invitation::findOne($rc->invId);
                if ($invitation) {
                    $invitation->status=Invitation::STATUS_REGISTERED;
                    $invitation->referral_user_id=$user->id;
                    $invitation->save();
                    \app\models\UserEvent::addRegisteredByInvitation($invitation->user,$invitation->referralUser);
                }

                Invitation::deleteAll(['status'=>Invitation::STATUS_OPEN, 'address'=>$invitation->address]);
            } else {
                $rc=RegistrationCode::findOne(['code'=>$rc->code]);
                $user->parent_id=$rc->user_id;
                $user->registration_code_id=$rc->id;
                $user->save();
                $rc->referral_user_id=$user->id;
                $rc->save();
            }

            \app\models\InviteMe::deleteAll(['email'=>$user->email]);
            \app\models\InviteMe::deleteAll(['phone'=>$user->phone]);

            $userInterest=new \app\models\UserInterest();
            $userInterest->user_id=$user->id;
            $userInterest->level1_interest_id=\app\models\Interest::COMMON_INTEREST_ID;
            $userInterest->save();

            $sModel=new Invitation();
            $sModel->type=Invitation::TYPE_SMS;
            $sModel->address=$dm->phone;
            $sModel->normalizeAddress();
            Invitation::deleteAll(['status'=>Invitation::STATUS_OPEN, 'address'=>$sModel->address]);

            $sModel=new Invitation();
            $sModel->type=Invitation::TYPE_EMAIL;
            $sModel->address=$dm->email;
            $sModel->normalizeAddress();
            Invitation::deleteAll(['status'=>Invitation::STATUS_OPEN, 'address'=>$sModel->address]);

            $this->initSessionObject();

            $trx->commit();

            Yii::$app->user->login($user);

            if ($user->parent->getRegistrationsCount()==$user->parent->getRegistrationsLimit()) {
                Yii::$app->mailer->sendEmail($user->parent,'free-registrations-limit-reached',['user'=>$user->parent]);
            }

            $this->redirect(['site/my','#'=>'/welcome']);
        }

        return $this->render('activation-code',['model'=>$data]);
    }
*/

    public function actionGetReferral() {
        Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;

        $data=$this->getSessionObject()['RegistrationCodeForm'];
        $username = '';

        if($data->refId) {
            $user = User::findOne($data->refId);
            $username = $user->name;
        }

        if($data->invId) {
            $invitation = Invitation::find()->where(['id'=>$data->invId])->with('user')->one();
            $username = $invitation->user->name;
        }

        if($_REQUEST['code']) {
            $registration_code = RegistrationCode::find()->where(['code'=>$_REQUEST['code']])->andWhere('referral_user_id is null')->with('user')->one();
            if ($registration_code) {
                $username = $registration_code->user->name;
            }
        }

        return [
            'username'=>$username,
            'data'=>$data
        ];
    }
	
	private function afterRegistrationReferral($user){
        $user->addReferralToParent();
        Yii::$app->response->on(\yii\web\Response::EVENT_AFTER_SEND, [$user, 'updateStatus']);

        if (Yii::$app->user->identity->parent) {
            Yii::$app->user->identity->parent->packetCanBeSelected();
        }

        $invitation = \app\models\Invitation::find()->where(['referral_user_id'=>$user->id])->one();
        if ($invitation) {
            \app\models\UserEvent::addRegisteredByInvitation($invitation->user, $invitation->referralUser);
        }
	}


}
