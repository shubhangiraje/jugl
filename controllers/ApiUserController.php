<?php

namespace app\controllers;

use app\components\EDateTime;
use app\components\Language;
use app\models\TrollboxMessage;
use app\models\UserDevice;
use Yii;
use app\models\User;
use app\models\Country;
use yii\web\NotFoundHttpException;


class ApiUserController extends \app\components\ApiController {

    private function getHierarchy3($baseUser) {
        $user=$baseUser;
        $isCurrentUserChild=false;
        do {
            if ($user->id=Yii::$app->user->id) {
                $isCurrentUserChild=true;
                break;
            }
            $user=$user->parent;
        } while ($user);

        if (!$isCurrentUserChild) {
            throw new \yii\web\HttpException(403);
        }

        $user=User::find()->andWhere(['id'=>$baseUser->id])->with(['avatarFile',
            'users','users.avatarFile',
            'users.users','users.users.avatarFile',
            'users.users.users','users.users.users.avatarFile',
            'users.users.users.users'])->one();

        $data=$user->getShortData();
        $data['users']=[];
        foreach ($user->users as $user1) {
            $data1 = $user1->getShortData();
            $data1['users'] = [];

            foreach ($user1->users as $user2) {
                $data2=$user2->getShortData();
                $data2['users']=[];
                foreach ($user2->users as $user3) {
                    $data3=$user3->getShortData();
                    if (!empty($user3->users)) $data3['users']=true;
                    $data2['users'][] = $data3;
                }
                $data1['users'][] = $data2;
            }

            $data['users'][]=$data1;
        }


        $prevUser=$baseUser;
        if ($prevUser->parent) {
            $prevUser=$prevUser->parent;
        }
        if ($prevUser->parent) {
            $prevUser=$prevUser->parent;
        }
        if ($prevUser->parent) {
            $prevUser=$prevUser->parent;
        }

        return ['hierachy'=>[
            'user'=>$data,
            'parent'=>$prevUser->id!=$baseUser->id ? $prevUser->id:null
        ]];
    }

    public function actionStatus() {
        $user=Yii::$app->user->identity;
        $data=$user->toArray([
            'id',
            'balance',
            'balance_earned',
            'balance_token_deposit_percent',
            'balance_buyed',
            'network_size',
            'new_network_members',
            'new_events',
            'status',
            'packet',
            'sex',
			'birthday',
			'city',
            'is_moderator',
            'first_name',
            'last_name',
            'nick_name',
            'stat_new_offers',
            'stat_new_offers_requests',
            'stat_new_search_requests',
            'stat_new_search_requests_offers',
            'stat_awaiting_feedbacks',
            'registration_from_desktop',
            'show_start_popup',
            'show_friends_invite_popup',
            'block_parent_team_requests',
			'later_profile_fillup_date',
            'parent_id',
            'is_company_name',
            'company_name',
			'delay_invited_member',
            'new_events',
            'new_follower_events',
            'is_blocked_in_trollbox',
            'validation_phone_status',
            'parent_registration_bonus',
            'country_id',
            'pixel_registration_notified',
            'allow_moderator_country_change',
            'balance_token',
            'balance_token_earned',
            'balance_token_buyed',
            'is_update_country_after_login',
            'video_identification_status'
        ]);

        $data['delay_invited_member']=0;
        $data['later_profile_fillup_date']= (new \app\components\EDateTime($data['later_profile_fillup_date']))->modify('+2 days') < (new \app\components\EDateTime()) ? 1 : 0;

        $data['balance']=floatval($data['balance']);
        $data['balance_earned']=floatval($data['balance_earned'])+floatval($data['balance_token_deposit_percent']);
        $data['balance_buyed']=floatval($data['balance_buyed']);

        $data['balance_token']=floatval($data['balance_token']);
        $data['balance_token_earned']=floatval($data['balance_token_earned']);
        $data['balance_token_buyed']=floatval($data['balance_token_buyed']);

        $data['not_force_packet_selection']=$user->not_force_packet_selection_till &&
            (new \app\components\EDateTime($user->not_force_packet_selection_till))>(new \app\components\EDateTime());

        $data['availableStickRequestsCount']=$user->getAvailableStickRequestsCount();

        $data['teamChangeFinishTime']=$user->getTeamChangeFinishTime(true);
        $data['showTeamleaderFeedbackNotification']=$user->showTeamleaderFeedbackNotification();
        if (in_array($user->packet,[\app\models\User::PACKET_VIP,\app\models\User::PACKET_VIP_PLUS]) &&
            (new \app\components\EDateTime($user->vip_active_till))->modify('-1 week')<(new \app\components\EDateTime())
            ) {
            $data['vipProlongActive']=true;
        }

        if ($user->avatarFile) {
            $data['avatarFile']=$user->avatarFile->getFrontImageData(['avatar','avatarSmall']);
        } else {
            $data['avatarFile']['thumbs']['avatar']=$user->avatarUrl;
            $data['avatarFile']['thumbs']['avatarSmall']=$user->avatarUrl;
        }

        $data['use_app'] = boolval(Yii::$app->user->identity->invitation_notification_start);


        if($user->dt_parent_change && ((new EDateTime()) < (new EDateTime($user->dt_parent_change))->modify('+1 day'))) {
            $data['block_team_change'] = true;
        }
		
		$data['labels']=array(
			'itemsSelected'=>Yii::t('app','ausgewählt'),
			'search'=>Yii::t('app','suchen...'),
			'select'=>Yii::t('app','Land auswählen'),
			'selectAll'=>Yii::t('app','Alle auswählen'),
			'unselectAll'=>Yii::t('app','Auswahl aufheben')
		);
		$data['currentCountry']=$this->currentCountry();

        return $data;
    }

    public function actionSaveShowStartPopup() {
        Yii::$app->db->createCommand("UPDATE user SET show_start_popup=1 WHERE id=:id", [
            ':id'=>Yii::$app->user->identity->id,
        ])->execute();

        return [
            'result'=>true,
        ];
    }
	
	public function actionSaveFriendsInvitationPopup() {
        Yii::$app->db->createCommand("UPDATE user SET show_friends_invite_popup=1 WHERE id=:id", [
            ':id'=>Yii::$app->user->identity->id,
        ])->execute();

        return [
            'result'=>true,
        ];
    }


    public function actionInviteFriendsCount() {
        $invite_friends_count = Yii::$app->user->identity->getFriendInvitationsLeftCount();

        if($invite_friends_count==0 || $invite_friends_count<0) {
            Yii::$app->db->createCommand("UPDATE user SET show_friends_invite_popup=1 WHERE id=:id", [
                ':id'=>Yii::$app->user->identity->id,
            ])->execute();
            return ['result'=>false];
        }

        return [
            'result'=>true,
            'invite_friends_count'=>$invite_friends_count
        ];
    }

    public function actionToggleBlockParentTeamRequests() {
        Yii::$app->user->identity->block_parent_team_requests=Yii::$app->user->identity->block_parent_team_requests ? 0:1;
        Yii::$app->user->identity->save();
        return ['result'=>true];
    }
	
	public function currentCountry(){
		$countryAry = Country::getList();
		$countryShortAry = Country::getListShort();
		$data = array();
		
		$data['country_id'] = Yii::$app->user->identity->country_id;
		$data['country_name'] = $countryAry[Yii::$app->user->identity->country_id];
		$data['country_shortname'] = $countryShortAry[Yii::$app->user->identity->country_id];
		return $data;
		
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