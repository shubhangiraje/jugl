<?php

namespace app\controllers;

use app\models\SearchRequestOffer;
use Yii;
use app\components\EDateTime;
use app\models\SearchRequest;
use yii\db\Query;


class ExtApiSearchRequestMyOfferController extends \app\components\ExtApiController {

    private function requestOffers($filter=[],$pageNum=1) {
        $perPage=10;

        $addConditions=['user_id'=>Yii::$app->user->id];
        switch ($filter['status']) {
            case 'ACCEPTED':
                $addConditions['status']=[SearchRequestOffer::STATUS_ACCEPTED];
                break;
            case 'REJECTED':
                $addConditions['status']=[SearchRequestOffer::STATUS_REJECTED,SearchRequestOffer::STATUS_DELETED];
                break;
            case 'AWAITING':
                $addConditions['status']=[SearchRequestOffer::STATUS_NEW,SearchRequestOffer::STATUS_CONTACTED];
                break;
            default:
        }

        $squery=new Query;

        $squery->select(['search_request.id',
            'relevancy'=>
            // level 3 relevancy
                '33.33*('.
                // count of matched level3 interests
                'SUM(IF(user_interest.level3_interest_id=search_request_interest.level3_interest_id,1,0))+'.
                // or 1 if level1 & level2 interests matches
                'MAX(IF(search_request_interest.level3_interest_id is null and (search_request_interest.level2_interest_id=user_interest.level2_interest_id or search_request_interest.level2_interest_id is null) and search_request_interest.level1_interest_id=user_interest.level1_interest_id ,1,0))'.
                ')/'.
                // count of level3 interests in search request or 1
                'COALESCE(NULLIF(COUNT(DISTINCT search_request_interest.level3_interest_id),0),1)+'.
                // level 2 relevancy
                '33.33*MAX(IF(user_interest.level2_interest_id=search_request_interest.level2_interest_id or (search_request_interest.level2_interest_id is null and user_interest.level1_interest_id=search_request_interest.level1_interest_id),1,0))+'.
                // level 1 relevancy
                '33.33*MAX(IF(user_interest.level1_interest_id=search_request_interest.level1_interest_id,1,0))'
        ])
            ->from('search_request')
            ->where('search_request.status!=:status_deleted and search_request.status!=:status_unlinked',[':status_deleted'=>SearchRequest::STATUS_DELETED,':status_unlinked'=>SearchRequest::STATUS_UNLINKED])
            ->innerJoin([
                'user_filter'=>(new Query)->select(['search_request_id','dt'=>'MAX(COALESCE(closed_dt,create_dt))'])
                    ->from('search_request_offer')->where($addConditions)->groupBy('search_request_id')
            ],'user_filter.search_request_id=search_request.id')
            ->innerJoin('search_request_interest','search_request_interest.search_request_id=search_request.id')
            ->innerJoin('user','user.id=search_request.user_id')
            ->leftJoin('user_interest','user_interest.user_id=:user_id and (
                user_interest.level3_interest_id=search_request_interest.level3_interest_id or
                user_interest.level2_interest_id=search_request_interest.level2_interest_id or
                user_interest.level1_interest_id=search_request_interest.level1_interest_id
                )',[':user_id'=>Yii::$app->user->id])
            ->groupBy('search_request.id')
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $squery->orderBy('user_filter.dt desc');

        $rows=$squery->all();
        $hasMore=count($rows)>$perPage;
        $rows=array_slice($rows,0,$perPage);

        $unsortedModels=SearchRequest::find()->andWhere(['id'=>\yii\helpers\ArrayHelper::getColumn($rows,'id')])->with([
            'user',
            'user.avatarFile',
            'searchRequestInterests',
            'searchRequestInterests.level1Interest',
            'searchRequestInterests.level2Interest',
            'searchRequestInterests.level3Interest',
            'files',
            'searchRequestMyOffers'
        ])->indexBy('id')->all();

        $data=[];
        foreach($rows as $row) {
            $model=$unsortedModels[$row['id']];
            $idata=$model->toArray(['id','title','price_from','price_to','bonus','zip','city','address','relevancy']);
            $idata['description']=\yii\helpers\StringHelper::truncate($model->description,100);
            $idata['create_dt']=(new EDateTime($model->create_dt))->js();
            $idata['relevancy']=floor(0.5+$row['relevancy']);
            $idata['user']=$model->user->getShortData(['rating', 'feedback_count', 'packet']);

            $idata['favorite']=count($model->searchRequestMyFavorites)>0;

            $idata['offers']=[];
            foreach($model->searchRequestMyOffers as $offer) {
                $item=$offer->toArray(['id','description','price_from','price_to','status','closed_dt']);
                $item['closed_dt']=(new EDateTime($offer->closed_dt))->js();
                $idata['offers'][]=$item;
            }
            
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
                $idata['image']=$model->files[0]->getThumbUrl('searchRequestMobile');
            } else {
                $idata['image']=\app\components\Thumb::createUrl('/static/images/account/default_interest.png','searchRequestMobile',true);
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
        return $this->requestOffers(json_decode($filter,true),$pageNum);
    }

    public function actionIndex() {
        return array_merge([],$this->requestOffers());
    }
}
