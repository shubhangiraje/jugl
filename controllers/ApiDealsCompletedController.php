<?php

namespace app\controllers;

use Yii;
use app\models\SearchRequest;
use app\models\SearchRequestOffer;
use app\models\Offer;
use app\models\OfferRequest;
use yii\web\NotFoundHttpException;
use app\components\EDateTime;
use yii\db\Query;


class ApiDealsCompletedController extends \app\components\ApiController {

    private function search($filter=[],$pageNum=1) {
        $perPage=50;

        // get union subrequest request
        $squery=(new Query)->select(['type'=>"('search_request')",'payment_complaint'=>"(0)",'search_request.id','search_request.closed_dt','search_request.status'])
            ->from('search_request')
            ->leftJoin('search_request_offer','search_request_offer.search_request_id=search_request.id and search_request_offer.status=:status_accepted and (:show_only_without_feedbacks=0 or search_request_offer.user_feedback_id is null)',[':status_accepted'=>SearchRequestOffer::STATUS_ACCEPTED])
            ->where(['search_request.user_id'=>Yii::$app->user->id])
            ->andWhere('search_request_offer.id is not null or (:show_only_without_feedbacks=0 and search_request.status=:status_deleted)',[':status_deleted'=>SearchRequestOffer::STATUS_DELETED])
            ->addGroupBy(['type','payment_complaint','search_request.id','search_request.status'])
            ->union(
                (new Query)->select(['type'=>"('search_request_offer')",'payment_complaint'=>"(0)",'search_request_offer.id','search_request_offer.closed_dt','status'=>"(0)"])
                    ->from('search_request_offer')
                    ->innerJoin('search_request','search_request.id=search_request_offer.search_request_id and (:show_only_without_feedbacks=0 or search_request_offer.counter_user_feedback_id is null)')
                    ->where(['search_request_offer.status'=>SearchRequestOffer::STATUS_ACCEPTED,'search_request_offer.user_id'=>Yii::$app->user->id])
            ,true)
            ->union(
                (new Query)->select(['type'=>"('offer')",'offer_request.payment_complaint','offer.id','closed_dt'=>"IF(offer.status='DELETED',offer.closed_dt,max(offer_request.closed_dt))",'offer.status'])
                    ->from('offer')
                    ->leftJoin('offer_request','offer_request.offer_id=offer.id and offer_request.status=:status_accepted and offer_request.pay_status=:pay_status_confirmed and (:show_only_without_feedbacks=0 or offer_request.user_feedback_id is null)',[':status_accepted'=>OfferRequest::STATUS_ACCEPTED,':pay_status_confirmed'=>OfferRequest::PAY_STATUS_CONFIRMED])
                    ->where(['offer.user_id'=>Yii::$app->user->id])
                    ->andWhere('offer_request.id is not null or (:show_only_without_feedbacks=0 and offer.status=:status_deleted)',[':status_deleted'=>Offer::STATUS_DELETED])
                    ->addGroupBy(['type','offer_request.payment_complaint','offer.id','offer.status'])
            ,true)
            ->union(
                (new Query)->select(['type'=>"('offer_request')",'offer_request.payment_complaint','offer_request.id','offer_request.closed_dt','status'=>"(0)"])
                    ->from('offer_request')
                    ->innerJoin('offer','offer.id=offer_request.offer_id and (:show_only_without_feedbacks=0 or offer_request.counter_user_feedback_id is null)')
                    ->where(['offer_request.status'=>OfferRequest::STATUS_ACCEPTED,'offer_request.pay_status'=>OfferRequest::PAY_STATUS_CONFIRMED,'offer_request.user_id'=>Yii::$app->user->id])
            ,true);


        // build main request with filtering and sorting
        $query=(new Query)
            ->from(['items'=>$squery])
            ->orderBy('closed_dt desc')
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $filters=explode('|',$filter['type']);

        $query->andFilterWhere([
            'type'=>$filters[0],
            'payment_complaint'=>$filters[1],
            'status'=>$filters[2]
        ]);

        $query->addParams(['show_only_without_feedbacks'=>$filters[3] ? 1:0]);

        $deals=$query->all();
        $hasMore=count($deals)>$perPage;
        $deals=array_slice($deals,0,$perPage);

        $idsByTypes=[];
        foreach($deals as $deal) {
            $idsByTypes[$deal['type']][]=$deal['id'];
        }

        $valsByTypesAndIds=[];

        // get data for items
        $valsByTypesAndIds['search_request']=$this->getSearchRequestData($idsByTypes['search_request']);
        $valsByTypesAndIds['search_request_offer']=$this->getSearchRequestOfferData($idsByTypes['search_request_offer']);
        $valsByTypesAndIds['offer']=$this->getOfferData($idsByTypes['offer']);
        $valsByTypesAndIds['offer_request']=$this->getOfferRequestData($idsByTypes['offer_request']);

        // build final data array
        $data=[];
        foreach($deals as $deal) {
            if (isset($valsByTypesAndIds[$deal['type']][$deal['id']])) {
                $dealData=$valsByTypesAndIds[$deal['type']][$deal['id']];
                $dealData['type']=$deal['type'];
                $data[]=$dealData;
            }
        }

        return [
            'results'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ]
        ];

    }

    private function getDealData($model,$interests) {
        if (!$model) return [];
        $data=$model->toArray(['id','title','price_from','price_to','price','delivery_days','view_bonus','buy_bonus','user_feedback_id','counter_user_feedback_id','status']);

        $data['create_dt']=(new EDateTime($model->create_dt))->js();
        $data['user']=$model->user->getShortData(['rating', 'feedback_count', 'packet']);

        if (count($interests)>0) {
            $data['level1Interest']=strval($interests[0]->level1Interest);
            $data['level2Interest']=strval($interests[0]->level2Interest);

            $level3Interests=[];
            foreach($interests as $sri) {
                $level3Interests[]=$sri->level3Interest;
            }
            $data['level3Interests']=implode(', ',$level3Interests);
        }

        if (count($model->files)>0) {
            $data['image']=$model->files[0]->getThumbUrl('searchRequest');
        } else {
            $data['image']=\app\components\Thumb::createUrl('/static/images/account/default_interest.png','offer');
        }

        return $data;
    }

    private function getDealOfferData($model) {
        if (!$model) return [];
        $data=$model->toArray(['id','relevancy','price_from','price_to']);
        $data['description']=\yii\helpers\StringHelper::truncate($model->description,80);
        $data['user']=$model->user->getShortData(['rating', 'feedback_count', 'packet']);
        $data['closed_dt']=(new EDateTime($model->closed_dt))->js();
        return $data;
    }

    private function getSearchRequestData($ids) {
        if (!is_array($ids) || empty($ids)) return;

        $models=SearchRequest::find()->where('status!=:status_unlinked',[':status_unlinked'=>SearchRequest::STATUS_UNLINKED])
            ->andWhere(['id'=>$ids])->with([
                'user',
                'user.avatarFile',
                'searchRequestInterests',
                'searchRequestInterests.level1Interest',
                'searchRequestInterests.level2Interest',
                'searchRequestInterests.level3Interest',
                'files',
                'acceptedSearchRequestOffers',
                'acceptedSearchRequestOffers.user',
                'acceptedSearchRequestOffers.user.avatarFile',
                'acceptedSearchRequestOffers.userFeedback',
                'acceptedSearchRequestOffers.counterUserFeedback',
            ])
            ->all();

        $data=[];

        foreach($models as $model) {
            $idata=[
                'id'=>$model->id,
                'deal'=>$this->getDealData($model,$model->searchRequestInterests),
                'dealOffers'=>[]
            ];

            foreach($model->acceptedSearchRequestOffers as $offer) {
                $idata['dealOffers'][]=array_merge($this->getDealOfferData($offer),[
                    'rating'=>$offer->userFeedback->rating,
                    'counter_rating'=>$offer->counterUserFeedback ? $offer->counterUserFeedback->rating:null
                ]);
            }

            $data[$idata['id']]=$idata;
        }

        return $data;
    }

    private function getSearchRequestOfferData($ids) {
        if (!is_array($ids) || empty($ids)) return;

        $models=SearchRequestOffer::find()->andWhere(['id'=>$ids])->with([
            'searchRequest.user',
            'searchRequest.user.avatarFile',
            'searchRequest.searchRequestInterests',
            'searchRequest.searchRequestInterests.level1Interest',
            'searchRequest.searchRequestInterests.level2Interest',
            'searchRequest.searchRequestInterests.level3Interest',
            'searchRequest.files',
            'user',
            'user.avatarFile',
            'userFeedback',
            'counterUserFeedback'
        ])
            ->all();

        $data=[];

        foreach($models as $model) {
            $idata=[
                'id'=>$model->id,
                'deal'=>$this->getDealData($model->searchRequest,$model->searchRequest->searchRequestInterests),
                'dealOffer'=>array_merge($this->getDealOfferData($model),[
                    'rating'=>$model->userFeedback->rating,
                    'counter_rating'=>$model->counterUserFeedback ? $model->counterUserFeedback->rating:null
                ])
            ];

            $data[$idata['id']]=$idata;
        }

        return $data;
    }

    private function getOfferData($ids) {
        if (!is_array($ids) || empty($ids)) return;

        $models=Offer::find()->where('status!=:status_unlinked',[':status_unlinked'=>Offer::STATUS_UNLINKED])
            ->andWhere(['id'=>$ids])->with([
            'user',
            'user.avatarFile',
            'offerInterests',
            'offerInterests.level1Interest',
            'offerInterests.level2Interest',
            'offerInterests.level3Interest',
            'files',
            'acceptedOfferRequest',
            'acceptedOfferRequest.user',
            'acceptedOfferRequest.user.avatarFile',
            'acceptedOfferRequest.userFeedback',
            'acceptedOfferRequest.counterUserFeedback',
        ])
            ->all();

        $data=[];

        foreach($models as $model) {
            $idata=[
                'id'=>$model->id,
                'deal'=>$this->getDealData($model,$model->offerInterests),
                'dealOffers'=>[]//array_merge($this->getDealOfferData($model->acceptedOfferRequest),['rating'=>$model->userFeedback->rating])
            ];

            foreach($model->acceptedOfferRequests as $request) {
                $idata['dealOffers'][]=array_merge($this->getDealOfferData($request),[
                    'rating'=>$request->userFeedback ? $request->userFeedback->rating:null,
                    'counter_rating'=>$request->counterUserFeedback ? $request->counterUserFeedback->rating:null
                ]);
            }

            $data[$idata['id']]=$idata;
        }

        return $data;
    }

    private function getOfferRequestData($ids) {
        if (!is_array($ids) || empty($ids)) return;

        $models=OfferRequest::find()->andWhere(['id'=>$ids])->with([
            'offer.user',
            'offer.user.avatarFile',
            'offer.offerInterests',
            'offer.offerInterests.level1Interest',
            'offer.offerInterests.level2Interest',
            'offer.offerInterests.level3Interest',
            'offer.files',
            'userFeedback',
            'counterUserFeedback',
            'user',
            'user.avatarFile',
        ])
            ->all();

        $data=[];

        foreach($models as $model) {
            $idata=[
                'id'=>$model->id,
                'deal'=>$this->getDealData($model->offer,$model->offer->offerInterests),
                'dealOffer'=>array_merge($this->getDealOfferData($model),[
                    'rating'=>$model->userFeedback->rating,
                    'counter_rating'=>$model->counterUserFeedback ? $model->counterUserFeedback->rating:null
                ])
            ];

            $data[$idata['id']]=$idata;
        }

        return $data;
    }

    public function actionSearch($filter,$pageNum) {
        return $this->search(json_decode($filter,true),$pageNum);
    }

    public function actionIndex() {
        return $this->search();
    }
}
