<?php

namespace app\controllers;


use Yii;
use app\components\EDateTime;
use app\models\UserFollowerEvent;
use app\models\Country;

class ApiFollowerEventLogController extends \app\components\ApiController {

    public function getLog($pageNum=1, $type=null) {
        Yii::$app->user->identity->resetNewFollowerEventsCount();

        $perPage=50;

        $eventQuery=UserFollowerEvent::find()->andWhere(['follower_user_id'=>Yii::$app->user->id])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1)
            ->with(['user','user.avatarFile'])
            ->orderBy('id desc');

        if ($type) {
            $eventQuery->andWhere(['type'=>$type]);
        }

        $eventItems=$eventQuery->all();
        $hasMore=count($eventItems)>$perPage;

        $data=[];
        foreach(array_slice($eventItems,0,$perPage) as $itemKey => $itemVal) {
            $data[]=[
                'id'=>$itemVal->id,
                'dt'=>(new EDateTime($itemVal->dt))->js(),
                'type'=>$itemVal->type,
                'text'=>$itemVal->text,
                'user'=>!$itemVal->user_id ? \app\models\User::getAdministrationUser()->getShortData(['rating', 'feedback_count', 'packet', 'country_id']):$itemVal->user->getShortData(['rating', 'feedback_count', 'packet', 'country_id'])
            ];
			/* NVII-MEDIA - Output Flag */
			$flagAry = Country::getListShort();
			$data[$itemKey]['user']['flag'] = $flagAry[$data[$itemKey]['user']['country_id']];
			/* NVII-MEDIA - Output Flag */

        }

        return [
            'log'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ]
        ];
    }

    public function actionLog($pageNum,$type) {
        return $this->getLog($pageNum,$type);
    }

    public function actionIndex() {
        Yii::$app->user->identity->new_network_members=0;
        Yii::$app->user->identity->save();

        return $this->getLog();
    }
}
