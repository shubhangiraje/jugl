<?php

namespace app\controllers;

use app\models\InviteMe;
use Yii;
use app\models\User;
use app\models\Country;

class ApiInviteMyController extends \app\components\ApiController  {

    private function getUsersInviteMy($country_ids,$pageNum=1) {
        $perPage=20;

        $time = (new \app\components\EDateTime())->modify("-" . \app\models\Setting::get('TEAM_CHANGE_PERIOD_DAYS') . " minute");

        $query=User::find()
            ->where(['status'=>User::STATUS_ACTIVE,'show_in_become_member'=>1])
            ->andWhere(['!=', 'id', Yii::$app->user->identity->getId()])
            ->andWhere('(registration_dt>:time)',[':time'=>$time->sql()]);

        if($country_ids) {
            $query->andWhere(array('in','country_id',explode(',',$country_ids)));
        }

        $users = $query->orderBy(['id'=>SORT_DESC])
            ->with(['invitationWinner','invitation'])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1)
            ->all();

        $hasMore=count($users)>$perPage;

        $data=[];
        $items=array_slice($users,0,$perPage);
        foreach($items as $item) {
            $itemData=$item->toArray(['id','first_name','last_name','email','phone','is_company_name','company_name', 'country_id']);
			$itemData['flag'] = $item->getFlag();

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
			'log'=>[
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

	public function actionIndex() {
        $currentCountry = [];
        $countryList = InviteMe::getCountryList();
        foreach ($countryList as $itemCountry) {
            if($itemCountry['id']==Yii::$app->user->identity->country_id) {
                $currentCountry[] = $itemCountry;
                break;
            }
        }

        return array_merge($this->getUsersInviteMy(Yii::$app->user->identity->country_id), [
            'countryList'=>InviteMe::getCountryList(),
            'currentCountry'=>$currentCountry
        ]);
    }

    public function actionList($country_ids, $pageNum=1) {
        return $this->getUsersInviteMy($country_ids, $pageNum);
    }



}