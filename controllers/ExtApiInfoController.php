<?php

namespace app\controllers;

use Couchbase\RegexpSearchQuery;
use Yii;
use app\models\Info;
use app\models\InfoComment;
use app\models\InfoCommentVote;


class ExtApiInfoController extends \app\components\ExtApiController {

    public function actionGetView($view) {
        $info = Info::find()->where(['view'=>$view])->one();
        $dataInfo = $info->toArray();

        $data = $info->toArray(['id']);
        if(!empty($dataInfo['title_'.Yii::$app->language])) {
            $data['title'] = $dataInfo['title_'.Yii::$app->language];
        } else {
            $data['title'] = $dataInfo['title_de'];
        }

        if(!empty($dataInfo['description_'.Yii::$app->language])) {
            $data['description'] = $dataInfo['description_'.Yii::$app->language];
        } else {
            $data['description'] = $dataInfo['description_de'];
        }

        $countryList = InfoComment::getCountryList($info->id);
        $currentCountry = [];
        foreach ($countryList as $itemCountry) {
            if($itemCountry['id']==Yii::$app->user->identity->country_id) {
                $currentCountry[] = $itemCountry;
                break;
            }
        }

        return [
            'info'=>$data,
            'infoComments'=>$this->getComments($info->id, Yii::$app->user->identity->country_id),
            'countryList'=>InfoComment::getCountryList($info->id),
            'currentCountry'=>$currentCountry
        ];
    }

    private function getComments($info_id,$country_id,$sort='votes_up',$pageNum=1) {
        $perPage = 10;
        $query=InfoComment::find()
            ->joinWith(['user'])
            ->where(['info_comment.info_id'=>$info_id]);

        if($country_id) {
            $query->andWhere(['info_comment.lang'=>$country_id]);
        }

        if (!Yii::$app->user->identity->is_moderator) {
            $query->andWhere(['info_comment.status'=>InfoComment::STATUS_ACTIVE]);
        }

        switch ($sort) {
            case 'dt':
                $query->orderBy(['info_comment.id'=>SORT_DESC]);
                break;
            case 'votes_up':
                $query->orderBy(['info_comment.votes_up'=>SORT_DESC]);
                break;
        }

        $query->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $data = [];
        $infoComments=$query->all();
        $hasMore=count($infoComments)>$perPage;

        foreach(array_slice($infoComments,0,$perPage) as $item) {
            $data[]=$item->getFrontInfo();
        }

        return [
            'items'=>$data,
            'hasMore'=>$hasMore,
        ];
    }

    public function actionAddComment() {
        $data = Yii::$app->request->getBodyParam('infoComment');
        $infoData = Yii::$app->request->getBodyParam('infoPopupData');
        $country_id = Yii::$app->request->getBodyParam('country_id');

        $id = $infoData['id'];
        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $model=new InfoComment();
        $model->user_id=Yii::$app->user->id;
        $model->info_id = $id;
        $model->dt=(new \app\components\EDateTime())->sqlDateTime();
        $model->lang = $country_id;

        $model->load($data,'');
        $model->file_id=\app\models\File::getIdFromProtected($data['file_id']);

        if ($model->validate()) {
            $model->save();
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['infoComment'=>$data];
        }

        $trx->commit();

        return [
            'result'=>true,
            'infoComment'=>$data,
            'infoComments'=>$this->getComments($id, $country_id),
            'countryList'=>InfoComment::getCountryList($id, true)
        ];
    }

    public function actionListComments($info_id, $country_id=false, $sort, $pageNum) {
        return $this->getComments($info_id, $country_id, $sort, $pageNum);
    }

    public function actionVoteComment() {
        $id=Yii::$app->request->getBodyParam('id');
        $vote=Yii::$app->request->getBodyParam('vote')>0 ? 1:-1;

        $trx=Yii::$app->db->beginTransaction();

        $model=\app\models\InfoCommentVote::findOne(['info_comment_id'=>$id,'user_id'=>Yii::$app->user->id]);

        if (!$model) {
            $model=new \app\models\InfoCommentVote();
            $model->user_id=Yii::$app->user->id;
            $model->info_comment_id=$id;
            $model->vote=$vote;
            $model->dt=(new \app\components\EDateTime)->sqlDateTime();
            $model->save();

            \app\models\InfoComment::updateAllCounters(['votes_up'=>$vote>0 ? 1:0,'votes_down'=>$vote<0 ? 1:0],['id'=>$id]);
        } else {
            $votes_up=$model->vote==1 ? -1:0;
            $votes_down=$model->vote==-1 ? -1:0;
            $model->vote=$vote;
            $model->save();
            if ($model->vote>0) {
                $votes_up++;
            } else {
                $votes_down++;
            }

            \app\models\InfoComment::updateAllCounters(['votes_up'=>$votes_up,'votes_down'=>$votes_down],['id'=>$id]);
        }

        $model=\app\models\InfoComment::findOne($id);

        if ($model && $model->user_id==Yii::$app->user->id) {
            return ['result'=>Yii::t('app','Du kannst keine Stimme für Deine eigene Nachricht abgeben')];
        }

        $trx->commit();

        return ['result'=>Yii::t('app','Deine Stimme wurde abgegeben'),'comment'=>$model->getFrontInfo()];
    }

    private function getVotesComment($id,$pageNum=1) {
        $perPage = 20;
        $query=InfoCommentVote::find()
            ->with(['user'])
            ->where(['info_comment_id'=>$id]);

        $query->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $data = [];
        $votes=$query->all();
        $hasMore=count($votes)>$perPage;

        foreach(array_slice($votes,0,$perPage) as $item) {
            $data[] = [
                'info_comment_id'=>$item->info_comment_id,
                'vote'=>$item->vote,
                'user'=>[
                    'id'=>$item->user->id,
                    'first_name'=>$item->user->first_name,
                    'last_name'=>$item->user->last_name,
                    'is_company_name'=>$item->user->is_company_name,
                    'company_name'=>$item->user->company_name,
                    'rating'=>$item->user->rating,
                    'feedback_count'=>$item->user->feedback_count,
                    'avatar'=>$item->user->getAvatarThumbUrl('avatarMobile')
                ]
            ];
        }

        return [
            'log'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ],
            'comment_id'=>$id
        ];
    }

    public function actionListVotesComment($id,$pageNum) {
        return $this->getVotesComment($id,$pageNum);
    }

    public function actionVotesComment($id) {
        return $this->getVotesComment($id);
    }

    public function actionAcceptComment() {
        $id=Yii::$app->request->getBodyParam('id');
        return \app\components\Moderator::acceptInfoComment($id);
    }

    public function actionRejectComment() {
        $id=Yii::$app->request->getBodyParam('id');
        return \app\components\Moderator::rejectInfoComment($id);
    }


}