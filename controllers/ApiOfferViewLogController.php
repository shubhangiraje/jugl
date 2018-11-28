<?php

namespace app\controllers;

use app\components\EDateTime;
use app\models\OfferViewLog;
use app\models\User;
use Yii;


class ApiOfferViewLogController extends \app\components\ApiController {

    public function actionEndView($id) {
        $model = OfferViewLog::findOne($id);
        $now = new EDateTime();
        $model->duration = strtotime($now->sqlDateTime()) - strtotime($model->create_dt);
        $model->save();

        return [
            'result'=>true
        ];
    }


    public function actionUpdate($id) {
        $model = OfferViewLog::findOne($id);
        $now = new EDateTime();
        $model->duration = strtotime($now->sqlDateTime()) - strtotime($model->create_dt);
        $model->save();

        return [
            'count_offer_view'=>$model->offer->count_offer_view
        ];
    }


    public function actionGetUsers($offer_id) {
        $offerViewLogs = OfferViewLog::find()
            ->select(['user_id','dt'=>'max(create_dt)'])
            ->where(['offer_id'=>$offer_id])
            ->with(['user'])
            ->groupBy('user_id')
            ->orderBy(['dt'=>SORT_DESC])
            ->all();

        $data = [];
        foreach ($offerViewLogs as $item) {
            $data[] = [
                'id'=>$item->user->id,
                'avatarSmall'=>$item->user->getAvatarThumbUrl('avatarSmall')
            ];
        }

        $count_users = count($data);

        return [
            'users'=>$count_users>0 ? $data : false,
            'count_users'=>$count_users
        ];

    }

    public function actionHistory($user_id, $offer_id, $pageNum=1) {
        $perPage=30;

        $query = OfferViewLog::find()
            ->where(['user_id'=>$user_id, 'offer_id'=>$offer_id])
            ->orderBy(['create_dt'=>SORT_DESC])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $news=$query->all();
        $hasMore=count($news)>$perPage;

        $data=[];
        foreach(array_slice($news,0,$perPage) as $item) {
            $idata['create_dt']=(new EDateTime($item->create_dt))->js();
            $idata['duration']=$item->duration;
            $data[] = $idata;
        }

        $user = User::findOne($user_id);

        return [
            'log'=>[
                'items'=>$data,
                'hasMore'=>$hasMore,
                'user'=>$user->getShortData(['city'])
            ]
        ];
    }



}