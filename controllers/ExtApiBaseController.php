<?php

namespace app\controllers;

use app\models\TrollboxMessage;
use app\models\User;
use app\models\Country;
use app\models\UserDevice;
use Yii;
use \app\models\ExtApiLoginForm;
use \app\components\EDateTime;
use \yii\helpers\Url;
use yii\web\NotFoundHttpException;

class ExtApiBaseController extends \app\components\ExtApiController {

    function actionLogin() {
        $model = new ExtApiLoginForm();
        $model->load(Yii::$app->request->getBodyParams());

        if ($model->login()) {
            if (Yii::$app->user->identity->registration_from_desktop) {
                Yii::$app->user->identity->registration_from_desktop=0;
                Yii::$app->user->identity->save();
                Yii::$app->user->identity->addRegistrationBonusToParent();
            }
			if(Yii::$app->user->identity->status==\app\models\User::STATUS_REGISTERED){
				Yii::$app->user->identity->status=\app\models\User::STATUS_ACTIVE;
				Yii::$app->user->identity->save();
			}
            return ['key'=>Yii::$app->user->identity->userDevice->key,'status'=>$this->actionStatus()];
        } else {
            $errors=$model->getFirstErrors();
            return ['error'=>array_shift($errors)];
        }
    }
	
	function actionLoginFacebook() {
        $model = new ExtApiLoginForm();		
        $model->load(Yii::$app->request->getBodyParams());
        $model->setScenario('facebook_login');
        //If user is existing, sign in
        if ($model->loginWithFacebook()) {
            if (Yii::$app->user->identity->registration_from_desktop) {
                Yii::$app->user->identity->registration_from_desktop=0;
                Yii::$app->user->identity->save();
                Yii::$app->user->identity->addRegistrationBonusToParent();
            }
            if(Yii::$app->user->identity->status==\app\models\User::STATUS_REGISTERED){
				Yii::$app->user->identity->status=\app\models\User::STATUS_ACTIVE;
				Yii::$app->user->identity->save();
			}
            return ['key'=>Yii::$app->user->identity->userDevice->key,'status'=>$this->actionStatus(),'facebook_user'=>true];
		//if not then create new user with random password
		} else {
				$facebook_id=Yii::$app->request->getBodyParams()['ExtApiLoginForm']['facebook_id'];
				$trxReg=Yii::$app->db->beginTransaction();
					$data=json_decode(Yii::$app->request->getBodyParams()['ExtApiLoginForm']['facebook_user'],true);
					$data['country_id']=Country::getId();
					$data['password']=Yii::$app->security->generateRandomString(30).'b1';
					$data['password_repeat']=$data['password'];
					$modelReg=new \app\models\RegistrationDataForm();
					$modelReg->setScenario('becomeMemberNew');
					$modelReg->load($data,'');	
                    //if valid (Email does not exist and Device_id<=2)
                    if ($modelReg->validate()) {
						$user=new \app\models\User;
						$user->setAttributes($modelReg->attributes);
						$now=new \app\components\EDateTime();
						$user->registration_dt=$now->sqlDateTime();
						$user->facebook_id=$facebook_id;
						
						if($modelReg->birth_day && $modelReg->birth_month && $modelReg->birth_year){
								$user->setBirthDay($modelReg->birth_day);
								$user->setBirthMonth($modelReg->birth_month);
								$user->setBirthYear($modelReg->birth_year);	
						}
						
						$user->show_in_become_member=true;
						$user->auth_key=Yii::$app->security->generateRandomString(32);
						$user->access_token=Yii::$app->security->generateRandomString(32);
						$user->encryptPwd();
						$user->status=\app\models\User::STATUS_REGISTERED;
						//$user->status=\app\models\User::STATUS_EMAIL_VALIDATION;
						$user->registered_by_become_member=1;
						$user->save();
						//$user->sendEmailValidation();

						Yii::$app->db->createCommand("
							INSERT INTO user_interest (user_id, level1_interest_id, type) 
								SELECT :user_id, id, type FROM interest WHERE parent_id is null", [
							':user_id'=>$user->id
						])->execute();

						//Yii::$app->user->login($user);
						
						$modelInstantLogin = new ExtApiLoginForm();		
						$modelInstantLogin->load(Yii::$app->request->getBodyParams());
						$modelInstantLogin->setScenario('facebook_login');
						
						if ($modelInstantLogin->loginWithFacebook()) {
                            $trxReg->commit();    
							if (Yii::$app->user->identity->registration_from_desktop) {
								Yii::$app->user->identity->registration_from_desktop=0;
								Yii::$app->user->identity->save();
								Yii::$app->user->identity->addRegistrationBonusToParent();
                            }
                            if(Yii::$app->user->identity->status==\app\models\User::STATUS_REGISTERED){
                                Yii::$app->user->identity->status=\app\models\User::STATUS_ACTIVE;
                                Yii::$app->user->identity->save();
                            }
							return ['key'=>Yii::$app->user->identity->userDevice->key,'status'=>$this->actionStatus(),'result'=>true];
                        }
                        else{
                            $trxReg->rollBack();
                            $errors=$modelInstantLogin->getFirstErrors();
                            return ['error'=>array_shift($errors)];	
                        }
					}
					else{
						$trxReg->rollBack();	
						$errors=$modelReg->getFirstErrors();
						if(!$data['email'] || $data['email']=="" || empty($data['email'])){
							return ['register'=>true];		
						}
						else{
							return ['redirect'=>array_shift($errors)];		
						}						
					}					
				
        }
    }

    function actionIsDeviceUsedForRegistration() {
//        if (\app\models\KnownDevice::isDeviceUsed(Yii::$app->request->getHeaders()->get('X-Ext-Api-Auth-Device-Uuid'))) {
//            return ['error'=>Yii::t('app',"Über dieses Gerät wurde bereits ein Jugl-Profil erstellt.\nPro User ist nur eine Mitgliedschaft gestattet.\nWenn Du Mitglied werden möchtest und kein Smartphone hast, lass Dir von einem Freund einen Einladungsgutschein für Jugl zusenden.\nDiesen erhält er unter www.jugl.net.")];
//        }
    }

    function actionStatus() {
        $user=Yii::$app->user->identity;
        $userInfo=$user->toArray([
            'id',
			'country_id',
            'new_events',
            'status',
            'first_name',
            'last_name',
            'nick_name',
            'zip','city',
            'street',
            'house_number',
            'network_size',
            'invitations',
            'company_name',
            'is_company_name',
            'packet',
            'stat_new_offers',
            'stat_new_offers_requests',
            'stat_new_search_requests',
            'stat_new_search_requests_offers',
            'stat_awaiting_feedbacks',
            'setting_off_send_email',
			/*nvii-media MERGE BRANCHE VRO TO ACC CORRECT? 
			'later_profile_fillup_date',
			nvii-media MERGE BRANCHE VRO TO ACC CORRECT? */
            'setting_notification_likes',
            'setting_notification_comments',
            'is_moderator',
            'block_parent_team_requests',
            'parent_id',
			'delay_invited_member',
            'new_events',
            'new_follower_events',
            'is_blocked_in_trollbox',
            'validation_phone_status',
            'parent_registration_bonus',
            'pixel_registration_notified',
            'allow_moderator_country_change',
            'balance_token',
            'balance_token_buyed',
            'balance_token_earned',
            'is_update_country_after_login',
            'video_identification_status',
            'video_identification_uploads'
        ]);

        $userInfo['delay_invited_member']=0;

        $userInfo['balance']=floatval($user->balance);
        $userInfo['balance_earned']=floatval($user->balance_earned)+floatval($user->balance_token_deposit_percent);
        $userInfo['balance_buyed']=floatval($user->balance_buyed);

        $userInfo['balance_token']=floatval($user->balance_token);
        $userInfo['balance_token_buyed']=floatval($user->balance_token_buyed);
        $userInfo['balance_token_earned']=floatval($user->balance_token_earned);

        $userInfo['later_profile_fillup_date']= (new \app\components\EDateTime($userInfo['later_profile_fillup_date']))->modify('+2 days') < (new \app\components\EDateTime()) ? 1 : 0;
        $userInfo['earned_this_year']=$user->earnedThisYear;
        $userInfo['earned_today']=$user->earnedToday;
        $userInfo['earned_yesterday']=$user->earnedYesterday;
        $userInfo['earned_this_month']=$user->earnedThisMonth;
        $userInfo['earned_total']=$user->earnedTotal;
        $userInfo['sex']=$user->sexLabel;
        $userInfo['country_short_name']=strtoupper($user->countryShortName);
        $userInfo['teamChangeFinishTime']=$user->getTeamChangeFinishTime(true);
        $userInfo['showTeamleaderFeedbackNotification']=$user->showTeamleaderFeedbackNotification();
        if (in_array($user->packet,[\app\models\User::PACKET_VIP,\app\models\User::PACKET_VIP_PLUS]) &&
            (new \app\components\EDateTime($user->vip_active_till))->modify('-1 week')<(new \app\components\EDateTime())
        ) {
            $userInfo['vipProlongActive']=true;
        }

        $userInfo['not_force_packet_selection']=$user->not_force_packet_selection_till &&
            (new \app\components\EDateTime($user->not_force_packet_selection_till))>(new \app\components\EDateTime());

        $userInfo['availableStickRequestsCount']=$user->getAvailableStickRequestsCount();

        if($user->birthday!=NULL) {
            $birthday=new EDateTime($user->birthday);
            $userInfo['birthday']=$birthday->format('d.m.Y');
            $userInfo['years']=(new EDateTime())->diff($birthday)->y;
        }

        $userInfo['avatarMobile']=$user->getAvatarThumbUrl('avatarMobile');

        if($user->dt_parent_change && ((new EDateTime()) < (new EDateTime($user->dt_parent_change))->modify('+1 day'))) {
            $userInfo['block_team_change'] = true;
        }

        $userInfo['can_upload_video_identification']=Yii::$app->user->identity->canUploadVideoIdentification();


        /*if (!$trollboxMessage) {
            $userInfo['can_upload_video_identification']=true;
        } else if ($trollboxMessage && ((new \app\components\EDateTime($trollboxMessage->dt))->modify('+1 day')<(new \app\components\EDateTime()))) {
            $userInfo['can_upload_video_identification']=true;
        } else {
            $userInfo['can_upload_video_identification']=false;
        }*/


        $data=[
            'user'=>$userInfo,
            'chatAuthorizationKey'=>Yii::$app->user->identity->chatAuthorizationKey,
            'refLink'=>Url::to(['registration/index','refId'=>$user->id],true)
        ];
		
		$data['labels']=array(
			'itemsSelected'=>Yii::t('app','ausgewählt'),
			'search'=>Yii::t('app','suchen...'),
			'select'=>Yii::t('app','Land auswählen'),
			'selectAll'=>Yii::t('app','Alle auswählen'),
			'unselectAll'=>Yii::t('app','Auswahl aufheben'),
			'loadMore'=>Yii::t('app','Alle Länder anzeigen')
		);

        return $data;
    }

    function actionLogout() {
        $userDevice=Yii::$app->user->identity->userDevice;
        $userDevice->delete();
    }

	
	
    function actionInviteFriendsStartTime() {
        $invite_friends_count = Yii::$app->user->identity->getFriendInvitationsLeftCount();

        $data=[
            'invite_friends_count'=>$invite_friends_count,
            'invitation_notification_start' => (new EDateTime(Yii::$app->user->identity->invitation_notification_start))->js()
        ];

        return $data;
    }


    public function actionUpdateSettingOffSendEmail() {
        $value=Yii::$app->request->getBodyParams()['value'];
        Yii::$app->db->createCommand("UPDATE user SET setting_off_send_email=:setting_off_send_email WHERE id=:id", [
            ':id'=>Yii::$app->user->identity->id,
            ':setting_off_send_email'=>$value
        ])->execute();
        return [];
    }

    public function actionUpdateSettings() {
        Yii::$app->db->createCommand("
            UPDATE user 
            SET setting_off_send_email=:setting_off_send_email,
            setting_notification_likes=:setting_notification_likes,
            setting_notification_comments=:setting_notification_comments
            WHERE id=:id", [
                ':id'=>Yii::$app->user->identity->id,
                ':setting_off_send_email'=>!Yii::$app->request->getBodyParam('settings')['setting_send_email'],
                ':setting_notification_likes'=>Yii::$app->request->getBodyParam('settings')['setting_notification_likes'],
                ':setting_notification_comments'=>Yii::$app->request->getBodyParam('settings')['setting_notification_comments'],
        ])->execute();
        return [];
    }

    public function actionToggleBlockParentTeamRequests() {
        Yii::$app->user->identity->block_parent_team_requests=Yii::$app->user->identity->block_parent_team_requests ? 0:1;
        Yii::$app->user->identity->save();
        return ['result'=>true];
    }

    public function actionUpdatePixelRegistrationNotified() {
        Yii::$app->db->createCommand('UPDATE user SET pixel_registration_notified=1 WHERE id=:id', [
            ':id'=>Yii::$app->user->id
        ])->execute();
        return true;
    }

    public function actionGetUpdateDataCountry($id) {
        $user = User::findOne($id);

        if(!$user) {
            throw new NotFoundHttpException();
        }

        return [
            'user'=>$user->getShortData(),
            'countries'=>$user->getCountries(),
        ];
    }

    public function actionUpdateCountry() {
        if(Yii::$app->user->identity->is_moderator && Yii::$app->user->identity->allow_moderator_country_change) {
            $country_id=Yii::$app->request->getBodyParams()['country_id'];
            $user_id=Yii::$app->request->getBodyParams()['user_id'];

            $result = Yii::$app->db->createCommand('UPDATE user SET country_id=:country_id WHERE id=:user_id', [
                ':country_id'=>$country_id,
                ':user_id'=>$user_id
            ])->execute();

            return [
                'result'=>$result,
                'flag'=> Country::getListShort()[$country_id]
            ];
        } else {
            return ['result'=>false];
        }
    }

    public function actionAutoUpdateCountry() {
        Yii::$app->db->createCommand('UPDATE user SET country_id=:country_id, is_update_country_after_login=1 WHERE id=:user_id', [
            'country_id'=>Country::getId(),
            'user_id'=>Yii::$app->user->id
        ])->execute();
        return ['result'=>true];
    }

}