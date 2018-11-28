<?php

namespace app\controllers;

use app\components\EDateTime;
use app\models\IcoPayoutForm;
use app\models\TokenDeposit;
use Yii;
use yii\filters\AccessControl;
use app\models\LoginForm;
use app\models\Country;
use app\components\MyIcoRedirectFilter;


class IcoSiteController extends \app\components\IcoController
{
    use \app\components\ControllerDeadlockHandler;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login','captcha','login-facebook','link-facebook-account-to-jugl'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'myRedirect' => [
                'class' => MyIcoRedirectFilter::className(),
            ]
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'view' => [
                'class' => 'yii\web\ViewAction',
                'viewPrefix' =>'/app-view'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    private function redirectAfterLogin() {
        return $this->redirect(['ico-site/dashboard']);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirectAfterLogin();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirectAfterLogin();
        } else {
            $model->password=null;
            $model->verifyCode=null;
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

	public function actionLoginFacebook()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirectAfterLogin();
        }
		

		//Facebook API methods
		$social = Yii::$app->getModule('social');
		$fb = $social->getFb(); // gets facebook object based on module settings
    
		try {
			$helper = $fb->getRedirectLoginHelper();
			$accessToken = $helper->getAccessToken();
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			return $this->redirect('/login');
		}
		if (isset($accessToken)) { // you got a valid facebook authorization token
			$response = $fb->get('/me?fields=id,first_name,last_name,email,gender,birthday,location', $accessToken);
			$model = new LoginForm();		
			$model->setScenario('facebook_login');
			$facebook_user=$response->getDecodedBody();	
			$model->facebook_id=$facebook_user['id'];
			
			//Check if User exists in Database
			if ($model->loginWithFacebookId()) {
				return $this->redirectAfterLogin();
			} else {
				//Else, try to Register new Member
				Yii::$app->session['facebook_infos']=$facebook_user;				
				if($facebook_user){					
					$modelReg=new \app\models\RegistrationDataForm();
					if($facebook_user!=null){
						$modelReg->first_name		=	$facebook_user['first_name']? $facebook_user['first_name'] : null;
						$modelReg->last_name		=	$facebook_user['last_name']? $facebook_user['last_name'] : null;
						$modelReg->sex				=	$facebook_user['gender'] ? strtoupper($facebook_user['gender'][0]) : null;
						$birthdate					=	explode('/',$facebook_user['birthday']);
						$modelReg->birth_day		=	$birthdate[1]? ltrim($birthdate[1], '0') : null;
						$modelReg->birth_month		=	$birthdate[0]? $birthdate[0] : null;
						$modelReg->birth_year		=	$birthdate[2]? $birthdate[2] : null;
						$modelReg->city				=	$facebook_user['location']? $facebook_user['location']['name'] : null;
						$modelReg->email			=	$facebook_user['email']? $facebook_user['email'] : null;
						$modelReg->email_repeat		=	$facebook_user['email']? $facebook_user['email'] : null;		
						$modelReg->facebook_id		=	$facebook_user['id'];
					}
					$modelReg->password=Yii::$app->security->generateRandomString(30);
					$modelReg->password_repeat=$modelReg->password;
					$modelReg->country_id=Country::getId();

					$modelReg->setScenario('becomeMemberNew');
						if ($modelReg->validate()) {
							$trx=Yii::$app->db->beginTransaction();

							$user=new \app\models\User;
							$user->setAttributes($modelReg->attributes);
							$now=new \app\components\EDateTime();
							$user->registration_dt=$now->sqlDateTime();
							$user->show_in_become_member=true;
								if($modelReg->facebook_id){
								$user->facebook_id=$modelReg->facebook_id;
								}
							if($modelReg->birth_day && $modelReg->birth_month && $modelReg->birth_year){
								$user->setBirthDay($modelReg->birth_day);
								$user->setBirthMonth($modelReg->birth_month);
								$user->setBirthYear($modelReg->birth_year);	
							}
							$user->auth_key=Yii::$app->security->generateRandomString(32);
							$user->access_token=Yii::$app->security->generateRandomString(32);
							$user->encryptPwd();
							//$user->status=\app\models\User::STATUS_EMAIL_VALIDATION;
							$user->status=\app\models\User::STATUS_REGISTERED;
							$user->registered_by_become_member=1;
							$user->save();
							//$user->sendEmailValidation();
								Yii::$app->db->createCommand("
									INSERT INTO user_interest (user_id, level1_interest_id, type) 
										SELECT :user_id, id, type FROM interest WHERE parent_id is null", [
									':user_id'=>$user->id
								])->execute();
							$trx->commit();
							//After Creating new user, Login in automatically
							$modelLoginFacebook = new LoginForm();		
							$modelLoginFacebook->setScenario('facebook_login');
							$modelLoginFacebook->facebook_id=$facebook_user['id'];
							if ($modelLoginFacebook->loginWithFacebookId()) {
									return $this->redirectAfterLogin();
							}
							
						}else{
							Yii::$app->session['facebook_infos']=$facebook_user;
							return $this->redirect('/site/become-member-facebook-linking');
						}
					}
			 }
				//return $this->redirect('/become-member');	
		}
			
		
		return $this->redirect('/login');       
    }
	
    public function actionDashboard() {
        $data=[
            'balance'=>Yii::$app->user->identity->balance,
            'balance_buyed'=>Yii::$app->user->identity->balance_buyed,
            'balance_earned'=>Yii::$app->user->identity->balance_earned,
            'balance_token_deposit_percent'=>Yii::$app->user->identity->balance_token_deposit_percent,
            'earned_this_year'=>Yii::$app->user->identity->earnedThisYear,
            'earned_today'=>Yii::$app->user->identity->earnedToday,
            'earned_yesterday'=>Yii::$app->user->identity->earnedYesterday,
            'earned_this_month'=>Yii::$app->user->identity->earnedThisMonth,
            'earned_total'=>Yii::$app->user->identity->earnedTotal,
            'balance_token'=>Yii::$app->user->identity->balance_token,
            'balance_token_earned'=>Yii::$app->user->identity->balance_token_earned,
            'balance_token_buyed'=>Yii::$app->user->identity->balance_token_buyed,
            'tokenDeposits'=>TokenDeposit::getLogTokenDeposit()
        ];

        return $this->render('dashboard', $data);
    }

    public function actionTokenPercentPayout() {
        $icoPayoutForm = new IcoPayoutForm();
        $icoPayoutForm->payment_method = IcoPayoutForm::PAYMENT_METHOD_ELV;

        if (Yii::$app->user->identity->balance_token_deposit_percent/\app\models\Setting::get("EXCHANGE_JUGLS_PER_EURO")<50 ||
            Yii::$app->user->identity->validation_status!=\app\models\User::VALIDATION_STATUS_SUCCESS) {
            return $this->redirect(['dashboard']);
        }

        if ($icoPayoutForm->load(Yii::$app->request->post()) && $icoPayoutForm->validate()) {
            $trx=Yii::$app->db->beginTransaction();
            Yii::$app->user->identity->lockForUpdate();

            $pRequest=new \app\models\PayOutRequest();
            $pRequest->type=\app\models\PayOutRequest::TYPE_TOKEN_DEPOSIT_PERCENT;
            $pRequest->user_id=Yii::$app->user->id;
            $pRequest->jugl_sum=Yii::$app->user->identity->balance_token_deposit_percent;
            $pRequest->currency_sum=Yii::$app->user->identity->balance_token_deposit_percent/\app\models\Setting::get("EXCHANGE_JUGLS_PER_EURO");
            $pRequest->dt=new \yii\db\Expression('NOW()');
            $pRequest->payment_method=$icoPayoutForm->payment_method;
            $pRequest->status=\app\models\PayOutRequest::STATUS_NEW;
            $pRequest->detailsData=$icoPayoutForm->attributes;
            unset($pRequest->detailsData['payment_method']);
            $pRequest->generatePayoutNum();
            $pRequest->save();

            \app\models\UserEvent::addNewPayoutRequest($pRequest);

            $trx->commit();
            return $this->render('token-percent-payout-success');
        }

        return $this->render('token-percent-payout-data',['icoPayoutForm'=>$icoPayoutForm]);
    }

    public function actionTokenDepositPayout($id,$type) {
        $model=\app\models\TokenDeposit::findOne($id);

        if (!$model || $model->user_id!=Yii::$app->user->id) {
            return $this->redirect(['dashboard']);
        }

        if ($model->status==\app\models\TokenDeposit::STATUS_COMPLETED) {
            return $this->render('token-deposit-payout-success',['model'=>$model]);
        }

        if ($type=='EUR') {
            if (Yii::$app->user->identity->validation_status!=\app\models\User::VALIDATION_STATUS_SUCCESS) {
                return $this->redirect(['dashboard']);
            }

            $icoPayoutForm = new IcoPayoutForm();
            $icoPayoutForm->payment_method = IcoPayoutForm::PAYMENT_METHOD_ELV;

            if ($icoPayoutForm->load(Yii::$app->request->post()) && $icoPayoutForm->validate()) {
                $trx=Yii::$app->db->beginTransaction();
                $model->lockForUpdate();
                if ($model->status!=\app\models\TokenDeposit::STATUS_COMPLETED) {
                    $pRequest=new \app\models\PayOutRequest();
                    $pRequest->type=\app\models\PayOutRequest::TYPE_TOKEN_DEPOSIT;
                    $pRequest->user_id=Yii::$app->user->id;
                    $pRequest->jugl_sum=0;
                    $pRequest->currency_sum=$model->sum*\app\models\Setting::TOKEN_DEPOSIT_TOKEN_TO_EURO_EXCHANGE_RATE;
                    $pRequest->dt=new \yii\db\Expression('NOW()');
                    $pRequest->payment_method=$icoPayoutForm->payment_method;
                    $pRequest->status=\app\models\PayOutRequest::STATUS_NEW;
                    $pRequest->detailsData=$icoPayoutForm->attributes;
                    unset($pRequest->detailsData['payment_method']);
                    $pRequest->generatePayoutNum();
                    $pRequest->save();
                    $model->payout_pay_out_request_id=$pRequest->id;
                    $model->status=\app\models\TokenDeposit::STATUS_COMPLETED;
                    $model->save();

                    \app\models\UserEvent::addNewPayoutRequest($pRequest);
                }
                $trx->commit();
                return $this->render('token-deposit-payout-success',['model'=>$model]);
            }

            return $this->render('token-deposit-payout-data',['model'=>$model, 'icoPayoutForm'=>$icoPayoutForm]);
        } else {
            $trx=Yii::$app->db->beginTransaction();
            $model->lockForUpdate();
            if ($model->status!=\app\models\TokenDeposit::STATUS_COMPLETED) {
                $model->payoutInJugls();
            }

            $trx->commit();
            return $this->render('token-deposit-payout-success',['model'=>$model]);
        }
    }
}
