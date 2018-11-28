<?php

namespace app\controllers;

use Yii;
use app\models\Offer;
use app\models\OfferRequest;
use app\models\Interest;


class ExtApiOfferMyRequestController extends \app\components\ExtApiController {

    private function offerRequests($filter=[],$pageNum=1) {
        $perPage=10;

        $query=OfferRequest::find()
            ->andWhere(['offer_request.user_id'=>Yii::$app->user->id,'offer.type'=>[Offer::TYPE_AUCTION, Offer::TYPE_AUTOSELL]])
            ->joinWith(['offer'])
            ->with([
                'offer',
                'offer.offerInterests',
                'offer.offerInterests.level1Interest',
                'offer.offerInterests.level2Interest',
                'offer.offerInterests.level3Interest',
                'offer.files',
            ])
            ->orderBy(['offer_request.id'=>SORT_DESC])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        switch ($filter['status']) {
            case 'ACTIVE':
                $query->andWhere("
                    (offer.status=:status_active or offer.status=:status_paused) and
                    offer_request.status=:status_active and offer_request.bet_active_till>=NOW()"
                    , [':status_active'=>OfferRequest::STATUS_ACTIVE ,
                       ':status_paused'=>Offer::STATUS_PAUSED]);
                break;
            case 'EXPIRED':
                $query->andWhere('offer.status in (:status_active,:status_paused) and
                offer_request.status!=:status_accepted and (offer_request.bet_active_till=\'0000-00-00 00:00:00\' or offer_request.bet_active_till<NOW())', [
                    ':status_active'=>Offer::STATUS_ACTIVE,
                    ':status_paused'=>Offer::STATUS_PAUSED,
                    ':status_accepted'=>OfferRequest::STATUS_ACCEPTED,
                ]);
                break;
            case 'ACCEPTED':
                $query->andWhere('offer_request.status=:status_accepted and offer.type=:offer_type', [
                    ':status_accepted'=>OfferRequest::STATUS_ACCEPTED,
                    ':offer_type' => Offer::TYPE_AUCTION
                ]);
                break;
            case 'OFFER_EXPIRED':
                $query->andWhere('offer.status!=:status_active and offer.status!=:status_paused and
                offer_request.status!=:status_accepted', [
                    ':status_active'=>Offer::STATUS_ACTIVE,
                    ':status_paused'=>Offer::STATUS_PAUSED,
                    ':status_accepted'=>OfferRequest::STATUS_ACCEPTED,
                ]);
                break;
            case 'OFFER_BUY':
                $query->andWhere('offer_request.status=:status_accepted and offer.type=:offer_type', [
                    ':status_accepted'=>OfferRequest::STATUS_ACCEPTED,
                    ':offer_type' => Offer::TYPE_AUTOSELL
                ]);
                break;
            default:
        }

        $models=$query->all();
        $hasMore=count($models)>$perPage;

        $data=[];
        foreach(array_slice($models,0,$perPage) as $model) {
            $idata=$model->toArray(['id','status','pay_status','bet_price']);
            $idata['bet_active_till']=$model->bet_active_till!='0000-00-00 00:00:00' ? (new \app\components\EDateTime($model->bet_active_till))->js():null;
            $idata['isExpired']=$model->isExpired;
            $idata['betCanBeChanged']=$model->betCanBeChanged;

            $odata=$model->offer->toArray(['id','type','title','price','delivery_days','view_bonus','view_bonus_total','view_bonus_used','buy_bonus','status','pay_status','accepted_offer_request_id', 'amount', 'show_amount', 'delivery_cost', 'active_till']);
            $odata['user']=$model->offer->user->getShortData(['rating', 'feedback_count', 'packet']);

            if (count($model->offer->offerInterests)>0) {
                $odata['level1Interest']=strval($model->offer->offerInterests[0]->level1Interest);
                $odata['level2Interest']=strval($model->offer->offerInterests[0]->level2Interest);

                $level3Interests=[];
                foreach($model->offer->offerInterests as $sri) {
                    $level3Interests[]=$sri->level3Interest;
                }
                $odata['level3Interests']=implode(', ',$level3Interests);
            }

            if (count($model->offer->files)>0) {
                $odata['image']=$model->offer->files[0]->getThumbUrl('offerMobile');
            } else {
                $odata['image']=\app\components\Thumb::createUrl('/static/images/account/default_interest.png','offerMobile',true);
            }

            $idata['offer']=$odata;

            $data[]=$idata;
        }

        $offerRequestIds=\yii\helpers\ArrayHelper::getColumn($data,'offer.id');

        if (!empty($offerRequestIds)) {
            $cobettersData=Yii::$app->db->createCommand("
                select offer_id,count(distinct user_id) as cobetters_count
                from offer_request ofr
                where ofr.status=:status_active and ofr.bet_active_till>=NOW() and ofr.user_id!=:user_id and offer_id in (".implode(',',$offerRequestIds).")
                group by offer_id
            ",[
                ":status_active"=>\app\models\OfferRequest::STATUS_ACTIVE,
                ":user_id"=>Yii::$app->user->id
            ])->queryAll();

            $cobettersData=\yii\helpers\ArrayHelper::index($cobettersData,'offer_id');

            $bestBetData=Yii::$app->db->createCommand("
                select offer_id,max(bet_price) as bet_price
                from offer_request ofr
                where ofr.status=:status_active and ofr.bet_active_till>=NOW() and offer_id in (".implode(',',$offerRequestIds).")
                group by offer_id
            ",[
                ":status_active"=>\app\models\OfferRequest::STATUS_ACTIVE
            ])->queryAll();

            $bestBetData=\yii\helpers\ArrayHelper::index($bestBetData,'offer_id');

            foreach($data as &$offerRequest) {
                $offerRequest['offer']['cobettersCount']=intval($cobettersData[$offerRequest['offer']['id']]['cobetters_count']);
                $offerRequest['offer']['bestBet']=$bestBetData[$offerRequest['offer']['id']]['bet_price'];
            }
        }

        return [
            'results'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ]
        ];
    }

    public function actionList($filter,$pageNum) {
        return $this->offerRequests(json_decode($filter,true),$pageNum);
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
        return array_merge($this->initialData(),$this->offerRequests());
    }
}
