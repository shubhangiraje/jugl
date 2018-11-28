<?php

namespace app\controllers;

use Yii;
use app\components\EDateTime;
use app\models\UserFollowerEvent;


class ExtApiFollowerEventController extends \app\components\ExtApiController {

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
        foreach(array_slice($eventItems,0,$perPage) as $item) {
            $data[]=[
                'id'=>$item->id,
                'dt'=>(new EDateTime($item->dt))->js(),
                'type'=>$item->type,
                'text'=>$item->text,
                'user'=>!$item->user_id ? \app\models\User::getAdministrationUser()->getShortData():$item->user->getShortData()
            ];
        }

        return [
                'items'=>$data,
                'hasMore'=>$hasMore
        ];
    }

    public function actionLog($pageNum,$type) {
        return $this->getLog($pageNum,$type);
    }
}
