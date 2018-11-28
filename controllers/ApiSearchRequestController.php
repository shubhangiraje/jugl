<?php

namespace app\controllers;

use app\components\EDateTime;
use app\models\SearchRequestComment;
use app\models\SearchRequestDraft;
use app\models\User;
use Yii;
use app\models\Interest;
use app\components\Helper;
use \app\models\SearchRequest;
use \app\models\SearchRequestInterest;
use \app\models\SearchRequestParamValue;

use \yii\web\NotFoundHttpException;


class ApiSearchRequestController extends \app\components\ApiController {

    private function parseIds($idsStr,&$level1Interest,&$level2Interest,&$level3Interests) {
        if ($idsStr=='') {
            $idsStr=\app\models\Interest::COMMON_INTEREST_ID2;
        }
		
        if ($idsStr=='') {
            $level1Interest=new Interest();
            $level2Interest=new Interest();
            $level3Interests=[new Interest()];
            return false;
        }

        $ids=explode(',',$idsStr);

        $level3Interests=Interest::find()
            ->andWhere(['id'=>$ids])
            ->with(['parent','parent.parent','params','params.paramValues','interestParamValues'])
            ->all();

        if (count($level3Interests)!=count($ids)) return false;

        if (count($level3Interests)==1 && $level3Interests[0]->level<3) {
            $interest=$level3Interests[0];
            if ($interest->level==1) {
                $level1Interest=$interest;
                $level2Interest=new Interest();
                $level3Interests=[new Interest()];
                return true;
            }
            if ($interest->level==2) {
                $level1Interest=$interest->parent;
                $level2Interest=$interest;
                $level3Interests=[new Interest()];
                return true;
            }
        }

        $level1Interest=$level3Interests[0]->parent->parent;
        $level2Interest=$level3Interests[0]->parent;

        foreach($level3Interests as $interest) {
            if ($interest->parent_id!=$level2Interest->id ||
                $interest->parent->parent_id!=$level1Interest->id) {
                return false;
            }
        }

        return true;
    }

    private function getParams($interests,&$params,&$paramsSelectedValue) {
        $params=[];
        $paramsSelectedValues=[];

        foreach($interests as $interest) {
            $params=array_merge($params,$interest->params);
            foreach($interest->interestParamValues as $ipv) {
                $paramsSelectedValues[$ipv->param_id][]=$ipv->param_value_id;
            }
        }

        $paramsSelectedValue=[];
        foreach($paramsSelectedValues as $paramId=>$values) {
            if (count(array_unique($values))==1) $paramsSelectedValue[$paramId]=$values[0];
        }
    }
	
	
	
    public function actionAdd($ids) {
	if(!$ids){
	$ids='685000';
	}
//        if ($this->parseIds($ids,$level1Interest,$level2Interest,$level3Interests)===false) {
//            throw new \Exception("invalid ids passed: $ids");
//        };

        $this->parseIds($ids,$level1Interest,$level2Interest,$level3Interests);

		//$this->countRequests()
        $data=[];
//        $activeTill=(new EDateTime())->modify('+1 month');
//        $data['searchRequest']=[
//            'files'=>[],
//            'active_till_parts'=>[
//                'day'=>intval($activeTill->format('d')),
//                'month'=>intval($activeTill->format('m')),
//                'year'=>intval($activeTill->format('Y'))
//            ]
//        ];

        $data['searchRequest']=[
            'files'=>[],
            'active_till_parts'=>[
                'day'=>'',
                'month'=>'',
                'year'=>''
            ],
            'country_id'=>64,
            'is_active_immediately'=>1,
            'scheduled_dt_parts'=>[
                'day'=>'',
                'month'=>'',
                'year'=>'',
                'hours'=>'',
                'minutes'=>''
            ]
        ];

        $data['searchRequest']['searchRequestInterests']=[];

		
        foreach($level3Interests as $interest) {
            $data['searchRequest']['searchRequestInterests'][]=[
                'level1Interest'=>$level1Interest->getShortData(),
                'level2Interest'=>$level2Interest->getShortData(),
                'level3Interest'=>$interest->getShortData(),
            ];
        }

        $this->getParams(array_merge(
            [
                $level1Interest,
                $level2Interest,
            ],
            $level3Interests
        ),$params,$paramsSelectedValue);

        $data['searchRequest']['searchRequestParamValues']=[];
        foreach($params as $param) {
            $pdata=[
                'param_value_id'=>$paramsSelectedValue[$param->id],
                'param_id'=>$param->id
            ];
            $pdata['param']=$param->toArray(['id','title','type','required']);
            $pdata['param']['values']=[];
            foreach($param->paramValues as $value) {
                $pdata['param']['values'][]=$value->toArray(['id','title']);
            }
            $data['searchRequest']['searchRequestParamValues'][]=$pdata;
        }
		
		$id_array = array_map('intval', explode(',', $_GET['ids']));
			if(!$_GET['ids']){
				$id_array=array(685000);
			}

		$searchInterestCount = SearchRequestInterest::find()
		->where(['in', 'level1_interest_id', $id_array])
		->orWhere(['or',['level2_interest_id'=>$id_array],['level3_interest_id'=>$id_array]])
		//->andWhere(['status' => \app\models\SearchRequest::STATUS_ACTIVE])
		->count();
		
		
        $data['birthDayList']=Helper::assocToRecords(Helper::getDaysList());
        $data['birthMonthList']=Helper::assocToRecords(Helper::getMonthsList());
        $data['birthYearList']=Helper::assocToRecords(Helper::getYearsList(0,1));
        $data['hoursList']=Helper::assocToRecords(Helper::getHoursList());
        $data['minutesList']=Helper::assocToRecords(Helper::getMinutesList());

        $data['countries']=Helper::getCountriesList();
		$data['countRequestInterests'] = $searchInterestCount;


        return $data;
    }

    public function actionSave() {

        $data=Yii::$app->request->getBodyParams()['searchRequest'];
        $draftId=Yii::$app->request->getBodyParams()['draftId'];

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        if ($data['id']) {
            //$searchRequest=$this->findModel($data['id']);
        } else {
            $searchRequest=new SearchRequest;
            $searchRequest->user_id=Yii::$app->user->id;
            $searchRequest->create_dt=(new EDateTime)->sqlDateTime();
        }

        $searchRequest->setScenario('save');

        $searchRequest->load($data,'');

        if (implode('',$data['active_till_parts'])!=='') {
            $searchRequest->active_till=implode('-',[
                $data['active_till_parts']['year'],
                ($data['active_till_parts']['month']<10 ? '0':'').$data['active_till_parts']['month'],
                ($data['active_till_parts']['day']<10 ? '0':'').$data['active_till_parts']['day']
            ]);

            if($searchRequest->active_till < date('Y-m-d')) {
                $errors[]=Yii::t('app','Das eingegebene "Aktiv bis" - Datum darf nicht in der Vergangenheit liegen');
            }

        } else {
            //$searchRequest->active_till=null;
            $searchRequest->active_till=(new EDateTime())->modify('+6 months')->sqlDate();
        }

        if (!$data['is_active_immediately'] && implode('',$data['scheduled_dt_parts'])!=='') {
            $scheduled_date=implode('-',[
                $data['scheduled_dt_parts']['year'],
                ($data['scheduled_dt_parts']['month']<10 ? '0':'').$data['scheduled_dt_parts']['month'],
                ($data['scheduled_dt_parts']['day']<10 ? '0':'').$data['scheduled_dt_parts']['day']
            ]);

            $scheduled_time=implode(':',[
                ($data['scheduled_dt_parts']['hours']<10 ? '0':'').$data['scheduled_dt_parts']['hours'],
                ($data['scheduled_dt_parts']['minutes']<10 ? '0':'').$data['scheduled_dt_parts']['minutes'],
                '00'
            ]);

            $searchRequest->scheduled_dt = $scheduled_date.' '.$scheduled_time;
        }


        if ($data['searchRequestInterests'][0]['level2Interest']['id']) {
            $searchRequest->search_request_bonus = $data['searchRequestInterests'][0]['level2Interest']['search_request_bonus'];
        } elseif ($data['searchRequestInterests'][0]['level1Interest']['id']) {
            $searchRequest->search_request_bonus = $data['searchRequestInterests'][0]['level1Interest']['search_request_bonus'];
        }


        if ($searchRequest->validate()) {
            if ($searchRequest->level1InterestSpamReportsLimitReached($data['searchRequestInterests'][0]['level1Interest']['id'])) {
                $errors[]=Yii::t('app','Du kannst keine Suchaufträge in dieser Interessenkategorie erstellen, da sich andere Benutzer wg. Spam beschwert haben');
            } else {
                $searchRequest->save();

//                if ($searchRequest->bonus<=Yii::$app->user->identity->balance) {
//                    $searchRequest->save();
//                } else {
//                    $errors[]=Yii::t('app','Du hast nicht genug Jugls');
//                }
            }
        } else {
            $data['$errors']=$searchRequest->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        $ids=[];
        foreach($data['searchRequestInterests'] as $sri) {
            if ($sri['level3Interest']['id']) {
                $ids[] = $sri['level3Interest']['id'];
            }
        }

        if (empty($ids)) {
            $level2Id=$data['searchRequestInterests'][0]['level2Interest']['id'];
            $level1Id=$data['searchRequestInterests'][0]['level1Interest']['id'];
            if ($level2Id) {
                $ids[]=$level2Id;
            } elseif ($level1Id) {
                $ids[]=$level1Id;
            }
        }

        if (!$this->parseIds(implode(',',$ids),$level1Interest,$level2Interest,$level3Interests)) {
            array_unshift($errors, Yii::t('app','Kategorie darf nicht leer sein'));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['searchRequest'=>$data];
        }

        $searchRequest->relinkFilesWithSortOrder($data['files'],'files','searchRequestFiles');

        // save param values
        foreach($searchRequest->searchRequestParamValues as $pv) {
            $pv->delete();
        }

        foreach($data['searchRequestParamValues'] as $param) {
            $srpv=new SearchRequestParamValue();
            $srpv->search_request_id=$searchRequest->id;
            $srpv->param_id=$param['param_id'];
            $srpv->param_value_id=$param['param_value_id'];
            $srpv->param_value=$param['param_value'];
            if ($srpv->param->required && $srpv->param_value_id.$srpv->param_value=='') {
                $errors[]=Yii::t('app','{param} darf nicht leer sein.',['param'=>$srpv->param->title]);
            }
            $srpv->save();
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['searchRequest'=>$data];
        }

        // save links to interests
        foreach($searchRequest->searchRequestInterests as $sri) {
            $sri->delete();
        }

        if ($this->parseIds(implode(',',$ids),$level1Interest,$level2Interest,$level3Interests)) {
            foreach($level3Interests as $level3Interest) {
                $sri = new SearchRequestInterest();
                $sri->search_request_id = $searchRequest->id;
                $sri->level1_interest_id = $level1Interest->id;
                $sri->level2_interest_id = $level2Interest->id;
                $sri->level3_interest_id = $level3Interest->id;

                $sri->save();
            }
        }

        $searchRequest->afterInsert();

        Yii::$app->user->identity->packetCanBeSelected();

        if(!empty($draftId)) {
            SearchRequestDraft::deleteDraft($draftId);
        }

        $trx->commit();
        return ['result'=>true,'willBeValidated'=>$searchRequest->status==\app\models\SearchRequest::STATUS_AWAITING_VALIDATION];

    }

    public function actionDetails($id) {
        $model=SearchRequest::find()->andWhere('id=:id', [':id'=>$id])->with([
            'searchRequestInterests',
            'searchRequestInterests.level1Interest',
            'searchRequestInterests.level2Interest',
            'searchRequestInterests.level3Interest',
            'searchRequestParamValues',
            'searchRequestParamValues.param',
            'files',
            'user',
            'user.avatarFile',
            'searchRequestMyFavorites',
            'country'
        ])->one();

        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $data=$model->toArray(['id','title','description','price_from','price_to','bonus','zip','city','address','status', 'search_request_type','provider_id']);
		
		if($model->search_request_type==SearchRequest::SEARCH_REQUEST_TYPE_EXTERNAL_AD){		
			
			$providerModel=\app\models\AdvertisingSearchRequestProvider::find()->where('provider_id=:id',['id'=>$model->provider_id])->one();
			if(preg_match('/r=/',$data['description'])){	
				$data['description']=preg_replace('/r=/','r='.Yii::$app->user->identity->id.'|'.$id.'|'.$providerModel->auth_token,$data['description']);
			}
		}

        if (count($model->searchRequestInterests)>0) {
            $data['level1Interest']=strval($model->searchRequestInterests[0]->level1Interest);
            $data['level2Interest']=strval($model->searchRequestInterests[0]->level2Interest);

            $level3Interests=[];
            foreach($model->searchRequestInterests as $sri) {
                $level3Interests[]=$sri->level3Interest;
            }
            $data['level3Interests']=implode(', ',$level3Interests);
        }

        $data['country'] = $model->country->country;
        $data['favorite']=count($model->searchRequestMyFavorites)>0;

        $data['user']=$model->user->getShortData(['rating', 'feedback_count', 'packet']);
        $data['images']=[];

        foreach($model->files as $image) {
            $data['images'][]=$image->getThumbUrl('searchRequest');
        }

        if (empty($data['images'])) {
            $data['images']=[\app\components\Thumb::createUrl('/static/images/account/default_interest.png','searchRequest')];
        }

        foreach($model->files as $image) {
            $data['bigImages'][]=$image->getThumbUrl('fancybox');
        }

        $data['paramValues']=[];
        foreach($model->searchRequestParamValues as $pv) {
            $data['paramValues'][]=[
                'title'=>strval($pv->param),
                'value'=>$pv->param->type==\app\models\Param::TYPE_LIST ? strval($pv->paramValue):$pv->param_value
            ];
        }

        $spamReport=new \app\models\UserSpamReport();
        $spamReport->user_id=Yii::$app->user->id;
        $spamReport->setSearchRequestObject($model);
        $data['spamReported']=!$spamReport->validate(['object']);

        return [
            'searchRequest'=>$data,
            'comments'=>SearchRequestComment::getComments($id)
        ];
    }

    protected function findModel($id)
    {
        if (($model = SearchRequest::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDelete() {
        return Yii::$app->db->transaction(function($db){
            $model=SearchRequest::find()->andWhere(['id'=>Yii::$app->request->getBodyParams()['id'],'user_id'=>Yii::$app->user->id])->one();

            if (!$model) {
                throw new \yii\web\NotFoundHttpException();
            }

            $model->status=\app\models\SearchRequest::STATUS_DELETED;
            $model->save();
            foreach($model->searchRequestOffers as $offer) {
                if ($offer->status!=\app\models\SearchRequestOffer::STATUS_ACCEPTED) {
                    $offer->status=\app\models\SearchRequestOffer::STATUS_REJECTED;
                    $offer->save();
                }
            }

            return ['result'=>true];
        });
    }

    public function actionUndelete() {
        return Yii::$app->db->transaction(function($db){
            $model=SearchRequest::find()->where(['id'=>Yii::$app->request->getBodyParams()['id'],'user_id'=>Yii::$app->user->id])->one();

            if (!$model) {
                throw new \yii\web\NotFoundHttpException();
            }

            $model->undelete();

            return ['result'=>true,'status'=>$model->status];
        });
    }

    public function actionUnlink() {
        return Yii::$app->db->transaction(function($db){
            $model=SearchRequest::find()->where(['id'=>Yii::$app->request->getBodyParams()['id'],'user_id'=>Yii::$app->user->id])->one();

            if (!$model) {
                throw new \yii\web\NotFoundHttpException();
            }

            $result=$model->deleteUnlink();

            return ['result'=>$result];
        });
    }

    public function actionClose() {
        return Yii::$app->db->transaction(function($db){
            $model=SearchRequest::find()->andWhere(['id'=>Yii::$app->request->getBodyParams()['id'],'user_id'=>Yii::$app->user->id])->one();

            if (!$model) {
                throw new \yii\web\NotFoundHttpException();
            }

            $model->status=\app\models\SearchRequest::STATUS_CLOSED;
            $model->save();

            return ['result'=>true];
        });
    }

    public function actionListComments($searchRequestId, $pageNum) {
        return [
            'comments'=>SearchRequestComment::getComments($searchRequestId,$pageNum)
        ];
    }

}
