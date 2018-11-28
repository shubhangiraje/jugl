<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Country;

class ExtApiInviteMyNewController extends \app\components\ExtApiController  {

    private function getUsersInviteMy($pageNum=1,$country_ids) {
        $perPage=20;

        $time = (new \app\components\EDateTime())->modify("-" . \app\models\Setting::get('TEAM_CHANGE_PERIOD_DAYS') . " minute");

        $query=User::find()
            ->where(['status'=>User::STATUS_ACTIVE,'show_in_become_member'=>1])
            ->andWhere(['!=', 'id', Yii::$app->user->identity->getId()])
            ->andWhere('(registration_dt>:time)',[':time'=>$time->sql()])
			->andWhere(array('in','country_id',explode(',',$country_ids)))
            ->orderBy(['id'=>SORT_DESC])
            ->with(['invitationWinner','invitation'])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $users=$query->all();
        $hasMore=count($users)>$perPage;

        $data=[];
        $items=array_slice($users,0,$perPage);
        foreach($items as $item) {
            $itemData=$item->toArray(['id','first_name','last_name','email','phone','is_company_name','company_name', 'country_id']);
	
			/* NVII-MEDIA - Output Flag */
			$flagAry = Country::getListShort();
			$flag = $flagAry[$itemData['country_id']];
			/* NVII-MEDIA - Output Flag */
			$itemData['flag'] = $flag;
            if ($item->invitationWinner/* && $item->invitation*/) {
                $itemData['winner']=[
                    'user_id'=>$item->invitationWinner->user_id,
                    'userName'=>$item->invitationWinner->secondUser->name,
                    'dt'=>(new \app\components\EDateTime($item->invitationWinner->dt))->js(),
                    'ms'=>$item->invitationWinner->getFormattedMs(),
                ];
            }

            $data[]=$itemData;
        }

        $ids=\yii\helpers\ArrayHelper::getColumn($data,'id');
        if (!empty($ids)) {
            $counts = Yii::$app->db->createCommand("select user_id,count(*) as cnt from user_become_member_invitation where user_id in (" . implode(',', $ids) . ") group by user_id")->queryAll();
            $counts = \yii\helpers\ArrayHelper::index($counts, 'user_id');
            foreach($data as &$r) {
                if ($counts[$r['id']] && $r['winner']) {
                    $r['winner']['count']=$counts[$r['id']]['cnt'];
                }
            }
        }
		return [
			
            'results'=>[
				'currentCountry'=> $this->currentCountry(),
                'items'=>$data,
                'hasMore'=>$hasMore
            ]
        ];
        
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
	

    public function actionList($pageNum=1,$country_ids=false) {
        return $this->getUsersInviteMy($pageNum,$country_ids);
    }


}