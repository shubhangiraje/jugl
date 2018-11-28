<?php

namespace app\controllers;

use app\components\EDateTime;
use app\models\base\User;
use Yii;
use app\models\UserFriend;
use yii\web\ForbiddenHttpException;
use app\models\UserFriendRequest;
use app\models\Country;

class ApiNetworkMembersController extends \app\components\ApiController {

    public function getNetworkMembers($params) {
        $sort=$params['filter']['sort'];
        $statusFilter=$params['filter']['statusFilter'];
        $nameFilter=$params['filter']['nameFilter'];
        $pageNum=$params['pageNum']?:1;
        $pageCount=$params['pageCount']?:1;

        $perPage=24;

        $networkMembersQuery=Yii::$app->user->identity->hasMany('\app\models\UserReferral', ['user_id' => 'id'])
            ->select(['user_referral.*'])
            ->innerJoin('user','user_referral.referral_user_id=user.id')
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage*$pageCount+1);

        switch ($sort) {
            case 'alpha':
                $networkMembersQuery->addSelect(["TRIM(CONCAT_WS(' ',nick_name,first_name,last_name)) as name"]);
                $networkMembersQuery->orderBy('name asc');
                break;
            default:
                //$networkMembersQuery->orderBy('user_referral.id desc');
        }

        switch ($statusFilter) {
            case 'online':
                $networkMembersQuery->innerJoin('chat_user','(chat_user.online=1 or chat_user.online_mobile=1) and user_referral.referral_user_id=chat_user.user_id');
                break;
            default:
        }

        $searchTerms=preg_split('/[\s.,]+/',$nameFilter,-1,PREG_SPLIT_NO_EMPTY);
        foreach($searchTerms as $k=>$term) {
            $networkMembersQuery->andWhere("CONCAT_WS(' ',user.nick_name,user.first_name,user.last_name) like (:term_$k)",[":term_$k"=>"%$term%"]);
        }

        $networkMembers=$networkMembersQuery->with('referralUser','referralUser.chatUser','referralUser.avatarFile')->all();

        $data=[
            'users'=>[],
            'hasMore'=>count($networkMembers)>$perPage*$pageCount
        ];
		$flagAry = Country::getListShort();
        $networkMembers=array_slice($networkMembers,0,$perPage*$pageCount);
        foreach($networkMembers as $key=>$networkMember) {
            $data['users'][$key]=$networkMember->referralUser->getShortData(['online,country_id']);
			$flag = $flagAry[$networkMember->referralUser->country_id];
			/* NVII-MEDIA - Output Flag */
			$data['users'][$key]['flag']=$flag;
        }

        return ['networkMembers'=>$data];
    }

    public function actionNetworkMembers() {
        $params=json_decode($_REQUEST['urlState'],true);
        return $this->getNetworkMembers($params['networkMembers']);
    }

    public function actionMarkAsSeen() {
        Yii::$app->user->identity->new_network_members=0;
        Yii::$app->user->identity->save();
        return [];
    }

    public function actionIndex() {
        $params=json_decode($_REQUEST['urlState'],true);
        $params['networkMembers']['pageCount']=$params['networkMembers']['pageNum'];
        $params['networkMembers']['pageNum']=1;
        return $this->getNetworkMembers($params['networkMembers']);
    }

    public function actionNewUsers($pageNum=1) {
        $perPage = 20;
        $query=Yii::$app->user->identity->hasMany('\app\models\UserReferral', ['user_id' => 'id'])
            ->select(['user_referral.*'])
            ->innerJoin('user','user_referral.referral_user_id=user.id')
            ->orderBy(['user.registration_dt'=>SORT_DESC]);
        $query->with('referralUser','referralUser.chatUser','referralUser.avatarFile');
        $query->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $data = [];
        $networkMembers=$query->all();
        $hasMore=count($networkMembers)>$perPage;
        foreach (array_slice($networkMembers,0,$perPage) as $item) {
            $idata = $item->referralUser->getShortData(['online']);
            $idata['registration_dt'] = (new EDateTime($item->referralUser->registration_dt))->js();
            if($item->referralUser->dt_status_active) {
                $idata['dt_status_active'] = (new EDateTime($item->referralUser->dt_status_active))->js();
            }
            $data[] = $idata;
        }

        return [
            'log'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ]
        ];

    }

}
