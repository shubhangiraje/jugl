<?php

namespace app\controllers;

use app\models\OfferInterest;
use Yii;
use app\models\Interest;
use yii\db\Query;
use app\models\Offer;
use app\components\EDateTime;


class ApiOfferAdvancedSearchController extends \app\components\ApiController {

    private function getCountOffers($level1_interest_id) {
        $countOffers = OfferInterest::find()
            ->distinct()
            ->where(['level1_interest_id'=>$level1_interest_id])
            ->joinWith(['offer'])
            ->andWhere(['offer.status'=>Offer::STATUS_ACTIVE])
            ->count();
        return $countOffers;
    }

    private function getInterests() {
        $interests = Interest::find()
            ->where('parent_id is null and type=:type_offer',[':type_offer'=>'OFFER'])
            ->with(['file'])
            ->orderBy(['sort_order'=>SORT_ASC])
            ->all();

        $data = [];
        foreach ($interests as $item) {
            $thumb = $item->file->link ? $thumb = $item->file->getThumbUrl('interestMobile') : $thumb = \app\components\Thumb::createUrl('/static/images/account/default_interest.png','interestMobile',true);
            $idata = [
                'interest_id' => $item->id,
                'interest_title' => $item->title,
                'interest_sort' => $item->sort_order,
                'interest_img'=>$thumb
            ];
            $idata['count_offers'] = $this->getCountOffers($item->id);
            $data[] = $idata;
        }
        return $data;
    }

    public function actionIndex() {
        return [
            'results' => [
                'interests'=>$this->getInterests(),
                'countries' => \app\components\Helper::getCountriesList()
            ]
        ];
    }

    private function search($filter=[],$pageNum=1,$user_id=NULL) {
        $perPage=10;
		$perpageAdvertising = $perPage >=(\app\models\Setting::get('ADVERTISING_OFFER_MOD_COUNT'))  ? intval(floor(($perPage/(\app\models\Setting::get('ADVERTISING_OFFER_MOD_COUNT'))))) :  \app\models\Setting::get('ADVERTISING_OFFER_MOD_COUNT') ;

        $squery=new Query;

        $columns=['offer.id'];
        if ($filter['text']) {
            $columns['relevancy']='MATCH(offer.title) AGAINST(:text)';
        }

        $squery->select($columns)
            ->from('offer')
/*
            ->innerJoin('offer_interest','offer_interest.offer_id=offer.id')
            ->innerJoin('user','user.id=offer.user_id')
            ->leftJoin('user_interest','user_interest.user_id=:user_id and (
                user_interest.level3_interest_id=offer_interest.level3_interest_id or
                user_interest.level2_interest_id=offer_interest.level2_interest_id or
                user_interest.level1_interest_id=offer_interest.level1_interest_id
                )',[':user_id'=>Yii::$app->user->id])
            //->where('offer.active_till>=CAST(NOW() AS DATE) and offer.status=:active_status',[':active_status'=>Offer::STATUS_ACTIVE])
            ->groupBy('offer.id')
*/
            ->where('offer.view_bonus is null and offer.active_till>=CAST(NOW() AS DATE) and offer.status=:active_status',[':active_status'=>Offer::STATUS_ACTIVE])
            ->offset(($pageNum-1)*$perPage)
            ->orderBy('id desc')
            ->limit($perPage+1);

        if ($filter['excludeInterests']) {
            $ids=array_map("intval",$filter['excludeInterests']);
            if (!empty($ids)) {
                $squery->andWhere('not exists(select id from offer_interest where offer_id=offer.id and level1_interest_id in ('.implode(',',$ids).'))');
            }
        }

        if ($filter['text']) {
            $squery->andWhere('MATCH(offer.title) AGAINST(:text)',[':text'=>$filter['text']]);
            $squery->orderBy('relevancy desc');
        }

        if ($filter['advancedEnabled']) {
            $squery->andFilterCompare('price',$filter['advanced']['price_from'],'>=');
            $squery->andFilterCompare('price',$filter['advanced']['price_to'],'<=');
            $squery->andFilterCompare('country_id',$filter['advanced']['country_id']);
            $squery->andFilterCompare('city',$filter['advanced']['city']);

            if ($filter['advanced']['distance'] && $filter['advanced']['longitude'] && $filter['advanced']['lattitude']) {
                $dist=ceil($filter['advanced']['distance']);
                $longitude=$filter['advanced']['longitude'];
                $lattitude=$filter['advanced']['lattitude'];

                $longitudeFrom = $longitude-$dist/abs(cos($lattitude/180*3.1415926)*69);
                $longitudeTo = $longitude+$dist/abs(cos($lattitude/180*3.1415926)*69);
                $lattitudeFrom = $lattitude-($dist/69);
                $lattitudeTo = $lattitude+($dist/69);

                $squery->innerJoin('zip_coords',"zip_coords.zip=offer.zip and zip_coords.country_id=offer.country_id and
                    lattitude>=:lattitude_from and lattitude<=:lattitude_to and
                    longitude>=:longitude_from and longitude<=:longitude_to and
                    :dist>=3959 * acos( cos( radians(:lattitude) )
                          * cos( radians(zip_coords.lattitude) )
                          * cos( radians(zip_coords.longitude) - radians(:longitude)) + sin(radians(:lattitude))
                          * sin( radians(zip_coords.lattitude) ))
                ",[
                    ':longitude_from'=>$longitudeFrom,
                    ':longitude_to'=>$longitudeTo,
                    ':lattitude_from'=>$lattitudeFrom,
                    ':lattitude_to'=>$lattitudeTo,
                    ':longitude'=>$longitude,
                    ':lattitude'=>$lattitude,
                    ':dist'=>$dist
                ]);

            }
        }
/*
        if($user_id) {
            $squery->where('offer.active_till>=CAST(NOW() AS DATE) and offer.status=:active_status and offer.user_id=:offer_user_id',[':active_status'=>Offer::STATUS_ACTIVE, ':offer_user_id'=>$user_id]);
        } else {
            $squery->where('offer.active_till>=CAST(NOW() AS DATE) and offer.status=:active_status',[':active_status'=>Offer::STATUS_ACTIVE]);
        }

        Yii::$app->user->identity->addOfferSearchFilterConditions($squery);

        switch ($filter['sort']) {
            case 'relevancy':
                $squery->orderBy('relevancy desc');
                break;
            case 'view_bonus':
                $squery->orderBy('offer.view_bonus desc');
                break;
            case 'buy_bonus':
                $squery->orderBy('offer.buy_bonus desc');
                break;
            case 'rating':
                $squery->orderBy('user.rating desc');
                break;
            default:
                $squery->orderBy('offer.create_dt desc');
        }

        if ($filter['type']) {
            $squery->andWhere(['offer.type'=>$filter['type']]);
        }
*/

        $rows=$squery->all();
        $hasMore=count($rows)>$perPage;
        $rows=array_slice($rows,0,$perPage);

        $unsortedModels=Offer::find()->andWhere(['id'=>\yii\helpers\ArrayHelper::getColumn($rows,'id')])->with([
            'user',
            'user.avatarFile',
            'offerInterests',
            'offerInterests.level1Interest',
            'offerInterests.level2Interest',
            'offerInterests.level3Interest',
            'files',
            'offerMyFavorites'
        ])->indexBy('id')->all();

        $data=[];
		$adata = [];

		if ($filter['excludeInterests']) {
            $ids=array_map("intval",$filter['excludeInterests']);
            if (!empty($ids)) {
               $advertising  = \app\models\Advertising::getAdvertisingOfferAdvanceSearch('ADVERTISING_WHITOUT_BONUS_DETAIL','',$pageNum,$perpageAdvertising, Yii::$app->user->identity->country_id, $ids);
            }
        }else{
			$advertising  = \app\models\Advertising::getAdvertisingOfferAdvanceSearch('ADVERTISING_WHITOUT_BONUS_DETAIL','',$pageNum,$perpageAdvertising, Yii::$app->user->identity->country_id);
		}

		if(!empty($advertising)){
			foreach ($advertising['model'] as $item1) {
				$adata[] = $item1->toArray(['advertising_display_name','link','user_bonus', 'banner', 'id', 'advertising_type', 'click_interval', 'popup_interval']);
			}	
		}
		$aryKey = 0;
		$i = 0;
        foreach($rows as $row) {
            $model=$unsortedModels[$row['id']];
            $idata=$model->toArray(['id','type','title','price','delivery_days','view_bonus','buy_bonus','zip','city','address','relevancy', 'amount', 'show_amount', 'active_till']);
            $idata['description']=\yii\helpers\StringHelper::truncate($model->description,100);
            $idata['create_dt']=(new EDateTime($model->create_dt))->js();
            $idata['relevancy']=floor(0.5+$row['relevancy']);
            $idata['user']=$model->user->getShortData(['rating', 'feedback_count', 'packet', 'country_id']);
            $idata['favorite']=count($model->offerMyFavorites)>0;

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
                $idata['image'] = $model->files[0]->getThumbUrl('searchRequestMobile');
            } else {
                $idata['image'] = \app\components\Thumb::createUrl('/static/images/account/default_interest.png','searchRequestMobile',true);
            }

            $data[]=$idata;
			if($adata){
				if($i % \app\models\Setting::get('ADVERTISING_OFFER_MOD_COUNT') == 0){
					if($adata[$aryKey]){
						$data[] = $adata[$aryKey];
						$aryKey++;
					}
				}
			}
        }
		
		if(count($rows) < $perPage){			
			foreach($adata as $k => $v){
					if($k >= $aryKey){
						$data[] = $v;
					}
			}
		}
		
		if($advertising['rowsCount'] > count($rows)){
			if ($filter['excludeInterests']) {
				$ids=array_map("intval",$filter['excludeInterests']);
				if (!empty($ids)) {
				   $advertising  = \app\models\Advertising::getAdvertisingOfferAdvanceSearch('ADVERTISING_WHITOUT_BONUS_DETAIL','',$pageNum,$perpageAdvertising, Yii::$app->user->identity->country_id, $ids);
				}
			}else{
				$advertising  = \app\models\Advertising::getAdvertisingOfferAdvanceSearch('ADVERTISING_WHITOUT_BONUS_DETAIL','',$pageNum,$perpageAdvertising, Yii::$app->user->identity->country_id);
			}
			
			if(!empty($advertising)){
				$hasMore = true;
				foreach ($advertising['model'] as $item1) {
					$adata[] = $item1->toArray(['advertising_display_name','link','user_bonus', 'banner', 'id', 'advertising_type', 'click_interval', 'popup_interval']);
				}	
			}else{
				$hasMore = false;
			}
		}
		
        $result = [
            'items'=>$data,
            'hasMore'=>$hasMore
        ];
/*
        $filterData = [];
        $filterQuery = Interest::find()
            ->where(['id'=>[$filter['level1_interest_id'], $filter['level2_interest_id'], $filter['level3_interest_id']]])
            ->all();

        foreach ($filterQuery as $item) {
            $filterData[] = $item->title;
        }

        $result['filterData']=$filterData;

        $paramValueData = [];
        if(!empty($filter['params'])) {
            foreach ($filter['params'] as $param=>$value) {
                if($value) {
                    $paramValue = ParamValue::find()
                        ->with(['param'])
                        ->where(['id'=>$value])
                        ->one();

                    $paramValueData[] = [
                        'param' => $paramValue->param->title,
                        'value' => $paramValue->title
                    ];
                }
            }

        }

        $result['paramValue']=$paramValueData;
*/
        return [
            'results'=>$result
        ];

    }

    public function actionSearch($filter, $pageNum) {
        Yii::$app->user->identity->viewedOffers();
        return $this->search(json_decode($filter,true),$pageNum);
    }

}
