<?php

namespace app\controllers;

use app\components\EDateTime;
use app\components\Helper;
use app\models\Interest;
use app\models\SearchRequestDraft;
use Yii;
use yii\web\NotFoundHttpException;

class ApiSearchRequestDraftController extends \app\components\ApiController {

    private function draftList($pageNum=1) {
        $perPage=20;

        $query = SearchRequestDraft::find()
            ->with(['user'])
            ->where(['user_id'=>Yii::$app->user->id])
            ->orderBy(['create_dt'=>SORT_DESC])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $drafts = $query->all();

        $hasMore=count($drafts)>$perPage;
        $data = [];
        foreach(array_slice($drafts,0,$perPage) as $draft) {
            $offerData = json_decode($draft->data, true);
            $idata = $offerData;

            $idata['searchRequestInterests'] = $offerData['searchRequestInterests'];

            if (count($offerData['searchRequestInterests'])>0) {
                $idata['level1Interest']=$offerData['searchRequestInterests'][0]['level1Interest']['title'];
                $idata['level2Interest']=$offerData['searchRequestInterests'][0]['level2Interest']['title'];

                $level3Interests=[];
                foreach($offerData['searchRequestInterests'] as $sri) {
                    $level3Interests[]=$sri['level3Interest']['title'];
                }
                $idata['level3Interests']=implode(', ',$level3Interests);
            }

            if (count($offerData['files'])>0) {
                $idata['image'] = \app\components\Thumb::createUrl($offerData['files'][0]['link'],'searchRequestMobile',true);
            } else {
                $idata['image']=\app\components\Thumb::createUrl('/static/images/account/default_interest.png','searchRequestMobile',true);
            }
            $idata['id']=$draft->id;
            $idata['user']=$draft->user->getShortData();
            $idata['create_dt'] = (new EDateTime($offerData['create_dt']))->js();
            $data[]=$idata;
        }

        return [
            'results'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ]
        ];

    }


    public function actionList($pageNum) {
        return $this->draftList($pageNum);
    }

    public function actionIndex() {
        return $this->draftList();
    }

    public function actionSave() {
        $data=Yii::$app->request->getBodyParams()['searchRequest'];

        unset($data['$allErrors']);
        unset($data['$errors']);
        unset($data['saving']);

        $model = new SearchRequestDraft();
        $model->user_id = Yii::$app->user->id;
        $model->data = json_encode($data);
        $model->create_dt = (new EDateTime())->sqlDateTime();
        $model->save();

        return [
            'result'=>true,
            'id'=>$model->id
        ];

    }

    public function actionUpdate() {
        $id = Yii::$app->request->getBodyParams()['id'];
        $searchRequest = Yii::$app->request->getBodyParams()['searchRequest'];
        $model = SearchRequestDraft::findOne($id);

        if(!$model) {
            throw new NotFoundHttpException();
        }

        unset($searchRequest['$allErrors']);
        unset($searchRequest['$errors']);
        unset($searchRequest['saving']);

        $model->data = json_encode($searchRequest);
        $model->save();

        return [
            'return'=>true,
            'id'=>$model->id
        ];
    }

    public function actionDelete() {
        $id = Yii::$app->request->getBodyParams()['id'];
        SearchRequestDraft::deleteDraft($id);
        return ['result'=>true];
    }


    public function actionGet($id, $ids) {

        $model = SearchRequestDraft::findOne($id);

        if(!$model) {
            throw new NotFoundHttpException();
        }

        $data=[];
        $data['searchRequest']=json_decode($model->data, true);
        $data['searchRequest']['draft_id']=$model->id;

        $data['birthDayList']=Helper::assocToRecords(Helper::getDaysList());
        $data['birthMonthList']=Helper::assocToRecords(Helper::getMonthsList());
        $data['birthYearList']=Helper::assocToRecords(Helper::getYearsList(0,1));
        $data['countries']=Helper::getCountriesList();

        if(!empty($ids)) {
            $this->parseIds($ids,$level1Interest,$level2Interest,$level3Interests);
            $data['searchRequest']['searchRequestInterests']=[];

            foreach($level3Interests as $interest) {
                $data['searchRequest']['searchRequestInterests'][]=[
                    'level1Interest'=>$level1Interest->getShortData(),
                    'level2Interest'=>$level2Interest->getShortData(),
                    'level3Interest'=>$interest->getShortData(),
                ];
            }
        }

        return $data;

    }

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

}
