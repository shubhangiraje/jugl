<?php

namespace app\controllers;

use Yii;
use app\models\OfferFavorite;
use app\models\SearchRequestFavorite;
use app\models\Country;
use yii\db\Query;
use app\components\EDateTime;
use yii\web\NotFoundHttpException;



class ApiFavoritesController extends \app\components\ApiController {


    private function search($filter=[],$pageNum=1) {
        $perPage=50;

        $squery = (new Query)->select(['type'=>"('search_request')", 'search_request_id as id'])
            ->from('search_request_favorite')
            ->where(['user_id'=>Yii::$app->user->id])
            ->union(
                (new Query)->select(['type'=>"('offer')", 'offer_id as id'])
                    ->from('offer_favorite')
                    ->where(['user_id'=>Yii::$app->user->id])
                ,true);

        $query=(new Query)
            ->from(['items'=>$squery])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $query->andFilterWhere(['type'=>$filter['type']]);
        $query->orderBy(['id'=>SORT_DESC]);

        $favorites=$query->all();
        $hasMore=count($favorites)>$perPage;
        $favorites=array_slice($favorites,0,$perPage);

        $idsByTypes=[];
        foreach($favorites as $favorite) {
            $idsByTypes[$favorite['type']][]=$favorite['id'];
        }

        $valsByTypesAndIds=[];

        $valsByTypesAndIds['search_request']=$this->getSearchRequestData($idsByTypes['search_request']);
        $valsByTypesAndIds['offer']=$this->getOfferData($idsByTypes['offer']);

        $data=[];
        foreach($favorites as $favorite) {
            if (isset($valsByTypesAndIds[$favorite['type']][$favorite['id']])) {
                $favoriteData=$valsByTypesAndIds[$favorite['type']][$favorite['id']];
                $favoriteData['type']=$favorite['type'];
                $data[]=$favoriteData;
            }
        }

        return [
            'results'=>[
                'items'=>$data,
                'hasMore'=>$hasMore,
                'filter' => $filter['type']
            ]
        ];

    }


    private function getFavoriteData($model,$interests) {
        if (!$model) return [];
        $data=$model->toArray(['id','title','price_from','price_to','price','delivery_days','view_bonus','buy_bonus']);

        $data['create_dt']=(new EDateTime($model->create_dt))->js();
        $data['user']=$model->user->getShortData(['rating', 'feedback_count', 'packet', 'country_id']);
		
		/* NVII-MEDIA - Output Flag */
		$flagAry = Country::getListShort();
		$data['user']['flag'] = $flagAry[$data['user']['country_id']];
		/* NVII-MEDIA - Output Flag */

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


    private function getSearchRequestData($ids) {
        if (!is_array($ids) || empty($ids)) return;

        $models=SearchRequestFavorite::find()
            ->andWhere(['search_request_id'=>$ids])
            ->with(['user', 'searchRequest'])
            ->all();

        $data=[];

        foreach($models as $model) {
            $idata=[
                'id'=>$model->search_request_id,
                'favorite'=>$this->getFavoriteData($model->searchRequest, $model->searchRequest->searchRequestInterests),
            ];

            $data[$idata['id']]=$idata;
        }

        return $data;
    }

    private function getOfferData($ids) {
        if (!is_array($ids) || empty($ids)) return;

        $models=OfferFavorite::find()
            ->andWhere(['offer_id'=>$ids])
            ->with(['user', 'offer'])
            ->all();

        $data=[];

        foreach($models as $model) {
            $idata=[
                'id'=>$model->offer_id,
                'favorite'=>$this->getFavoriteData($model->offer, $model->offer->offerInterests),
            ];

            $data[$idata['id']]=$idata;
        }

        return $data;
    }


    public function actionDelete() {
        return Yii::$app->db->transaction(function($db) {
            switch (Yii::$app->request->getBodyParams()['type']) {
                case 'search_request':
                    $model = SearchRequestFavorite::findOne(['user_id'=>Yii::$app->user->id, 'search_request_id' => Yii::$app->request->getBodyParams()['id']]);
                    if ($model) {
                        $model->delete();
                    }
                    return ['result'=>true];
                    break;

                case 'offer':
                    $model = OfferFavorite::findOne(['user_id'=>Yii::$app->user->id, 'offer_id' => Yii::$app->request->getBodyParams()['id']]);
                    if ($model) {
                        $model->delete();
                    }
                    return ['result'=>true];
                    break;

                default: return ['result'=>false];
            }
        });
    }

    public function actionAdd() {
        return Yii::$app->db->transaction(function($db) {
            switch (Yii::$app->request->getBodyParams()['type']) {
                case 'search_request':
                    $isFavorite = SearchRequestFavorite::find()->where(['user_id'=>Yii::$app->user->id, 'search_request_id'=>Yii::$app->request->getBodyParams()['id']])->one();
                    if(!$isFavorite) {
                        try {
                            $model = new SearchRequestFavorite();
                            $model->user_id = Yii::$app->user->id;
                            $model->search_request_id = Yii::$app->request->getBodyParams()['id'];
                            $model->save();
                        } catch (\Exception $e) {

                        }
                    }
                    return ['result'=>true];
                    break;
                case 'offer':
                    $isFavorite = OfferFavorite::find()->where(['user_id'=>Yii::$app->user->id, 'offer_id'=>Yii::$app->request->getBodyParams()['id']])->one();
                    if(!$isFavorite) {
                        try {
                            $model = new OfferFavorite();
                            $model->user_id = Yii::$app->user->id;
                            $model->offer_id = Yii::$app->request->getBodyParams()['id'];
                            $model->save();
                        } catch (\Exception $e) {

                        }
                    }
                    return ['result'=>true];
                    break;

                default: return ['result'=>false];
            }
        });
    }


    public function actionSearch($filter,$pageNum) {
        return $this->search(json_decode($filter,true),$pageNum);
    }

    public function actionIndex() {
        return $this->search();
    }


}