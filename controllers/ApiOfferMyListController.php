<?php

namespace app\controllers;

use app\models\OfferRequest;
use app\models\OfferView;
use Yii;
use yii\web\NotFoundHttpException;
use app\models\Offer;
use app\models\OfferOffer;
use app\models\Interest;
use app\components\EDateTime;


class ApiOfferMyListController extends \app\components\ApiController {

    private function offers($filter=[],$pageNum=1) {
        $perPage=50;

        $query=Offer::find()
            ->andWhere(['offer.status'=>[Offer::STATUS_ACTIVE, Offer::STATUS_EXPIRED, Offer::STATUS_PAUSED, Offer::STATUS_AWAITING_VALIDATION, Offer::STATUS_REJECTED, Offer::STATUS_SCHEDULED]])
            ->andWhere(['offer.user_id'=>Yii::$app->user->id])
            ->with([
                'offerInterests',
                'offerInterests.level1Interest',
                'offerInterests.level2Interest',
                'offerInterests.level3Interest',
                'files',
                'offerActiveRequests',
                'offerActiveRequests.offerRequestModifications',
                'offerActiveRequests.user',
                'offerActiveRequests.user.avatarFile',
            ])
            ->orderBy(['create_dt'=>SORT_DESC])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);


        switch ($filter['status']) {
            case 'ACTIVE':
                //$query->andWhere('active_till>=CAST(NOW() AS DATE)');
                $query->andWhere('offer.status=:status_active', ['status_active'=>Offer::STATUS_ACTIVE ]);
                break;
            case 'EXPIRED':
                //$query->andWhere('active_till<CAST(NOW() AS DATE)');
                $query->andWhere('offer.status=:status_expired', ['status_expired'=>Offer::STATUS_EXPIRED ]);
                break;
            case 'PAUSED':
                //$query->andWhere('active_till<CAST(NOW() AS DATE)');
                $query->andWhere('offer.status=:status_expired', ['status_expired'=>Offer::STATUS_PAUSED ]);
                break;
            case 'REQUEST_NEW':
                $columns=[];

                foreach(Offer::getTableSchema()->columnNames as $column) {
                    $columns[]=Offer::getDb()->quoteTableName(Offer::getTableSchema()->name).'.'.Offer::getDb()->quoteColumnName($column);
                }
                $selectColumns=$columns;
                $selectColumns['max_bet_dt']='MAX(offer_request.bet_dt)';
                $query->select($selectColumns);

                //$query->distinct=true;
                $query->innerJoin('offer_request','offer_request.offer_id=offer.id and offer_request.status=:status_active and offer_request.pay_status is null',[':status_active'=>\app\models\OfferRequest::STATUS_ACTIVE]);
                $query->orderBy(['max_bet_dt'=>SORT_DESC]);
                $query->groupBy($columns);
                break;
            case 'REQUEST_INVITED':
                $query->distinct=true;
                $query->innerJoin('offer_request','offer_request.offer_id=offer.id and offer_request.pay_status=:pay_status',[':pay_status'=>\app\models\OfferRequest::PAY_STATUS_INVITED]);
                break;
            case 'REQUEST_PAYED':
                $query->distinct=true;
                $query->innerJoin('offer_request','offer_request.offer_id=offer.id and offer_request.pay_status=:pay_status and offer_request.pay_method!=:pay_method_pod',[':pay_status'=>\app\models\OfferRequest::PAY_STATUS_PAYED,':pay_method_pod'=>\app\models\OfferRequest::PAY_METHOD_POD]);
                break;
            case 'REQUEST_PAYING_POD':
                $query->distinct=true;
                $query->innerJoin('offer_request','offer_request.offer_id=offer.id and offer_request.pay_status=:pay_status and offer_request.pay_method=:pay_method_pod',[':pay_status'=>\app\models\OfferRequest::PAY_STATUS_PAYED,':pay_method_pod'=>\app\models\OfferRequest::PAY_METHOD_POD]);
                break;
            case 'REQUEST_CONFIRMED':
                $query->distinct=true;
                $query->innerJoin('offer_request','offer_request.offer_id=offer.id and offer_request.pay_status=:pay_status',[':pay_status'=>\app\models\OfferRequest::PAY_STATUS_CONFIRMED]);
                break;
            case 'TYPE_AUCTION':
                $query->andWhere('offer.type=:type', ['type'=>Offer::TYPE_AUCTION]);
                break;
            case 'TYPE_AUTOSELL':
                $query->andWhere('offer.type=:type', ['type'=>Offer::TYPE_AUTOSELL]);
                break;
            case 'TYPE_AD':
                $query->andWhere('offer.type=:type', ['type'=>Offer::TYPE_AD]);
                break;
            case 'SCHEDULED':
                $query->andWhere('offer.status=:status_scheduled', ['status_scheduled'=>Offer::STATUS_SCHEDULED]);
                break;
            default:
        }

        if ($filter['id']) {
            $query->andWhere(['id'=>$filter['id']]);
        }

        $models=$query->all();
        $hasMore=count($models)>$perPage;

        $data=[];
        foreach(array_slice($models,0,$perPage) as $model) {
            $idata=$model->toArray(['id','type','title','price','delivery_days','view_bonus','view_bonus_total','view_bonus_used','buy_bonus','status','pay_status','accepted_offer_request_id', 'amount', 'show_amount', 'delivery_cost', 'active_till']);

            // manually set expired status
//            if ((new EDateTime())->setTime(0,0,0)>(new EDateTime($model->active_till))) {
//                $idata['status']=Offer::STATUS_EXPIRED;
//            }

            $idata['create_dt']=(new EDateTime($model->create_dt))->js();
            $idata['canUpdateViewBonusTotal']=$model->canUpdateViewBonusTotal();

            if (count($model->offerInterests)>0) {
                $idata['level1Interest']=strval($model->offerInterests[0]->level1Interest);
                $idata['level2Interest']=strval($model->offerInterests[0]->level2Interest);

                $level3Interests=[];
                foreach($model->offerInterests as $sri) {
                    $level3Interests[]=$sri->level3Interest;
                }
                $idata['level3Interests']=implode(', ',$level3Interests);
            }

            if (count($model->files)>0) {
                $idata['image']=$model->files[0]->getThumbUrl('offer');
            } else {
                $idata['image']=\app\components\Thumb::createUrl('/static/images/account/default_interest.png','offer');
            }

            $idata['offerRequests']=[];
            $betters=[];
            foreach($model->offerActiveRequests as $offer) {
                $betters[$offer->user_id]=true;
                $odata=$offer->toArray(['id','status','pay_status','pay_method','description']);
                $odata['closed_dt'] = (new \app\components\EDateTime($offer->closed_dt))->js();
                $odata['bet_price'] = (float)$offer->bet_price;
                $odata['isExpired']=$offer->isExpired;
                $idata['offerRequestsCount']++;
                if ($idata['bestBet']<$offer->bet_price && !$odata['isExpired'] && $offer->status==OfferRequest::STATUS_ACTIVE) {
                    $idata['bestBet']=$offer->bet_price;
                }
                $odata['bet_active_till']=$offer->bet_active_till!='0000-00-00 00:00:00' ? (new \app\components\EDateTime($offer->bet_active_till))->js():null;
                $odata['user']=$offer->user->getShortData(['rating', 'feedback_count', 'packet']);

                $odata['modifications']=[];
                foreach($offer->offerRequestModifications as $mod) {
                    $odata['modifications'][]=['price'=>$mod->price,'dt'=>(new \app\components\EDateTime($mod->dt))->js()];
                }
                $odata['modificationsCount']=count($odata['modifications']);
                $idata['offerRequests'][]=$odata;
            }
            $idata['offerRequestsCount']=count($idata['offerRequests']);
            $idata['bettersCount']=count($betters);

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
        return $this->offers(json_decode($filter,true),$pageNum);
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
        Yii::$app->user->identity->viewedOffersRequests();
        return array_merge($this->initialData(),$this->offers());
    }

    public function actionGetOfferViewUsers($offer_id) {
        $offer_view_users = OfferView::find()->with(['user'])->where(['offer_id'=>$offer_id])->orderBy(['dt'=>SORT_DESC])->all();
        $data = [];
        foreach ($offer_view_users as $offer_view_user) {
            $data[] = ['avatarSmall'=>$offer_view_user->user->getAvatarThumbUrl('avatarSmall')];
        }

        $count_users = count($data);
        return [
            'users'=>$count_users>0 ? $data : false,
            'count_users'=>$count_users
        ];
    }



}
