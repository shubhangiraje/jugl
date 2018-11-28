<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Country;
use app\components\EDateTime;


class ApiUserSearchController extends \app\components\ApiController {

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
        $zip=$params['filter']['zip'];
        $city=$params['filter']['city'];
        $zipcity=$params['filter']['zip_city'];
        $ageFrom=$params['filter']['ageFrom'];
        $ageTo=$params['filter']['ageTo'];
        $single=boolval($params['filter']['single']);
		
		if(is_array($params['filter']['country_ids'])){
			foreach($params['filter']['country_ids'] as $ids){
			$country_ids[]=$ids['id'];
			}
		}

        $returnResults=trim($sex.$name.$zip.$city.$zipcity.$ageFrom.$ageTo.$single.$country_ids)!='';

        $pageNum=$params['pageNum']?:1;
        $pageCount=$params['pageCount']?:1;

        $perPage=24;

        $usersQuery=User::find()
            ->select(['user.*',"TRIM(CONCAT_WS(' ',user.nick_name,user.first_name,user.last_name,user.company_name,user.country_id)) as name"])
            ->where('user.id!=:user_id AND status=:status_active AND (user.first_name IS NOT NULL AND TRIM(user.first_name)!="") AND (user.last_name IS NOT NULL AND TRIM(user.last_name)!="")',[':user_id'=>Yii::$app->user->id,':status_active'=>User::STATUS_ACTIVE])
            ->leftJoin('chat_user','user.id=chat_user.user_id')
            ->orderBy('chat_user.online desc, chat_user.online_mobile desc, name asc')
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage*$pageCount+1);

        $usersQuery->andFilterWhere(['sex'=>$sex]);

        $this->addTermsConditions($usersQuery,"CONCAT_WS(' ',user.nick_name,user.first_name,user.last_name,user.company_name,user.country_id)",$name);
//        $this->addTermsConditions($usersQuery,'zip',$zip);
//        $this->addTermsConditions($usersQuery,'city',$city);
        $this->addTermsConditions($usersQuery,"CONCAT_WS(' ',user.zip,user.city)",$zipcity);

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
		/*nviimedia*/
		if ($country_ids) {
		$countries=$country_ids;	
			if(!in_array('no_countries',$countries)){
				$usersQuery->andWhere(['in','user.country_id',$countries]);
			}
			elseif(in_array('no_countries',$countries)){
				unset($countries['no_countries']);
				$usersQuery->andWhere(['in','user.country_id',$countries])->orWhere('user.country_id IS NULL');	
			}	
        }
		/*nviimedia*/
		

        if ($returnResults) {
            // don't use read locks for long query
            $trx=Yii::$app->db->beginTransaction('READ UNCOMMITTED');
            $users=$usersQuery->with('chatUser','avatarFile')->all();
            $trx->commit();
        } else {
            $users=[];
        }

        $data=[
            'users'=>[],
            'hasMore'=>count($users)>$perPage*$pageCount,
            'showResults'=>$returnResults
        ];

        $users=array_slice($users,0,$perPage*$pageCount);
		$flagAry = Country::getListShort();
        foreach($users as $friendKey => $friendVal) {
            $data['users'][]=$friendVal->getShortData(['online','country_id']);
			$data['users'][$friendKey]['flag'] = $flagAry[$friendVal['country_id']];
		}

        return ['currentCountry'=>$this->currentCountry(),'users'=>$data];
    }

    public function actionUsers() {
        $params=json_decode($_REQUEST['urlState'],true);
        return $this->getUsers(
            $params['users']
        );
    }

    public function actionIndex() {
        $params=json_decode($_REQUEST['urlState'],true);
        $params['users']['pageCount']=$params['users']['pageNum'];
        $params['users']['pageNum']=1;
		if( Yii::$app->user->identity->country_id ){
			$params['users']['country_ids']= array(Yii::$app->user->identity->country_id);
		}
        return array_merge(
            [
                'sexes'=>User::getSexList(),
                'countryArrayUserSearch'=>User::getCountryCountList()
            ],
            $this->getUsers($params['users'])
        );
    }


	public function actionNewUsersRequest() {
        $params=json_decode($_REQUEST['urlState'],true);
        $countries=json_decode($_REQUEST['country_ids'],true);
		$pageNum=$params['users']['pageNum'];
		
		$perPage=20;	
        if($countries!=null && !empty($countries) ){
			$query=User::find()
			->where(['in','country_id',$countries])
            ->andWhere(['status'=>User::STATUS_ACTIVE])
            ->andWhere('registration_dt >= CURDATE()')
            ->orderBy(['dt_status_active'=>SORT_DESC])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);
		}
		else{
			$query=User::find()
            ->where(['status'=>User::STATUS_ACTIVE])
            ->andWhere('registration_dt >= CURDATE()')
            ->orderBy(['dt_status_active'=>SORT_DESC])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);
		}
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
            'users'=>$data,
            'hasMore'=>$hasMore
            
        ];
    }
    public function actionNewUsers($pageNum=1) {
		
		$perPage=20;

		/*if( Yii::$app->user->identity->country_id!=null && !empty( Yii::$app->user->identity->country_id) ){
			$query=User::find()
            ->where(['status'=>User::STATUS_ACTIVE])
            ->andWhere('registration_dt >= CURDATE()')
            ->andWhere(['country_id'=> Yii::$app->user->identity->country_id])
            ->orderBy(['dt_status_active'=>SORT_DESC])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);
		}
		else{*/
			$query=User::find()
            ->where(['status'=>User::STATUS_ACTIVE])
            ->andWhere('registration_dt >= CURDATE()')
            ->orderBy(['dt_status_active'=>SORT_DESC])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);
		/*}*/
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
			'currentCountry'=>$this->currentCountry(),
            'users'=>$data,
            'hasMore'=>$hasMore
            
        ];

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
