<?php

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use app\models\SearchRequest;
use app\models\SearchRequestOffer;
use app\models\Interest;
use app\components\EDateTime;


class ApiSearchRequestMyListController extends \app\components\ApiController {

    private function searchRequests($filter=[],$pageNum=1) {
        $perPage=10;

        $query=SearchRequest::find()->andWhere(['status'=>[SearchRequest::STATUS_ACTIVE,SearchRequest::STATUS_AWAITING_VALIDATION,SearchRequest::STATUS_REJECTED,SearchRequest::STATUS_SCHEDULED]])->andWhere('active_till>=CAST(NOW() as DATE)')->andWhere(['user_id'=>Yii::$app->user->id])->with([
                'searchRequestInterests',
                'searchRequestInterests.level1Interest',
                'searchRequestInterests.level2Interest',
                'searchRequestInterests.level3Interest',
                'files',
                'searchRequestActiveOffers',
                'searchRequestActiveOffers.user',
                'searchRequestActiveOffers.user.avatarFile',
            ])
            ->orderBy(['create_dt'=>SORT_DESC])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $models=$query->all();
        $hasMore=count($models)>$perPage;

        $data=[];
        foreach(array_slice($models,0,$perPage) as $model) {
            $idata=$model->toArray(['id','title','price_from','price_to','bonus','status']);
            $idata['create_dt']=(new EDateTime($model->create_dt))->js();

            if (count($model->searchRequestInterests)>0) {
                $idata['level1Interest']=strval($model->searchRequestInterests[0]->level1Interest);
                $idata['level2Interest']=strval($model->searchRequestInterests[0]->level2Interest);

                $level3Interests=[];
                foreach($model->searchRequestInterests as $sri) {
                    $level3Interests[]=$sri->level3Interest;
                }
                $idata['level3Interests']=implode(', ',$level3Interests);
            }

            if (count($model->files)>0) {
                $idata['image']=$model->files[0]->getThumbUrl('searchRequest');
            } else {
                $idata['image']=\app\components\Thumb::createUrl('/static/images/account/default_interest.png','searchRequest');
            }

            $idata['searchRequestOffers']=[];
            foreach($model->searchRequestActiveOffers as $offer) {
                $odata=$offer->toArray(['id','relevancy','price_from','price_to','status']);
                $odata['description']=\yii\helpers\StringHelper::truncate($offer->description,80);
                $odata['user']=$offer->user->getShortData(['rating', 'feedback_count', 'packet']);
                $idata['searchRequestOffers'][]=$odata;
            }

            $data[]=$idata;
        }

        return [
            'results'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ]
        ];
    }

    public function actionList($filter,$pageNum) {
        return $this->searchRequests(json_decode($filter,true),$pageNum);
    }

    private function initialData() {
        $data=[];

        $data['interests']=[];
        foreach(Interest::find()->orderBy('sort_order asc')->all() as $interest) {
            $data['interests'][]=$interest->toArray(['id','parent_id','title']);
        }

        return $data;
    }

    public function actionIndex() {
        Yii::$app->user->identity->viewedSearchRequestsOffers();
        return array_merge($this->initialData(),$this->searchRequests());
    }
}
