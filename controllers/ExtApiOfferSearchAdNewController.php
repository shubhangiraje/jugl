<?php

namespace app\controllers;

use app\models\ParamValue;
use Yii;
use yii\web\NotFoundHttpException;
use app\models\Offer;
use app\models\Interest;
use app\models\Param;
use app\components\EDateTime;
use app\models\Country;
use yii\db\Query;


class ExtApiOfferSearchAdNewController extends \app\components\ExtApiController {

    private function addInterestAndParamValueFilter($filter,$level,$squery) {
        static $joinNum=1;

        $idName='level'.$level.'_interest_id';

        if (!$filter[$idName]) return;

        switch($level) {
            case 1:
            case 2:
                // for search request all records in offer_interest has equal level1_interest_id and level2_interest_id and can be filtered by join
                $squery->andWhere(['offer_interest.level'.$level.'_interest_id'=>$filter[$idName]]);
                break;
            case 3:
                $squery->addSelect(['level3_match'=>'MAX(IF(offer_interest.level3_interest_id='.intval($filter[$idName]).',1,0))'])
                    ->having(['level3_match'=>1]);
                break;
        }

        // add filter by params only if level3 is specified
        if ($filter['level3_interest_id']) {
            $interest = Interest::findOne($filter[$idName]);
            if (!$interest) return;

            foreach ($interest->params as $param) {
                if ($param->type == Param::TYPE_LIST && $filter['params'][$param->id] != '') {
                    $tableName = 'ipvfil' . ($joinNum++);
                    $squery->innerJoin("offer_param_value as $tableName", "$tableName.offer_id=offer.id and $tableName.param_id=:param_id_$tableName and $tableName.param_value_id=:param_value_id_$tableName", [
                        ":param_id_$tableName" => $param->id,
                        ":param_value_id_$tableName" => $filter['params'][$param->id]
                    ]);
                }
            }
        }
    }

    private function search($filter=[],$pageNum=1,$user_id=NULL) {
        $perPage=10;
		$perpageAdvertising = $perPage >=(\app\models\Setting::get('ADVERTISING_OFFER_MOD_COUNT'))  ? intval(floor(($perPage/(\app\models\Setting::get('ADVERTISING_OFFER_MOD_COUNT'))))) :  \app\models\Setting::get('ADVERTISING_OFFER_MOD_COUNT') ;

		$country_ids=false;
		
		if($filter['country']){
			$country_temp=$filter['country'];
			foreach($country_temp as $cid){
				$country_ids[]=$cid['id'];
			}	
		}
		
        $squery=new Query;
        $squery->select(['offer.id',
                'relevancy'=>
                    // level 3 relevancy
                    '33.33*('.
                    // count of matched level3 interests
                    'SUM(IF(user_interest.level3_interest_id=offer_interest.level3_interest_id,1,0))+'.
                    // or 1 if level1 & level2 interests matches
                    'MAX(IF(offer_interest.level3_interest_id is null and (offer_interest.level2_interest_id=user_interest.level2_interest_id or offer_interest.level2_interest_id is null) and offer_interest.level1_interest_id=user_interest.level1_interest_id ,1,0))'.
                    ')/'.
                    // count of level3 interests in search request or 1
                    'COALESCE(NULLIF(COUNT(DISTINCT offer_interest.level3_interest_id),0),1)+'.
                    // level 2 relevancy
                    '33.33*MAX(IF(user_interest.level2_interest_id=offer_interest.level2_interest_id or (offer_interest.level2_interest_id is null and user_interest.level1_interest_id=offer_interest.level1_interest_id),1,0))+'.
                    // level 1 relevancy
                    '33.33*MAX(IF(user_interest.level1_interest_id=offer_interest.level1_interest_id,1,0))','offer.country_id'
            ])
            ->from('offer')
            ->innerJoin('offer_interest','offer_interest.offer_id=offer.id')
            ->innerJoin('user','user.id=offer.user_id')
            ->leftJoin('user_interest','user_interest.user_id=:user_id and (
                user_interest.level3_interest_id=offer_interest.level3_interest_id or
                user_interest.level2_interest_id=offer_interest.level2_interest_id or
                user_interest.level1_interest_id=offer_interest.level1_interest_id
                )',[':user_id'=>Yii::$app->user->id])
            //->where('offer.active_till>=CAST(NOW() AS DATE) and offer.status=:active_status',[':active_status'=>Offer::STATUS_ACTIVE])
            ->groupBy('offer.id')
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

		/*nviimedia*/
		if ($country_ids) {
		$countries=$country_ids;	
			if(!in_array('no_countries',$countries)){
				$squery->where(['in','offer.country_id',$countries])->orWhere('offer.country_id IS NULL');
			}
			/*elseif(in_array('no_countries',$countries)){
				$squery->andWhere(['in','offer.country_id',$countries])->orWhere('offer.country_id IS NULL');	
			}*/
        }
		else{
			$squery->where('offer.country_id IS NULL');
		}
		/*nviimedia*/
		
        if($user_id) {
            $squery->andWhere('offer.active_till>=CAST(NOW() AS DATE) and offer.status=:active_status and offer.user_id=:offer_user_id',[':active_status'=>Offer::STATUS_ACTIVE, ':offer_user_id'=>$user_id]);
        } else {
            $squery->andWhere('offer.active_till>=CAST(NOW() AS DATE) and offer.status=:active_status',[':active_status'=>Offer::STATUS_ACTIVE]);
        }
		
		
		
		
		
        $squery->andWhere('offer.view_bonus>0');

        Yii::$app->user->identity->addOfferSearchFilterConditions($squery);

        $this->addInterestAndParamValueFilter($filter,1,$squery);
        $this->addInterestAndParamValueFilter($filter,2,$squery);
        $this->addInterestAndParamValueFilter($filter,3,$squery);

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
		$advertising  = \app\models\Advertising::getAdvertising('ADVERTISING_DETAIL','',$pageNum,$perpageAdvertising, Yii::$app->user->identity->country_id);
		if(!empty($advertising)){
			foreach ($advertising['model'] as $item1) {
				$adata[] = $item1->toArray(['advertising_display_name','link','user_bonus', 'banner', 'id', 'advertising_type', 'click_interval', 'popup_interval']);
			}	
		}
		$aryKey = 0;
		$i = 0;
		
        foreach($rows as $row) {
			$i++;
			$z = $i;
            $model=$unsortedModels[$row['id']];
            $idata=$model->toArray(['id','type','country_id','title','price','delivery_days','view_bonus','buy_bonus','zip','city','address','relevancy', 'amount', 'show_amount', 'active_till', 'count_offer_view']);
            $idata['description']=\yii\helpers\StringHelper::truncate($model->description,100);
            $idata['create_dt']=(new EDateTime($model->create_dt))->js();
            $idata['relevancy']=floor(0.5+$row['relevancy']);
            $idata['user']=$model->user->getShortData(['rating', 'feedback_count', 'packet','country_id']);
			
			/* NVII-MEDIA - Output Flag */
			$flagAry = Country::getListShort();
			$idata['user']['flag'] = $flagAry[$idata['user']['country_id']];
			/* NVII-MEDIA - Output Flag */
			
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
			$advertising  = \app\models\Advertising::getAdvertising('ADVERTISING_DETAIL','',$pageNum,$perpageAdvertising, Yii::$app->user->identity->country_id);
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
			'currentCountry'=>$this->currentCountry(),
            'items'=>$data,
            'hasMore'=>$hasMore
        ];

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

        return [
            'results'=>$result
        ];

    }

    public function actionSearch($filter, $pageNum,$user=Null) {
        Yii::$app->user->identity->viewedOffers();
        return $this->search(json_decode($filter,true),$pageNum,$user);
    }

    public function actionSearchBy($filter, $pageNum, $user_id) {
        return $this->search(json_decode($filter,true),$pageNum,$user_id);
    }

    private function initialData() {
        $data=[];

        $data['interests']=[];
        foreach(Interest::find()->where(['type'=>Interest::TYPE_OFFER])->orderBy('sort_order asc')->all() as $interest) {
            $data['interests'][]=$interest->toArray(['id','parent_id','title']);
        }

        $data['params']=[];
        $paramsModels=Param::find()->with(['paramValues'])->where(['type'=>Param::TYPE_LIST])->orderBy('interest_id asc,sort_order asc')->all();
        foreach($paramsModels as $param) {
            $pdata=$param->toArray(['id','interest_id','title','required']);
            $pdata['values']=[['id'=>0,'title'=>'']];
            foreach($param->paramValues as $value) {
                $pdata['values'][]=$value->toArray(['id','title']);
            }
            $data['params'][]=$pdata;
        }

        return $data;
    }

    public function actionIndex() {
        return array_merge($this->initialData(),$this->search());
    }

    private function filterData() {
        $data=[];

        $data['interests']=[];
        foreach(Interest::find()->where(['type'=>Interest::TYPE_OFFER])->orderBy('sort_order asc')->all() as $interest) {
            $data['interests'][]=$interest->toArray(['id','parent_id','title']);
        }

        $data['params']=[];
        $paramsModels=Param::find()->with(['paramValues'])->where(['type'=>Param::TYPE_LIST])->orderBy('interest_id asc,sort_order asc')->all();
        foreach($paramsModels as $param) {
            $pdata=$param->toArray(['id','interest_id','title','required']);
            $pdata['values']=[['id'=>0,'title'=>'']];
            foreach($param->paramValues as $value) {
                $pdata['values'][]=$value->toArray(['id','title']);
            }
            $data['params'][]=$pdata;
        }

        return $data;
    }

    public function actionInitFilter() {
        return $this->filterData();
    }
	public function currentCountry(){
		$countryAry = Country::getList();
		$countryShortAry = Country::getListShort();
		$data = array();
		
		$data['country_id'] = Yii::$app->user->identity->country_id;
		$data['country_name'] = $countryAry[Yii::$app->user->identity->country_id];
		$data['country_shortname'] = $countryShortAry[Yii::$app->user->identity->country_id];
		return $data;
		
	}





}
