<?php

namespace app\controllers;

use app\components\Language;
use app\models\Invitation;
use app\models\UserInfoView;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\components\Thumb;
use app\models\LoginForm;
use app\models\Country;
use app\components\MyRedirectFilter;
use yii\web\NotFoundHttpException;
use kartik\social\Module;
use app\components\ExtService;


class SiteController extends Controller
{
    use \app\components\ControllerDeadlockHandler;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['set-language','activation','cron5','cron60','cron1440','cron00','log','test-sms','index','view','login','error','captcha','restore-password-step1','restore-password-step2','generate-thumbnail','become-member','become-member-facebook-linking','app-membership-payment','deadlock1','deadlock2','sign-out-mail','login-facebook','link-facebook-account-to-jugl', 'cashface', 'redirect-to-juglcoin'],
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
                'class' => MyRedirectFilter::className(),
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

    /*
    public function actionError() {
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            // action has been invoked not from error handler, but by direct route, so we display '404 Not Found'
            $exception = new HttpException(404, Yii::t('yii', 'Page not found.'));
        }

        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }

        if ($exception instanceof Exception) {
            $name = $exception->getName();
        } else {
            $name = Yii::t('yii', 'Error');
        }
        if ($code) {
            $name .= " (#$code)";
        }


        if ($exception instanceof UserException) {
            $message = $exception->getMessage();
        } else {
            $message = Yii::t('yii', 'An internal server error occurred.');
        }

        if ($exception instanceof  \yii\db\Exception && $exception->errorInfo[0]='HY000') {
            http_response_code(505);
            echo "deadlock";
            die;
        }

        if (Yii::$app->getRequest()->getIsAjax()) {
            return "$name: $message";
        } else {
            return $this->render('error', [
                'name' => $name,
                'message' => $message,
                'exception' => $exception,
            ]);
        }

    }
*/
    public function actionIndex() {
        return $this->render('index', [
            'model'=>new \app\models\LoginForm(),
            'modelCode'=>new \app\models\RegistrationCodeForm()
        ]);
    }

    public function actionRedirectToJuglcoin() {
        Yii::$app->session->set('redirect_to_juglcoin', true);
        return $this->redirect(['/']);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    private function redirectAfterLogin() {
        $url=Yii::$app->session['forwardAfterLogin'];

        if ($url) {
            Yii::$app->session['forwardAfterLogin']=null;
            return $this->redirect($url);
        } else {
            if (Yii::$app->session->has('redirect_to_juglcoin') && Yii::$app->session->get('redirect_to_juglcoin')) {
                Yii::$app->session->remove('redirect_to_juglcoin');
                return $this->redirect(Yii::$app->params['buyTokenSite'].Yii::$app->params['buyTokenUrl'].'?PHPSESSID='.session_id());
            }
            return $this->redirect(['site/my']);
        }
    }

    public function actionLogin()
    {
        if ($_GET[session_name()]) {
            Yii::$app->session->open();
            Yii::$app->session->close();
            session_id($_GET[session_name()]);
            Yii::$app->session->open();
            return $this->redirect('?');
        }

        if ($_GET['forwardAfterLogin']) {
            Yii::$app->session['forwardAfterLogin'] = $_GET['forwardAfterLogin'];
        }

        if (!\Yii::$app->user->isGuest) {
            return $this->redirectAfterLogin();
        }

        $model = new LoginForm();
		//$model->setScenario('normal');
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
        if ($_GET['forwardAfterLogin']) {
            Yii::$app->session['forwardAfterLogin'] = $_GET['forwardAfterLogin'];
        }

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
	
	
	
	
	
    public function actionMy() {
        $this->layout='my';

        return $this->render('my', [
            'infoViewedViews'=>\app\models\UserInfoView::getViews()
        ]);
    }

    public function actionRestorePasswordStep1() {
        $model=new \app\models\RestorePasswordStep1Form;

        $res=$model->sendRestoreCode($_POST);

        if ($res) {
            Yii::$app->session->setFlash('success', $model->email);
            return $this->refresh();
        }

        return $this->render('restore-password-step1', [
            'model' => $model,
        ]);
    }

    public function actionRestorePasswordStep2() {
        $model=new \app\models\RestorePasswordStep2Form;

        $res=$model->restorePassword();

        if ($res) {
            Yii::$app->session->setFlash('success',true);
            return $this->refresh();
        }

        return $this->render('restore-password-step2', [
            'model' => $model,
        ]);
    }

    public function actionGenerateThumbnail($url) {
        Thumb::generate($url);
    }

    public function actionCron5() {

        try {
            \app\models\Offer::updateStatusScheduled();
        } catch (\Exception $e) {}

        try {
            \app\models\SearchRequest::updateStatusScheduled();
        } catch (\Exception $e) {}

        try {
            \app\models\CfrDistributionUser::process();
        } catch (\Exception $e) {}

        try {
            \app\models\TokenDeposit::doPayouts();
        } catch (\Exception $e) {}

        //\app\models\UserEvent::duplicatePayEvents();
        
		//Ausgeschaltet aufgrund Performance Problemen, Testweise
		//\app\models\User::sendInvitationPushNotifications();
		
        /*Erinnerungsemail für Einladung von Freunden und Bekannten*/
		//\app\models\User::sendInvitationEmailNotifications();
		
		/*App Login Erinnerung*/
       // \app\models\User::sendAppLoginNotifications();

        try {
            \app\models\UserStickToParentRequest::processExpired();
        } catch (\Exception $e) {}

        if ($e) {
            throw $e;
        }
    }

    public function actionCron60() {
        try {
            \app\models\CfrDistribution::distribute();
        } catch (\Exception $e) {}

        try {
            \app\models\User::processExpiredVipPackets();
        } catch (\Exception $e) {}

        try {
            \app\models\User::processVipPacketNotifications();
        } catch (\Exception $e) {}

        try {
            \app\models\OfferRequest::notifySellers();
        } catch (\Exception $e) {}

        try {
            \app\models\OfferRequest::notifyBuyers();
        } catch (\Exception $e) {}

        try {
            \app\models\User::sendNotFinishedRegistrationNotifications();
        } catch (\Exception $e) {}

        try {
            \app\models\User::sendTeamleaderFeedbackNotification();
        } catch (\Exception $e) {}

        try {
            \app\models\User::blockWithoutParent();
        } catch (\Exception $e) {}

        if ($e) {
            throw $e;
        }
    }

    public function actionCron1440() {
        try {
            \app\models\Offer::setExpiredStatus();
        } catch (\Exception $e) {}

        try {
            \app\models\SearchRequest::setExpiredStatus();
        } catch (\Exception $e) {}

        try {
            \app\models\User::updateStatOfferYearTurnover();
        } catch (\Exception $e) {}

        try {
            \app\models\User::updateStatActiveSearchRequests();
        } catch (\Exception $e) {}

        try {
            \app\models\User::updateStatOffersViewBuyRatio();
        } catch (\Exception $e) {}

        try {
            \app\models\User::updateStatMessagesPerDay();
        } catch (\Exception $e) {}

        try {
            \app\models\User::updateUserOfferRequestCompletedInterest();
        } catch (\Exception $e) {}

        try {
    		\app\models\AdvertisingSearchRequestState::apiTradetrackerConversion();
        } catch (\Exception $e) {}

        if ($e) {
            throw $e;
        }
    }


	public function actionCron00() {
        try {
            \app\models\User::resetDelayInviteMember();
        } catch (\Exception $e) {}

        if ($e) {
            throw $e;
        }
    }

    public function beforeAction($action)
    {
        if ($this->action->id == 'my' || $this->action->id == 'set-language' ) {
            Yii::$app->controller->enableCsrfValidation = false;
        }

		/*$language=false;
		$country_id_by_user_ip=Country::getCountryIdByCountryCode();
		
		if($country_id_by_user_ip){
			$language=Country::getCountryLanguage($country_id_by_user_ip);
		}
		
		if($language!=false && !empty($language) && !Yii::$app->session['language']){
			Yii::$app->session['language']=$language;
			Language::setLanguage();
		}else{
			if(!Yii::$app->session['language']){
				Yii::$app->session['language']="de";
			}
		}*/

        Language::setLanguage();

        return parent::beforeAction($action);
    }


    public function actionSetLanguage() {
        if (Yii::$app->request->isPost) {
            $language = Yii::$app->request->post('language');
            if(in_array($language, ['de','en','ru'])) {
                Yii::$app->session['language']=$language;
            }
        }
        return $this->redirect('/');
    }

    public function actionBecomeMember() {
		$facebookData = Yii::$app->session['facebook_infos'];
        $model=new \app\models\RegistrationDataForm();
        
		
		if($facebookData!=null){
			$model->first_name		=	$facebookData['first_name']? $facebookData['first_name'] : null;
			$model->last_name		=	$facebookData['last_name']? $facebookData['last_name'] : null;
			$model->sex				=	$facebookData['gender'] ? strtoupper($facebookData['gender'][0]) : null;
			$birthdate				=	explode('/',$facebookData['birthday']);
			$model->birth_day		=	$birthdate[1]? ltrim($birthdate[1], '0') : null;
			$model->birth_month		=	$birthdate[0]? $birthdate[0] : null;
			$model->birth_year		=	$birthdate[2]? $birthdate[2] : null;
			$model->city			=	$facebookData['location'] ? $facebookData['location']['name'] : null;
			$model->email			=	$facebookData['email']? $facebookData['email'] : null;
			$model->email_repeat	=	$facebookData['email']? $facebookData['email'] : null;		
			$model->facebook_id		=	$facebookData['id'];
		}

		$model->country_id=Country::getId();

		if(empty($_REQUEST['RegistrationDataForm']['existing_account']) && empty($_REQUEST['RegistrationDataForm']['existing_password']) ){
			$model->setScenario('becomeMemberNew');
			if ($model->load($_REQUEST) && $model->validate()) {
				$trx=Yii::$app->db->beginTransaction();

				$user=new \app\models\User;
				$user->setAttributes($model->attributes);
				$now=new \app\components\EDateTime();
				$user->registration_dt=$now->sqlDateTime();
				$user->show_in_become_member=true;
					if($model->facebook_id){
					$user->facebook_id=$model->facebook_id;
					}
				if($model->birth_day && $model->birth_month && $model->birth_year){
					$user->setBirthDay($model->birth_day);
					$user->setBirthMonth($model->birth_month);
					$user->setBirthYear($model->birth_year);	
				}
				$user->auth_key=Yii::$app->security->generateRandomString(32);
				$user->access_token=Yii::$app->security->generateRandomString(32);
				$user->encryptPwd();
				//$user->status=\app\models\User::STATUS_EMAIL_VALIDATION;
				$user->status=\app\models\User::STATUS_REGISTERED;
				$user->registered_by_become_member=1;
				$user->registration_from_desktop=1;
				$user->save();
				//$user->sendEmailValidation();

				Yii::$app->db->createCommand("
					INSERT INTO user_interest (user_id, level1_interest_id, type) 
						SELECT :user_id, id, type FROM interest WHERE parent_id is null", [
					':user_id'=>$user->id
				])->execute();

				$trx->commit();

				Yii::$app->user->login($user);

				//Yii::$app->session->setFlash('validation-popup',true);
				return $this->redirect(['site/my']);
			}
		}
		else{
			$model->setScenario('linkFacebook');	
			if($model->load($_REQUEST) && $model->validate()){
				$user=\app\models\User::find()->where('email=:email',[':email'=>$model->existing_account])->one();
				Yii::$app->db->createCommand("
					UPDATE user SET facebook_id=:fb_id WHERE id=:user_id LIMIT 1;", [
					':user_id'=>$user->id,
					':fb_id'=>$facebookData['id']
				])->execute();
				Yii::$app->user->login($user);
				return $this->redirect(['site/my']);
			}		
		}				 
		
		

        $data['model']=$model;
        return $this->render('become-member',$data);

    }
	
/*
    public function actionTestPush() {
            \app\models\SearchRequest::findOne(141)->sendPush();
    }
*/
	public function actionBecomeMemberFacebookLinking() {
		$facebookData = Yii::$app->session['facebook_infos'];
        $model=new \app\models\RegistrationDataForm();
		
		if($facebookData['email']){
		$model->existing_account=$facebookData['email'];	
		}
		
			$model->setScenario('linkFacebook');	
			if($model->load($_REQUEST) && $model->validate()){
				$user=\app\models\User::find()->where('email=:email',[':email'=>$model->existing_account])->one();
				Yii::$app->db->createCommand("
					UPDATE user SET facebook_id=:fb_id WHERE id=:user_id LIMIT 1;", [
					':user_id'=>$user->id,
					':fb_id'=>$facebookData['id']
				])->execute();
				Yii::$app->user->login($user);
				return $this->redirect(['site/my']);
			}		

        $data['model']=$model;
        return $this->render('become-member-facebook-linking',$data);

    }

    public function actionActivation($code) {
        $id=Yii::$app->security->validateData($code,Yii::$app->params['emailValidationSecret']);
        $model=\app\models\User::findOne($id);

        if ($model && $model->status==\app\models\User::STATUS_EMAIL_VALIDATION) {
            $model->status=\app\models\User::STATUS_REGISTERED;
            $model->save();
            Yii::$app->user->login($model);
        }

        return $this->redirect(['site/my']);
    }
	
    public function actionAppMembershipPayment($id) {
        $id=Yii::$app->security->validateData($id,Yii::$app->params['paymentIDSecret']);
        if (!$id) {
            throw new \yii\web\ForbiddenHttpException();
        }

        $payInRequest=\app\models\PayInRequest::findOne($id);

        if (!$payInRequest) {
            throw new \yii\web\ForbiddenHttpException();
        }

        if ($payInRequest->return_status!=\app\models\PayInRequest::RETURN_STATUS_AWAITING) {
            throw new \yii\web\ForbiddenHttpException();
        }

        return $this->renderPartial('app-membership-payment',['data'=>$payInRequest->getPaymentMethodData('http://jugl.net/dummy?back_app#')]);
    }
	
	



//    public function actionTestPdfInvoice() {
//        $payInRequestID = 48;
//        $payInRequest =\app\models\PayInRequest::findOne($payInRequestID);
//        $payInRequest->sendInvoice();
//    }

    /*
        public function actionDeadlock1() {
            $trx=Yii::$app->db->beginTransaction();

            //try {
                Yii::$app->db->createCommand("update user set stat_messages_per_day=coalesce(stat_messages_per_day,0)+0.000010 where id=2")->execute();
                sleep(2);
                Yii::$app->db->createCommand("update user set stat_messages_per_day=coalesce(stat_messages_per_day,0)+0.000010 where id=4")->execute();
            //} catch (\Exception $e) {
                //var_dump($e);exit;
            //}
            $trx->commit();

            die('ok');
        }

        public function actionDeadlock2() {
            $trx=Yii::$app->db->beginTransaction();

            //try {
                Yii::$app->db->createCommand("update user set stat_messages_per_day=coalesce(stat_messages_per_day,0)+0.000001 where id=4")->execute();
                sleep(2);
                Yii::$app->db->createCommand("update user set stat_messages_per_day=coalesce(stat_messages_per_day,0)+0.000001 where id=2")->execute();
            //} catch (\Exception $e) {
                //var_dump($e);exit;
            //}
            $trx->commit();

            die('ok');
        }
    */

/*
        public function actionTestVip($id) {

            $pir=\app\models\PayInRequest::findOne($id);
            $pir->confirm_status=\app\models\PayInRequest::CONFIRM_STATUS_SUCCESS;
            $pir->save();
            Yii::$app->user->identity->packet=\app\models\User::PACKET_VIP;
            Yii::$app->user->identity->save();

            die('done');
        }

    public function actionTestVipPlus($id) {

        $pir=\app\models\PayInRequest::findOne($id);
        $pir->confirm_status=\app\models\PayInRequest::CONFIRM_STATUS_SUCCESS;
        $pir->save();
        Yii::$app->user->identity->packet=\app\models\User::PACKET_VIP_PLUS;
        Yii::$app->user->identity->save();

        die('done');
    }
*/

    /*
    public function actionTestSms() {
        var_dump(Yii::$app->sms->send('017622114658','Überweisung'));
        exit;
    }
    */
/*
    public function actionTestPush() {
        Yii::$app->user->identity->sendInvitationPushNotification();
    }

    public function actionTestEmail() {
        Yii::$app->user->identity->sendInvitationEmailNotification();
    }
*/
/*
    public function actionTestCreateGroupChat() {
        echo \app\models\ChatUser::createGroupChat();
    }

    public function actionTestAddUserToGroupChat() {
        \app\models\ChatUser::findOne(167)->joinUserToGroupChat(Yii::$app->user->identity);
    }
*/

/*
    public function actionTestRegistrationBonus() {
        $users=\app\models\User::findBySql("
            select * from user
            where registration_dt between('2017-02-14 00:00:00' and '2017-02-19 00:00:00')
            and status='ACTIVE' and packet in ('VIP') and parent_registration_bonus=0
        ")->all();

        echo "found ".count($users)."<br/>";

        foreach($users as $user) {
            $trx=Yii::$app->db->beginTransaction();

            echo "processing user {$user->id} ({$user->email} {$user->first_name} {$user->last_name})<br/>";

            $user->addRegistrationBonusToParent();

            $trx->commit();
        }

        echo "done";
        exit;
    }
*/
    public function actionTestRecalc() {
        Yii::$app->user->identity->updateStatAwaitingFeedbacks();
    }

    public function actionTestAddVip() {
        //Yii::$app->user->identity->addVipPacket(3);
        //Yii::$app->user->identity->save();
    }

    public function actionTestAddVipPlus() {
        //$trx=Yii::$app->db->beginTransaction();
        //Yii::$app->user->identity->addVipPlusPacket(3);
        //Yii::$app->user->identity->save();
        //$trx->rollBack();
    }

    public function actionTestAddFunds() {
        //Yii::$app->user->identity->addBalanceLogItem(\app\models\BalanceLog::TYPE_PAYIN, 100, Yii::$app->user->identity, Yii::t('app', 'Buyed {jugl_sum} jugls for {currency_sum}€', [
        //    'jugl_sum' => 100,
        //    'currency_sum' => 1
        //]),true);
    }

    public function actionTestBuyToken() {
/*
        Yii::$app->user->identity->distributeTokenReferralPayment(1000, Yii::$app->user->identity,
            \app\models\BalanceTokenLog::TYPE_IN,
            \app\models\BalanceTokenLog::TYPE_IN_REF,
            \app\models\BalanceTokenLog::TYPE_IN_REF_REF,
            Yii::t('app', 'Buyed {jugl_sum} Tokens for {currency_sum}€', [
                'jugl_sum' => 1000,
                'currency_sum' => 100
            ]),0,'','','',true,false,true);
*/
    }


	public function actionSignOutMail(){
		if($_GET['user'] !== NULL && $_GET['authkey']!== NULL){
			$user = \app\models\User::find()->where('id=:id',[':id'=>$_GET['user']])->andWhere('auth_key=:auth_key',[':auth_key'=>$_GET['authkey']])->one();


			if($user && $user->allow_send_message_to_all_users == 0){
				$user->allow_send_message_to_all_users = 1;
				$user->save();
			}else{
				throw new NotFoundHttpException('The requested page does not exist.');
			}

			 /*
				$model=Video::find()->andWhere('video_id=:video_id',[':video_id'=>$id])->one();
				Yii::$app->db->createCommand("UPDATE user SET allow_send_message_to_all_users = 0 WHERE id = ".$_GET['user']."AND auth_key=".$_GET['authkey'])->execute();
			*/
			return $this->render('sign-out-mail');
		}else{
			 throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
	/*public function actionFacebook()
	{	
		$social = Yii::$app->getModule('social');
		$fb = $social->getFb(); // gets facebook object based on module settings
		try {
			$helper = $fb->getRedirectLoginHelper();
			$accessToken = $helper->getAccessToken();
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			// There was an error communicating with Graph
			return $this->render('validate-fb', [
				'out' => '<div class="alert alert-danger">' . $e->getMessage() . '</div>'
			]);
		}
		if (isset($accessToken)) { // you got a valid facebook authorization token
			$response = $fb->get('/me?fields=id,name,email,first_name,last_name,birthday,location,hometown', $accessToken);
			$user=$response->getGraphUser();
			$user_existing = \app\models\User::find()->where('facebook_id=:id',[':id'=>$user->id])->one();
			Yii::$app->session['forwardAfterLogin']=false;
			return $this->redirectAfterLogin();
				
			
		} elseif ($helper->getError()) {
			// the user denied the request
			// You could log this data . . .
			return $this->render('validate-fb', [
				'out' => '<legend>Validation Log</legend><pre>' .
				'<b>Error:</b>' . print_r($helper->getError(), true) .
				'<b>Error Code:</b>' . print_r($helper->getErrorCode(), true) .
				'<b>Error Reason:</b>' . print_r($helper->getErrorReason(), true) .
				'<b>Error Description:</b>' . print_r($helper->getErrorDescription(), true) .
				'</pre>'
			]);
		}
		return $this->render('validate-fb', [
			'out' => '<div class="alert alert-warning"><h4>Oops! Nothing much to process here.</h4></div>'
		]);
	}*/
	
	public function actionCashface(){
		if(Yii::$app->request->isGet){
			$params = Yii::$app->request->get();
		}
		if(Yii::$app->request->isPost){
			$params = Yii::$app->request->post();
		}		
		ExtService::setCashface($params);
	}
}
