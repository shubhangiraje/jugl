<?php

namespace app\controllers;


use Yii;
use app\components\EDateTime;
use app\models\UserBecomeMemberInvitation;

class ExtApiUserBecomeMemberInvitationsController extends \app\components\ExtApiController {

    private function getUsers($id,$pageNum=1) {
        $perPage=20;

        $query = UserBecomeMemberInvitation::find()
            ->with(['secondUser'])
            ->where(['user_id'=>$id])
            ->orderBy(['dt'=>SORT_ASC,'ms'=>SORT_ASC])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $usersBecomeMemberInvitation = $query->all();
        $hasMore=count($usersBecomeMemberInvitation)>$perPage;
        $data = [];
        foreach (array_slice($usersBecomeMemberInvitation,0,$perPage) as $item) {
            $data[] = [
                'user'=>$item->secondUser->getShortData(['rating', 'feedback_count', 'packet']),
                'ms'=>$item->getFormattedMs(),
                'dt'=>(new EDateTime($item->dt))->js()
            ];
        }

        return [
            'items'=>$data,
            'hasMore'=>$hasMore
        ];

    }

    public function actionList($id,$pageNum) {
        return $this->getUsers($id,$pageNum);
    }


}