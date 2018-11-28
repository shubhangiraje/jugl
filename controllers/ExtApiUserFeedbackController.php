<?php

namespace app\controllers;

use Yii;
use app\components\EDateTime;
use app\models\UserFeedback;


class ExtApiUserFeedbackController extends \app\components\ExtApiController {

    public function actionUpdate() {

        if ($_REQUEST['id']) {
            $model = \app\models\UserFeedback::findOne($_REQUEST['id']);
        }

        if ($_REQUEST['search_request_offer_id']) {
            $sro= \app\models\SearchRequestOffer::findOne($_REQUEST['search_request_offer_id']);

            if ($sro->searchRequest->user_id!=Yii::$app->user->id) {
                throw new \yii\web\NotFoundHttpException();
            }

            $model=$sro->userFeedback;
            if (!$model) {
                $model=new \app\models\UserFeedback();
                $model->user_id=$sro->user_id;
                $model->second_user_id=$sro->searchRequest->user_id;
            }
        }

        if ($_REQUEST['offer_request_id']) {
            $or= \app\models\OfferRequest::findOne($_REQUEST['offer_request_id']);

            if ($or->offer->user_id!=Yii::$app->user->id) {
                throw new \yii\web\NotFoundHttpException();
            }

            $model=$or->userFeedback;
            if (!$model) {
                $model=new \app\models\UserFeedback();
                $model->user_id=$or->user_id;
                $model->second_user_id=$or->offer->user_id;
            }
        }

        if (!$model || $model->second_user_id != Yii::$app->user->id) {
            throw new \yii\web\NotFoundHttpException();
        }

        $data=$model->toArray(['id','rating','feedback']);

        if ($sro) {
            $data['search_request_offer_id']=$sro->id;
        }

        if ($or) {
            $data['offer_request_id']=$or->id;
        }

        return [
            'feedback'=>$data
        ];
    }

    public function actionSave() {
        $data=Yii::$app->request->getBodyParams()['feedback'];

        $model=\app\models\UserFeedback::findOne($data['id']);

        if ($data['search_request_offer_id']) {
            $sro= \app\models\SearchRequestOffer::findOne($data['search_request_offer_id']);

            if ($sro->searchRequest->user_id!=Yii::$app->user->id) {
                throw new \yii\web\NotFoundHttpException();
            }

            $model=$sro->userFeedback;
            if (!$model) {
                $model=new \app\models\UserFeedback();
                $model->user_id=$sro->user_id;
                $model->second_user_id=$sro->searchRequest->user_id;
                $model->create_dt=(new EDateTime())->sqlDateTime();
            }
        }

        if ($data['offer_request_id']) {
            $or= \app\models\OfferRequest::findOne($data['offer_request_id']);

            if ($or->offer->user_id!=Yii::$app->user->id) {
                throw new \yii\web\NotFoundHttpException();
            }

            $model=$or->userFeedback;
            if (!$model) {
                $model=new \app\models\UserFeedback();
                $model->user_id=$or->user_id;
                $model->second_user_id=$or->offer->user_id;
                $model->create_dt=(new EDateTime())->sqlDateTime();
            }
        }

        if (!$model || $model->second_user_id!=Yii::$app->user->id) {
            throw new \yii\web\NotFoundHttpException();
        }

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $model->setScenario('update');
        $model->load($data,'');

        if ($model->validate()) {
            $model->save();
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['feedback'=>$data];
        }

        if ($sro) {
            $sro->user_feedback_id=$model->id;
            $sro->save();
        }

        if ($or) {
            $or->user_feedback_id=$model->id;
            $or->save();
        }

        if (count($model->searchRequestOffers)>0) {
            \app\models\UserEvent::addDealFeedback($model->searchRequestOffers[0]);
            $events=\app\models\UserEvent::find()->where(['user_id'=>$model->searchRequestOffers[0]->searchRequest->user_id,'type'=>\app\models\UserEvent::TYPE_SEARCH_REQUEST_FEEDBACK_NOTIFICATION])
                ->andWhere('text like(:text)',[':text'=>"%[searchRequestOfferFeedback:{$model->searchRequestOffers[0]->id}%"])->all();
        }

        if (count($model->offerRequests)>0) {
            \app\models\UserEvent::addDealFeedback($model->offerRequests[0]);
            $events=\app\models\UserEvent::find()->where(['user_id'=>$model->offerRequests[0]->offer->user_id,'type'=>\app\models\UserEvent::TYPE_OFFER_FEEDBACK_NOTIFICATION])
                ->andWhere('text like(:text)',[':text'=>"%[offerRequestFeedback:{$model->offerRequests[0]->id}%"])->all();
        }

        foreach($events as $event) {
            $event->text=preg_replace('%\[(searchRequestOfferFeedback|offerRequestFeedback):\d+\]%',Yii::t('app','Bereits bewertet.'),$event->text);
            $event->save();
        }

        $trx->commit();

        return ['result'=>true,'events'=>\app\models\UserEvent::getFrontData($events)];
    }

    public function actionResponseUpdate() {

        $model=UserFeedback::findOne(['id'=>Yii::$app->request->getQueryParam('id'),'user_id'=>Yii::$app->user->id]);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException();
        }

        $data=$model->toArray(['id','response']);

        return [
            'feedback'=>$data
        ];
    }

    public function actionResponseSave() {
        $data=Yii::$app->request->getBodyParams()['feedback'];

        $model=UserFeedback::findOne(['id'=>$data['id'],'user_id'=>Yii::$app->user->id]);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException();
        }

        $model->response_dt=(new EDateTime())->sqlDateTime();

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $model->setScenario('response-update');
        $model->load($data,'');

        if ($model->validate()) {
            //\app\models\UserEvent::addTeamFeedback($model);
            $model->save();
            /*
            $events=\app\models\UserEvent::find()->where(['user_id'=>Yii::$app->user->identity,'type'=>\app\models\UserEvent::TYPE_TEAM_CHANGE])
                ->andWhere('text like(:text)',[':text'=>'%[teamleaderFeedback%'])->all();
            foreach($events as $event) {
                $event->text=preg_replace('%\[teamleaderFeedback:\d+\]%',Yii::t('app','Sie haben Teamleading bewertet.'),$event->text);
                $event->save();
            }
            */
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['feedback'=>$data];
        }

        $trx->commit();

        $feedbackData=$model->toArray(['response','id']);
        $feedbackData['response_dt']=(new EDateTime())->js();

        return ['result'=>true,'feedback'=>$feedbackData];
    }


    public function actionCounterUpdate() {

        if ($_REQUEST['id']) {
            $model = \app\models\UserFeedback::findOne($_REQUEST['id']);
        }

        if ($_REQUEST['search_request_offer_id']) {
            $sro= \app\models\SearchRequestOffer::findOne($_REQUEST['search_request_offer_id']);

            if ($sro->user_id!=Yii::$app->user->id) {
                throw new \yii\web\NotFoundHttpException();
            }

            $model=$sro->counterUserFeedback;
            if (!$model) {
                $model=new \app\models\UserFeedback();
                $model->user_id=$sro->searchRequest->user_id;
                $model->second_user_id=$sro->user_id;
            }
        }

        if ($_REQUEST['offer_request_id']) {
            $or= \app\models\OfferRequest::findOne($_REQUEST['offer_request_id']);

            if ($or->user_id!=Yii::$app->user->id) {
                throw new \yii\web\NotFoundHttpException();
            }

            $model=$or->counterUserFeedback;
            if (!$model) {
                $model=new \app\models\UserFeedback();
                $model->user_id=$or->offer->user_id;
                $model->second_user_id=$or->user_id;
            }
        }

        if (!$model || $model->second_user_id != Yii::$app->user->id) {
            throw new \yii\web\NotFoundHttpException();
        }

        $data=$model->toArray(['id','rating','feedback']);

        if ($sro) {
            $data['search_request_offer_id']=$sro->id;
        }

        if ($or) {
            $data['offer_request_id']=$or->id;
        }

        return [
            'feedback'=>$data
        ];
    }

    public function actionCounterSave() {
        $data=Yii::$app->request->getBodyParams()['feedback'];

        $model=\app\models\UserFeedback::findOne($data['id']);

        if ($data['search_request_offer_id']) {
            $sro= \app\models\SearchRequestOffer::findOne($data['search_request_offer_id']);

            if ($sro->user_id!=Yii::$app->user->id) {
                throw new \yii\web\NotFoundHttpException();
            }

            $model=$sro->counterUserFeedback;
            if (!$model) {
                $model=new \app\models\UserFeedback();
                $model->user_id=$sro->searchRequest->user_id;
                $model->second_user_id=$sro->user_id;
                $model->create_dt=(new EDateTime())->sqlDateTime();
            }
        }

        if ($data['offer_request_id']) {
            $or= \app\models\OfferRequest::findOne($data['offer_request_id']);

            if ($or->user_id!=Yii::$app->user->id) {
                throw new \yii\web\NotFoundHttpException();
            }

            $model=$or->counterUserFeedback;
            if (!$model) {
                $model=new \app\models\UserFeedback();
                $model->user_id=$or->offer->user_id;
                $model->second_user_id=$or->user_id;
                $model->create_dt=(new EDateTime())->sqlDateTime();
            }
        }

        if (!$model || $model->second_user_id!=Yii::$app->user->id) {
            throw new \yii\web\NotFoundHttpException();
        }

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        $model->setScenario('update');
        $model->load($data,'');

        if ($model->validate()) {
            $model->save();
        } else {
            $data['$errors']=$model->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['feedback'=>$data];
        }

        if ($sro) {
            $sro->counter_user_feedback_id=$model->id;
            $sro->save();
        }

        if ($or) {
            $or->counter_user_feedback_id=$model->id;
            $or->save();
        }

        $events = [];
        if (count($model->counterSearchRequestOffers)>0) {
            \app\models\UserEvent::addDealCounterFeedback($model->counterSearchRequestOffers[0]);
            $events=\app\models\UserEvent::find()->where(['user_id'=>$model->counterSearchRequestOffers[0]->user_id,'type'=>\app\models\UserEvent::TYPE_SEARCH_REQUEST_FEEDBACK_NOTIFICATION])
                ->andWhere('text like(:text)',[':text'=>"%[searchRequestOfferCounterFeedback:{$model->counterSearchRequestOffers[0]->id}%"])->all();
        }

        if (count($model->counterOfferRequests)>0) {
            \app\models\UserEvent::addDealCounterFeedback($model->counterOfferRequests[0]);
            $events=\app\models\UserEvent::find()->where(['user_id'=>$model->counterOfferRequests[0]->user_id,'type'=>\app\models\UserEvent::TYPE_OFFER_FEEDBACK_NOTIFICATION])
                ->andWhere('text like(:text)',[':text'=>"%[offerRequestCounterFeedback:{$model->counterOfferRequests[0]->id}%"])->all();
        }

        foreach($events as $event) {
            $event->text=preg_replace('%\[(searchRequestOfferCounterFeedback|offerRequestCounterFeedback):\d+\]%',Yii::t('app','Bereits bewertet.'),$event->text);
            $event->save();
        }

        $trx->commit();

        return [
            'result'=>true,
            'events'=>\app\models\UserEvent::getFrontData($events)
        ];
    }

}