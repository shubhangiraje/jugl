<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Country;


class ApiNetworkController extends \app\components\ApiController {


    private function getHierarchy3($baseUser, $parentUserID=null) {
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

        $user=User::find()->andWhere(['id'=>$baseUser->id])
            ->with(['avatarFile',
            'users','users.avatarFile',
            'users.users','users.users.avatarFile',
            'users.users.users','users.users.users.avatarFile',
            'users.users.users.users'])->one();
			
		/* NVII-MEDIA - Output Flag */
		$flagAry = Country::getListShort();
        $data=$user->getShortData(['country_id']);
		$data['flag'] = $flagAry[$data['country_id']];
		/* NVII-MEDIA - Output Flag */
		
        $data['users']=[];
        foreach ($user->users as $user1) {
            $data1 = $user1->getShortData(['country_id']);
			/* NVII-MEDIA - Output Flag */
			$data1['flag'] = $flagAry[$data1['country_id']];
			/* NVII-MEDIA - Output Flag */
            $data1['users'] = [];

            foreach ($user1->users as $user2) {
                $data2=$user2->getShortData(['country_id']);
				/* NVII-MEDIA - Output Flag */
				$data2['flag'] = $flagAry[$data2['country_id']];
				/* NVII-MEDIA - Output Flag */
                $data2['users']=[];
	
                foreach ($user2->users as $user3) {
                    $data3=$user3->getShortData(['country_id']);
					/* NVII-MEDIA - Output Flag */
					$data3['flag'] = $flagAry[$data3['country_id']];
					/* NVII-MEDIA - Output Flag */
                    if (!empty($user3->users)) $data3['users']=true;
                    if(!in_array($user3->status,[User::STATUS_REGISTERED,User::STATUS_LOGINED])) {
                        $data2['users'][] = $data3;
                    }
                }

                if(!in_array($user2->status,[User::STATUS_REGISTERED,User::STATUS_LOGINED])) {
                    $data1['users'][] = $data2;
                }
            }

            if(!in_array($user1->status,[User::STATUS_REGISTERED,User::STATUS_LOGINED])) {
                $data['users'][]=$data1;
            }
        }

        $prevUser=$baseUser;
        if($parentUserID) {
            if ($prevUser->parent && $prevUser->id!=$parentUserID) {
                $prevUser=$prevUser->parent;
            }
            if ($prevUser->parent && $prevUser->id!=$parentUserID) {
                $prevUser=$prevUser->parent;
            }
            if ($prevUser->parent && $prevUser->id!=$parentUserID) {
                $prevUser=$prevUser->parent;
            }
        } else {
            if ($prevUser->parent && $prevUser->id!=Yii::$app->user->id) {
                $prevUser=$prevUser->parent;
            }
            if ($prevUser->parent && $prevUser->id!=Yii::$app->user->id) {
                $prevUser=$prevUser->parent;
            }
            if ($prevUser->parent && $prevUser->id!=Yii::$app->user->id) {
                $prevUser=$prevUser->parent;
            }
        }



        return ['hierarchy'=>[
            'user'=>$data,
            'parent'=>$prevUser->id!=$baseUser->id ? $prevUser->id:null
        ]];
    }

    public function actionHierarchy() {
        $params=json_decode($_REQUEST['urlState'],true);
        return $this->getHierarchy3(User::findOne($params['user_id']));
    }

    public function actionIndex() {
        $params=json_decode($_REQUEST['urlState'],true);
        $user=Yii::$app->user->identity;

        return array_merge(
            ['user'=>$user->toArray(['network_size','network_levels'])],
            $this->getHierarchy3(User::findOne($params['user_id']?:Yii::$app->user->id))
        );
    }

    public function actionUserHierarchy() {
        $params=json_decode($_REQUEST['urlState'],true);
        $parent_id=json_decode($_REQUEST['parent_id'],true);
        return $this->getHierarchy3(User::findOne($params['user_id']), $parent_id);
    }


    public function actionUserNetwork($id) {
        $params=json_decode($_REQUEST['urlState'],true);
        $user=User::findOne($id);

        return array_merge(
            ['user'=>$user->toArray(['id','network_size','network_levels','last_name','first_name'])],
            $this->getHierarchy3(User::findOne($params['user_id']?:$user->id), $user->id)
        );
    }


}