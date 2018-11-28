<?php

namespace app\controllers;

use app\models\ParamValue;
use Yii;
use yii\web\NotFoundHttpException;
use app\models\SearchRequest;
use app\models\Interest;
use app\models\Param;
use app\models\Country;
use app\components\EDateTime;
use yii\db\Query;


class ExtApiSearchRequestSearchNewController extends \app\components\ExtApiController {

    private function addInterestAndParamValueFilter($filter,$level,$squery) {
        static $joinNum=1;

        $idName='level'.$level.'_interest_id';

        if (!$filter[$idName]) return;

        switch($level) {
            case 1:
            case 2:
                // for search request all records in search_request_interest has equal level1_interest_id and level2_interest_id and can be filtered by join
                $squery->andWhere(['search_request_interest.level'.$level.'_interest_id'=>$filter[$idName]]);
                break;
            case 3:
                $squery->addSelect(['level3_match'=>'MAX(IF(search_request_interest.level3_interest_id='.intval($filter[$idName]).',1,0))'])
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
                    $squery->innerJoin("search_request_param_value as $tableName", "$tableName.search_request_id=search_request.id and $tableName.param_id=:param_id_$tableName and $tableName.param_value_id=:param_value_id_$tableName", [
                        ":param_id_$tableName" => $param->id,
                        ":param_value_id_$tableName" => $filter['params'][$param->id]
                    ]);
                }
            }
        }
    }

    private function search($filter=[],$pageNum=1, $user_id = NULL) {
        $perPage=10;
		
		$country_ids=false;
		
		if($filter['country']){
			$country_temp=$filter['country'];
			foreach($country_temp as $cid){
				$country_ids[]=$cid['id'];
			}	
		}

        $level1InterestsIds=Yii::$app->user->identity->getLevel1InterestIds(\app\models\UserInterest::TYPE_SEARCH_REQUEST);

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

        if($user_id) {
            $squery->where('search_request.active_till>=CAST(NOW() AS DATE) and search_request.status=:active_status and search_request.user_id=:user_id',[':active_status'=>SearchRequest::STATUS_ACTIVE, ':user_id'=>$user_id]);
        } else {
            $squery->where('search_request.active_till>=CAST(NOW() AS DATE) and search_request.status=:active_status',[':active_status'=>SearchRequest::STATUS_ACTIVE]);
        }
		
		if(!$country_ids){
			if(Yii::$app->user->identity->country_id!=null){
			$squery->andWhere(array('search_request.country_id'=>Yii::$app->user->identity->country_id));
			}
		}else{
			$squery->andWhere(array('in','search_request.country_id',$country_ids));
		}

        $squery->andWhere("exists(
            select * from search_request_interest
                  where search_request_interest.search_request_id=search_request.id and search_request_interest.level1_interest_id in (".implode(',',$level1InterestsIds).")
              )");

        $this->addInterestAndParamValueFilter($filter,1,$squery);
        $this->addInterestAndParamValueFilter($filter,2,$squery);
        $this->addInterestAndParamValueFilter($filter,3,$squery);

        switch ($filter['sort']) {
            case 'relevancy':
                $squery->orderBy('relevancy desc');
                break;
            case 'bonus':
                $squery->orderBy('search_request.bonus desc');
                break;
            case 'rating':
                $squery->orderBy('user.rating desc');
                break;
            default:
                $squery->orderBy('search_request.create_dt desc');
        }

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
            'searchRequestMyFavorites'
        ])->indexBy('id')->all();

        $data=[];
        $ids = [];
        foreach($rows as $row) {
            $model=$unsortedModels[$row['id']];
            $idata=$model->toArray(['id','title','price_from','price_to','bonus','zip','city','address','relevancy']);
            $ids[] = $model->id;
            $idata['description']=\yii\helpers\StringHelper::truncate($model->description,100);
            $idata['create_dt']=(new EDateTime($model->create_dt))->js();
            $idata['relevancy']=floor(0.5+$row['relevancy']);
            $idata['user']=$model->user->getShortData(['rating', 'feedback_count', 'packet','country_id']);
			/* NVII-MEDIA - Output Flag */
			$flagAry = Country::getListShort();
			$idata['user']['flag'] = $flagAry[$idata['user']['country_id']];
			/* NVII-MEDIA - Output Flag */
            $idata['favorite']=count($model->searchRequestMyFavorites)>0;

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

        if(!empty($ids)) {
            $ids = implode(',',$ids);

            $countsSearchRequest = Yii::$app->db->createCommand("select sro.search_request_id,count(*) as count_total,sum(IF(status='ACCEPTED',1,0)) as count_accepted,sum(IF(status='REJECTED',1,0)) as count_rejected
            from search_request_offer sro where sro.search_request_id in (".$ids.")
                group by sro.search_request_id")->queryAll();

            $dataCounts = [];
            foreach ($countsSearchRequest as $item) {
                $dataCounts[$item['search_request_id']]=[
                    'count_total'=>$item['count_total'],
                    'count_accepted'=>$item['count_accepted'],
                    'count_rejected'=>$item['count_rejected']
                ];
            }

            foreach ($data as $key=>$value) {
                $data[$key]['count_total']=$dataCounts[$value['id']]['count_total'];
                $data[$key]['count_accepted']=$dataCounts[$value['id']]['count_accepted'];
                $data[$key]['count_rejected']=$dataCounts[$value['id']]['count_rejected'];
            }
        }

        $result = [
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

    public function actionSearch($filter,$pageNum) {
        Yii::$app->user->identity->viewedSearchRequests();
        return $this->search(json_decode($filter,true),$pageNum);
    }

    private function filterData() {
        $data=[];

        $data['interests']=[];
        foreach(Interest::find()->where(['type'=>Interest::TYPE_SEARCH_REQUEST])->orderBy('sort_order asc')->all() as $interest) {
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

    public function actionSearchBy($filter, $pageNum, $user_id) {
        return array_merge($this->filterData(), $this->search(json_decode($filter,true),$pageNum, $user_id));
    }


}
