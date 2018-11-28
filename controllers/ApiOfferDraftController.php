<?php

namespace app\controllers;

use app\components\EDateTime;
use app\components\Helper;
use app\models\Interest;
use app\models\OfferDraft;
use Yii;
use yii\web\NotFoundHttpException;

class ApiOfferDraftController extends \app\components\ApiController {

    private function draftList($pageNum=1) {
        $perPage=20;

        $query = OfferDraft::find()
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

            $idata['offerInterests'] = $offerData['offerInterests'];

            if (count($offerData['offerInterests'])>0) {
                $idata['level1Interest']=$offerData['offerInterests'][0]['level1Interest']['title'];
                $idata['level2Interest']=$offerData['offerInterests'][0]['level2Interest']['title'];

                $level3Interests=[];
                foreach($offerData['offerInterests'] as $sri) {
                    $level3Interests[]=$sri['level3Interest']['title'];
                }
                $idata['level3Interests']=implode(', ',$level3Interests);
            }

            if (!empty($offerData['files'][0])) {
                $idata['image'] = \app\components\Thumb::createUrl($offerData['files'][0]['link'],'offerMobile',true);
            } else {
                $idata['image']=\app\components\Thumb::createUrl('/static/images/account/default_interest.png','offerMobile',true);
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
        $data=Yii::$app->request->getBodyParams()['offer'];

        unset($data['$allErrors']);
        unset($data['$errors']);
        unset($data['saving']);

        $model = new OfferDraft();
        $model->user_id = Yii::$app->user->id;
        $model->data = json_encode($data);
        $model->create_dt = (new EDateTime())->sqlDateTime();
        $model->save();

        return [
            'result'=>true,
            'id'=>$model->id
        ];

    }

    public function actionDelete() {
        $id = Yii::$app->request->getBodyParams()['id'];
        OfferDraft::deleteDraft($id);
        return ['result'=>true];
    }

    private function parseIds($idsStr,&$level1Interest,&$level2Interest,&$level3Interests) {
        if ($idsStr=='') {
            $idsStr=\app\models\Interest::COMMON_INTEREST_ID;
        }

        if ($idsStr=='') {
            $level1Interest=new Interest();
            $level2Interest=new Interest();
            $level3Interests=[new Interest()];
            return false;
        }

        $ids=explode(',',$idsStr);

        $level3Interests=Interest::find()->andWhere(['id'=>$ids])->with(['parent','parent.parent','params','params.paramValues','interestParamValues'])->all();

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
            $params=array_merge($params,$interest['params']);
            foreach($interest['interestParamValues'] as $ipv) {
                $paramsSelectedValues[$ipv['param_id']][]=$ipv['param_value_id'];
            }
        }

        $paramsSelectedValue=[];
        foreach($paramsSelectedValues as $paramId=>$values) {
            if (count(array_unique($values))==1) $paramsSelectedValue[$paramId]=$values[0];
        }
    }

    public function actionGet($id, $ids) {
        $model = OfferDraft::findOne($id);

        if(!$model) {
            throw new NotFoundHttpException();
        }

        $data=[];
        $data['offer']=json_decode($model->data, true);
        $data['offer']['draft_id']=$model->id;
        $data['SELLBONUS_SELLER_PARENTS_PERCENT']=\app\models\Setting::get('SELLBONUS_SELLER_PARENTS_PERCENT');

        $data['birthDayList']=Helper::assocToRecords(Helper::getDaysList());
        $data['birthMonthList']=Helper::assocToRecords(Helper::getMonthsList());
        $data['birthYearList']=Helper::assocToRecords(Helper::getYearsList(0,1));

        $empty_option=array(0 => '');
        $data['countries'] = Helper::getCountriesList();
        $data['countries']= array_merge($empty_option, $data['countries']);


        if(!empty($ids)) {
            $this->parseIds($ids,$level1Interest,$level2Interest,$level3Interests);

            $data['offer']['offerInterests']=[];
            foreach($level3Interests as $interest) {
                $data['offer']['offerInterests'][]=[
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

            $data['offer']['offerParamValues']=[];
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
                $data['offer']['offerParamValues'][]=$pdata;
            }
        }


        $data['level1Interests']=[];
        $data['level1Interests'][] = '';
        foreach(Interest::find()->where('parent_id is null')->andWhere(['type'=>'OFFER'])->orderBy('sort_order')->all() as $interest) {
            $data['level1Interests'][]=$interest->toArray(['id','title']);
        }

        return $data;
    }

    public function actionUpdate() {
        $id = Yii::$app->request->getBodyParams()['id'];
        $offer = Yii::$app->request->getBodyParams()['offer'];

        unset($offer['$allErrors']);
        unset($offer['$errors']);
        unset($offer['saving']);

        $model = OfferDraft::findOne($id);

        if(!$model) {
            throw new NotFoundHttpException();
        }

        if ($offer['offerInterests'][0]['level2Interest']['id']) {
            $offer->offer_view_bonus = $offer['offerInterests'][0]['level2Interest']['offer_view_bonus'];
            $offer->offer_view_total_bonus = $offer['offerInterests'][0]['level2Interest']['offer_view_total_bonus'];
        } elseif ($offer['offerInterests'][0]['level1Interest']['id']) {
            $offer->offer_view_bonus = $offer['offerInterests'][0]['level1Interest']['offer_view_bonus'];
            $offer->offer_view_total_bonus = $offer['offerInterests'][0]['level1Interest']['offer_view_total_bonus'];
        }

        $model->data = json_encode($offer);
        $model->save();

        return [
            'return'=>true,
            'id'=>$model->id
        ];
    }





}
