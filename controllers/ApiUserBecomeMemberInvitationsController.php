<?php

namespace app\controllers;


use Yii;
use app\components\EDateTime;
use app\models\UserBecomeMemberInvitation;

class ApiUserBecomeMemberInvitationsController extends \app\components\ApiController {

    private function getUsers($id) {

        $query = UserBecomeMemberInvitation::find()
            ->with(['secondUser'])
            ->where(['user_id'=>$id])
            ->orderBy(['dt'=>SORT_ASC,'ms'=>SORT_ASC]);

        $usersBecomeMemberInvitation = $query->all();
        $data = [];
        foreach ($usersBecomeMemberInvitation as $item) {
            $data[] = [
                'user'=>$item->secondUser->getShortData(['rating', 'feedback_count', 'packet']),
                'ms'=>$item->getFormattedMs(),
                'dt'=>(new EDateTime($item->dt))->js()
            ];
        }

        return [
            'items'=>$data
        ];

    }

    public function actionList($id) {
        return $this->getUsers($id);
    }


}