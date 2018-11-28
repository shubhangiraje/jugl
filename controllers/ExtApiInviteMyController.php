<?php

namespace app\controllers;

use Yii;
use app\models\User;

class ExtApiInviteMyController extends \app\components\ExtApiController  {

    private function getUsersInviteMy($pageNum=1) {
        $perPage=30;

        $time=(new \app\components\EDateTime())->modify("-".\app\models\Setting::get('TEAM_CHANGE_PERIOD_DAYS')." minute");

        $query=User::find()
            ->where(['status'=>User::STATUS_ACTIVE,'show_in_become_member'=>1])
            ->andWhere(['!=', 'id', Yii::$app->user->identity->getId()])
            ->andWhere('(registration_dt>:time or parent_id is null)',[':time'=>$time->sql()])
            ->orderBy(['id'=>SORT_DESC])
            ->with(['invitationWinner','invitation'])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $users=$query->all();
        $hasMore=count($users)>$perPage;

        $data=[];
        $items=array_slice($users,0,$perPage);
        foreach($items as $item) {
            $itemData=$item->toArray(['id','first_name','last_name','email','phone','is_company_name','company_name']);
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
                'items'=>$data,
                'hasMore'=>$hasMore
            ]
        ];
    }

    public function actionList($pageNum) {
        return $this->getUsersInviteMy($pageNum);
    }


}