<?php

namespace app\controllers;

use Yii;
use \app\models\User;
use \app\components\EDateTime;
use app\models\Country;


class ExtApiNetworkController extends \app\components\ExtApiController {

    private function getHierarchy($baseUser, $user_hierarchy_id) {
        $user=User::findOne($baseUser->id);
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

        $users=User::find()->andWhere(['parent_id'=>$baseUser->id])->with(
            ['avatarFile','users'])->all();
		
        $usersData=[];
        foreach ($users as $user) {
            $data = $user->getShortData(['country_id']);
            /* NVII-MEDIA - Output Flag */
            $flagAry = Country::getListShort();
            $data['flag'] = $flagAry[$data['country_id']];
            /* NVII-MEDIA - Output Flag */
            $data['hasChildren'] = count($user->users)>0;
            if(!in_array($user->status,[User::STATUS_REGISTERED,User::STATUS_LOGINED])) {
                $usersData[]=$data;
            }
        }


        $prevUser=$baseUser;

        if($user_hierarchy_id) {
            if ($prevUser->parent && $prevUser->id!=$user_hierarchy_id) {
                $prevUser=$prevUser->parent;
            }
        } else {
            if ($prevUser->parent && $prevUser->id!=Yii::$app->user->id) {
                $prevUser=$prevUser->parent;
            }
        }

        
        return [
            'user'=>$baseUser->getShortData(),
            'users'=>$usersData,
            'parent'=>$prevUser->id!=$baseUser->id ? $prevUser->id:null,
            'user_hierarchy_id'=>$user_hierarchy_id
        ];
    }



    private function getNewNetworkMembers($pageNum=1) {
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
            'items'=>$data,
            'hasMore'=>$hasMore
        ];
    }


    public function actionHierarchy($user_id, $user_hierarchy_id=null) {
        $user = User::findOne($user_id);
        if(!$user_hierarchy_id) {
            $user_hierarchy_id = $user->id;
        }
        return $this->getHierarchy($user, $user_hierarchy_id);
    }


    public function actionNewUsers($pageNum) {
        return $this->getNewNetworkMembers($pageNum);
    }




}