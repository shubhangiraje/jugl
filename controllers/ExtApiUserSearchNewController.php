<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Country;
use app\components\EDateTime;


class ExtApiUserSearchNewController extends \app\components\ExtApiController {

    private function addTermsConditions($query,$field,$search) {
        $terms=preg_split('/[\s.,]+/',$search,-1,PREG_SPLIT_NO_EMPTY);
        foreach($terms as $term) {
            $query->andFilterWhere(['like', $field, $term]);
        }

        return count($terms);
    }

    public function getUsers($params) {
        $sex=$params['filter']['sex'];
        $name=$params['filter']['name'];
        $zipcity=$params['filter']['zipcity'];
        $age=$params['filter']['age'];
        $ageFrom=$params['filter']['ageFrom'];
        $ageTo=$params['filter']['ageTo'];
        $single=boolval($params['filter']['single']);
		
		if(is_array($params['filter']['country'])){
			foreach($params['filter']['country'] as $ids){
			$country_ids[]=$ids['id'];
			}
		}
		else{
			$country_ids=false;
		}
		
        $returnResults=trim($sex.$name.$zipcity.$age.$ageFrom.$ageTo.$single.$country_ids)!='';

        $pageNum=$params['pageNum']?:1;
        $pageCount=$params['pageCount']?:1;

        $perPage=50;

        $usersQuery=User::find()
            //->select(['user.*','name'=>"TRIM(CONCAT_WS(' ',user.nick_name,user.first_name,user.last_name,user.company_name))"])
            ->where('user.id!=:user_id AND status=:status_active AND (user.first_name IS NOT NULL AND TRIM(user.first_name)!="") AND (user.last_name IS NOT NULL AND TRIM(user.last_name)!="")',[':user_id'=>Yii::$app->user->id,':status_active'=>User::STATUS_ACTIVE])
            ->leftJoin('chat_user','user.id=chat_user.user_id')
            ->orderBy('chat_user.online desc, chat_user.online_mobile desc, first_name asc, last_name asc');

        if ($returnResults) {
            $usersQuery->andFilterWhere(['sex'=>$sex]);

            $this->addTermsConditions($usersQuery,"CONCAT_WS(' ',user.nick_name,user.first_name,user.last_name,user.company_name)",$name);
            $this->addTermsConditions($usersQuery,"CONCAT_WS(' ',user.zip,user.city)",$zipcity);

            if ($age!='') {
                $dateTo=(new EDateTime())->modify("-".intval($age)." year");
                $dateFrom=$dateTo->modifiedCopy("-1 year");
                $usersQuery->andWhere('birthday>:date_from and birthday<:date_to',[
                    ':date_from'=>$dateFrom->sqlDate(),
                    ':date_to'=>$dateTo->sqlDate()
                ]);
            }

            if ($ageFrom!='') {
                $dateTo=(new EDateTime())->modify("-".intval($ageFrom)." year");
                $usersQuery->andWhere('birthday<:date_to1',[
                    ':date_to1'=>$dateTo->sqlDate(),
                ]);
            }

            if ($ageTo!='') {
                $dateTo=(new EDateTime())->modify("-".intval($ageTo)." year");
                $usersQuery->andWhere('birthday>:date_to2',[
                    ':date_to2'=>$dateTo->sqlDate()
                ]);
            }

            if ($single) {
                $usersQuery->andWhere('marital_status=:marital_status_single',[
                    ':marital_status_single'=>\app\models\User::MARTIAL_STATUS_SINGLE
                ]);
            }
			if (count($country_ids)>0) {
				$usersQuery->andWhere(array('in','user.country_id',$country_ids));
			}

        }

        $searchUserCount = $usersQuery->/*groupBy(['user.id'])->*/count();

        $users=$usersQuery->offset(($pageNum-1)*$perPage)
            ->limit($perPage*$pageCount+1)
            ->with('chatUser','avatarFile')->all();


        $data=[
            'items'=>[],
            'hasMore'=>count($users)>$perPage*$pageCount,
            'searchUserCount' => $searchUserCount
        ];

        $users=array_slice($users,0,$perPage*$pageCount);
		$flagAry = Country::getListShort();
        foreach($users as $user) {
            $userData=$user->getShortData(['online','country_id']);
			$userData['flag']=$flagAry[$userData['country_id']];
            $userData['registration_dt']=(new EDateTime($user->registration_dt))->js();
            $data['items'][]=$userData;
        }

        return $data;
    }

    private function getNewUsers($pageNum=1,$country_ids) {

		$perPage=20;
			$query=User::find()
            ->where(['status'=>User::STATUS_ACTIVE])
            ->andWhere('registration_dt >= CURDATE()')
            ->orderBy(['dt_status_active'=>SORT_DESC])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);
		
        $users=$query->all();

        $hasMore=count($users)>$perPage;
        $data=[];
        foreach(array_slice($users,0,$perPage) as $user) {
            $userData=$user->getShortData(['online', 'country_id']);
            $userData['registration_dt']=(new EDateTime($user->registration_dt))->js();
            if($user->dt_status_active) {
                $userData['dt_status_active']=(new EDateTime($user->dt_status_active))->js();
            }
			/* NVII-MEDIA - Output Flag */
			$flagAry = Country::getListShort();
			$userData['flag'] = $flagAry[$userData['country_id']];
			/* NVII-MEDIA - Output Flag */
			
			
            $data[]=$userData;
        }

        return [
            'items'=>$data,
            'hasMore'=>$hasMore
            
        ];
		
    }


    public function actionSearchByName($name) {
        //$pageNum=$params['pageNum']?:1;
        //$pageCount=$params['pageCount']?:1;

        $perPage=50;

        $usersQuery=User::find()
            ->where('user.id!=:user_id and status!=:status_deleted',[':user_id'=>Yii::$app->user->id,':status_deleted'=>User::STATUS_DELETED])
            ->leftJoin('chat_user','user.id=chat_user.user_id');
            //->orderBy('chat_user.online desc, chat_user.online_mobile desc, name asc');

        $this->addTermsConditions($usersQuery,"CONCAT_WS(' ',user.first_name,user.last_name,user.nick_name,user.company_name)",$name);

        //$searchUserCount = $usersQuery->groupBy(['user.id'])->count();

        $users=$usersQuery
            //->offset(($pageNum-1)*$perPage)
            ->limit($perPage)
            ->with('chatUser','avatarFile')->all();

        $data=[
            'items'=>[],
            //'hasMore'=>count($users)>$perPage*$pageCount,
            //'searchUserCount' => $searchUserCount
        ];

        //$users=array_slice($users,0,$perPage*$pageCount);
		$flagAry = Country::getListShort();
        foreach($users as $user) {
            $userData=[
                'id'=>$user->id,
                'userName'=>$user->first_name.' '.$user->last_name,
                'avatar_mobile_url'=>$user->getAvatarThumbUrl('avatarMobile'),
                'status'=>$user->chatUser->online ? 2:($user->chatUser->online_mobile ? 1:0),
				'flag'=>$flagAry[$user->country_id]
            ];
            $data['items'][]=$userData;
        }

        return $data;
    }

    public function actionUsers() {
        return $this->getUsers(
            Yii::$app->request->getBodyParams()
        );
    }

    public function actionNewUsers($pageNum,$country_ids=false) {
        return $this->getNewUsers($pageNum,$country_ids);
    }
	/*nviimedia*/
	public function currentCountry(){
		$countryAry = Country::getList();
		$countryShortAry = Country::getListShort();
		$data = array();
		$data['country_id'] = Yii::$app->user->identity->country_id;
		$data['country_name'] = $countryAry[Yii::$app->user->identity->country_id];
		$data['country_shortname'] = $countryShortAry[Yii::$app->user->identity->country_id];
		return $data;
	}
	/*nviimedia*/


}
