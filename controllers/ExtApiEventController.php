<?php

namespace app\controllers;

use Yii;
use app\components\EDateTime;
use app\models\UserEvent;


class ExtApiEventController extends \app\components\ExtApiController {

    public function getLog($pageNum=1, $type=null) {
        Yii::$app->user->identity->resetNewEventsCount();

        $perPage=50;

        $eventQuery=UserEvent::find()->andWhere(['user_id'=>Yii::$app->user->id])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1)
            ->with(['secondUser','secondUser.avatarFile'])
            ->orderBy('id desc');

        if ($type) {
            switch ($type) {
                case UserEvent::TYPE_FRIEND_REQUEST:
                    $eventQuery->andWhere(['type'=>[UserEvent::TYPE_FRIEND_REQUEST,UserEvent::TYPE_FRIEND_REQUEST_ACCEPTED]]);
                    break;
                case UserEvent::SELECT_WAS_WIRD_MIR_ANGEBOTEN:
                    $eventQuery->andWhere(['type'=>[UserEvent::TYPE_SEARCH_REQUEST_OFFER_NEW]]);
                    break;
                case UserEvent::SELECT_AKZEPTIERTES_ANGEBOT:
                    $eventQuery->andWhere(['type'=>[UserEvent::TYPE_SEARCH_REQUEST_OFFER_ACCEPTED]]);
                    break;
                case UserEvent::SELECT_ICH_WURDE_BEWERTET:
                    $eventQuery->andWhere(['type'=>[UserEvent::TYPE_SEARCH_REQUEST_OFFER_FEEDBACK,UserEvent::TYPE_OFFER_REQUEST_FEEDBACK]]);
                    break;
                case UserEvent::TYPE_SEARCH_REQUEST_MY_OFFER:
                    $eventQuery->andWhere(['type'=>[UserEvent::TYPE_SEARCH_REQUEST_MY_OFFER]]);
                    break;
                case UserEvent::TYPE_OFFER_MY_REQUEST:
                    $eventQuery->andWhere(['type'=>[UserEvent::TYPE_OFFER_MY_REQUEST]]);
                    break;
                case UserEvent::SELECT_MY_FEEDBACKS:
                    $eventQuery->andWhere(['type'=>[UserEvent::TYPE_SEARCH_REQUEST_OFFER_MY_FEEDBACK,UserEvent::TYPE_OFFER_REQUEST_MY_FEEDBACK]]);
                    break;
                case UserEvent::SELECT_OFFER_REQUEST_SOLD:
                    $eventQuery->andWhere(['type'=>[UserEvent::TYPE_OFFER_REQUEST_NEW]]);
                    break;
                default:
                    $eventQuery->andWhere(['type'=>$type]);
                    break;
            }
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
                'user'=>!$item->second_user_id ? \app\models\User::getAdministrationUser()->getShortData():$item->secondUser->getShortData()
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
