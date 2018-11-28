<?php

namespace app\controllers;

use Yii;
use \yii\web\NotFoundHttpException;
use \app\models\Offer;
use \app\models\OfferRequest;
use app\components\EDateTime;


class ExtApiOfferRequestController extends \app\components\ExtApiController {

    public function actionBetGet() {
        $offer_id=Yii::$app->request->getQueryParam('offer_id');
        $offer_request_id=Yii::$app->request->getQueryParam('offer_request_id');

        $offerBet=new \app\models\OfferBetForm();

        if ($offer_request_id) {
            $offerRequest=\app\models\OfferRequest::findOne($offer_request_id);
            if (!$offerRequest || $offerRequest->user_id!=Yii::$app->user->id || !$offerRequest->betCanBeChanged) {
                throw new NotFoundHttpException();
            }
            $offerBet->setAttributes([
                'offer_id'=>$offerRequest->offer_id,
                'offer_request_id'=>$offerRequest->id,
                'price'=>$offerRequest->bet_price,
                'period'=>$offerRequest->bet_period,
                'description'=>$offerRequest->description
            ]);
        } else {
            $offer=\app\models\Offer::findOne($offer_id);
            if (!$offer || !$offer->canCreateRequest()) {
                throw new NotFoundHttpException();
            }
            $offerBet->offer_id=$offer->id;
        }

        return ['offerBet'=>$offerBet->attributes];
    }

    public function actionBet() {
        $data=Yii::$app->request->getBodyParams()['offerBet'];

        $errors=[];
        $data['$allErrors']=&$errors;

        $offerBet=new \app\models\OfferBetForm();

        $trx=Yii::$app->db->beginTransaction();

        $offerBet->load($data,'');

        if ($offerBet->validate()) {
            $previousBiggestBet=Yii::$app->db->createCommand("
              select max(bet_price)
              from offer_request
              where offer_id=:offer_id and status=:status_active and bet_active_till>NOW()
            ",[
                ':offer_id'=>$offerBet->offer_id,
                ':status_active'=>\app\models\OfferRequest::STATUS_ACTIVE
            ])->queryScalar();

            if ($previousBiggestBet-1e-6>$offerBet->price && !$offerBet->dont_check_price) {
                return ['offerBet'=>$data,'result'=>false,'price_is_not_best'=>true,'best_price'=>$previousBiggestBet];
            }

            if ($offerBet->offer_request_id) {
                $offerRequest = \app\models\OfferRequest::findOne($offerBet->offer_request_id);

                $offerRequestModification= new \app\models\OfferRequestModification();
                $offerRequestModification->offer_request_id=$offerRequest->id;
                $offerRequestModification->price=$offerRequest->bet_price;
                $offerRequestModification->dt=$offerRequest->bet_dt;
                $offerRequestModification->save();

                $offerRequest->modifications++;
            } else {
                $offerRequest = new \app\models\OfferRequest();
            }
            $offerRequest->offer_id=$offerBet->offer_id;
            $offerRequest->user_id=Yii::$app->user->id;
            $offerRequest->bet_price=$offerBet->price;
            $offerRequest->description=$offerBet->description;
            $offerRequest->bet_dt=(new \app\components\EDateTime())->sqlDateTime();
            $offerRequest->bet_period=$offerBet->period;
            $offerRequest->bet_active_till=(new \app\components\EDateTime())->modify("+ ".$offerBet->period)->sqlDateTime();

            $offerRequest->save();

            if ($previousBiggestBet>0 && $previousBiggestBet<$offerBet->price-1e-6) {
                $offerRequests=\app\models\OfferRequest::find()->andWhere("offer_id=:offer_id and status=:status_active and bet_active_till>NOW() and bet_price=:bet_price",[
                    ':offer_id'=>$offerBet->offer_id,
                    ':status_active'=>\app\models\OfferRequest::STATUS_ACTIVE,
                    ':bet_price'=>$previousBiggestBet
                ])->all();

                foreach($offerRequests as $ofr) {
                    if ($ofr->user_id!=$offerRequest->user_id) {
                        \app\models\UserEvent::addYourBetIsNowNotBest($ofr, $offerRequest);
                    }
                }
            }

            \app\models\UserEvent::addNewOfferMyBet($offerRequest,$offerRequestModification ? $offerRequestModification->price:null);
            if ($offerRequest->offer->notify_if_price_bigger<=$offerRequest->bet_price) {
                \app\models\UserEvent::addNewOfferBet($offerRequest,$offerRequestModification ? $offerRequestModification->price:null);
            }

        } else {
            $data['$errors']=$offerBet->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['offerBet'=>$data];
        }

        $trx->commit();
        return ['offerBet'=>$data,'result'=>true];
    }


    public function actionSave() {
        $data=Yii::$app->request->getBodyParams()['offerRequest'];

        $offer=Offer::find()->andWhere('id=:id and status=:status_active and active_till>=CAST(NOW() AS DATE)',[
            ':id'=>$data['offer_id'],
            ':status_active'=>Offer::STATUS_ACTIVE
        ])->one();

        if (!$offer) {
            return ['result'=>Yii::t('app','Es tut uns leid, das Angebot ist nicht mehr verfügbar')];
        }

        $errors=[];
        $data['$allErrors']=&$errors;

        $trx=Yii::$app->db->beginTransaction();

        if ($data['id']) {
            //$searchRequest=$this->findModel($data['id']);
        } else {
            $offerRequest=new OfferRequest;
            $offerRequest->user_id=Yii::$app->user->id;
            $offerRequest->offer_id=$offer->id;
        }

        $offerRequest->setScenario('save');

        $offerRequest->load($data,'');

        if ($offerRequest->validate()) {
            $offerRequest->save();
        } else {
            $data['$errors']=$offerRequest->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['offerRequest'=>$data,'result'=>implode(' ',$data['$errors'])];
        }

        if ($offer->type==\app\models\Offer::TYPE_AUTOSELL) {
            \app\models\UserEvent::addNewOfferRequest($offerRequest);
            \app\models\UserEvent::addOfferRequestAccepted($offerRequest);

            $offerRequest->pay_status=OfferRequest::PAY_STATUS_INVITED;
            $offerRequest->status=OfferRequest::STATUS_ACCEPTED;
            $offerRequest->save();

            if ($offerRequest->offer->amount!==null) {
                if ($offerRequest->offer->amount>0) {
                    $offerRequest->offer->amount=$offerRequest->offer->amount-1;
                    if ($offerRequest->offer->amount===0) {
                        $offerRequest->offer->status=\app\models\Offer::STATUS_EXPIRED;
                    }
                    $offerRequest->offer->save();
                } else {
                    return ['result' => Yii::t('app','Stückzahl darf nicht 0 sein')];
                }
            }

        } else {
            \app\models\UserEvent::addNewOfferRequest($offerRequest);
        }

        $trx->commit();

        return ['result'=>true,'offerRequest'=>$offerRequest->toArray(['id'])];
    }

    public function actionAccept()
    {
        $trx = Yii::$app->db->beginTransaction();

        $model = OfferRequest::find()->andWhere(['id' => Yii::$app->request->getBodyParams()['id']])->one();

        if (!$model || $model->offer->user_id != Yii::$app->user->id) {
            throw new \yii\web\NotFoundHttpException();
        }

        $data = Yii::$app->request->getBodyParams()['feedback'];
        $errors = [];
        $data['$allErrors'] =& $errors;

        $model->status = \app\models\OfferRequest::STATUS_ACCEPTED;
        $model->pay_status=\app\models\OfferRequest::PAY_STATUS_INVITED;
        $model->pay_tx_id=rand(100000,999999);
        $model->save();

        if ($model->isExpired) {
            return ['result' => Yii::t('app','Gebot ist nich verfügbar')];
        }

        if ($model->offer->amount!==null) {
            if ($model->offer->amount>0) {
                $model->offer->amount=$model->offer->amount-1;
                if ($model->offer->amount===0) {
                    $model->offer->status=\app\models\Offer::STATUS_EXPIRED;
                }
                $model->offer->save();
            } else {
                return ['result' => Yii::t('app','Stückzahl darf nicht 0 sein')];
            }
        }

        \app\models\UserEvent::addOfferRequestAccepted($model);

        $trx->commit();
        return ['result' => true];
    }

    public function actionPaymentComplaint($id) {
        $model=OfferRequest::findOne($id);

        if (!$model || $model->offer->user_id!=Yii::$app->user->id) {
            throw new \yii\web\NotFoundHttpException();
        }

        if ($model->status!=OfferRequest::STATUS_ACCEPTED || $model->pay_status==OfferRequest::PAY_STATUS_CONFIRMED || $model->payment_complaint) {
            throw new \yii\web\NotFoundHttpException();
        }

        $paymentComplaint=[
            'offerRequestId'=>$model->id,
            'text'=>Yii::t('app','Du hast das Angebot "{offerTitle}" innerhalb von 6 Werktagen nicht bezahlt. Du wirst hiermit abgemahnt und aufgefordert, das Angebot innerhalb von 3 Werktagen zu bezahlen. Nach Ablauf dieser Frist, habe ich, {name}, das Recht vom Handel zurückzutreten und/oder Schadensersatz einzufordern.',[
                'offerTitle'=>$model->offer->title,
                'name'=>$model->offer->user->getName()
            ])
        ];

        return [
            'paymentComplaint'=>$paymentComplaint
        ];
    }

    public function actionPaymentComplaintSave() {
        $trx=Yii::$app->db->beginTransaction();

        $paymentComplaint=Yii::$app->request->getBodyParam('paymentComplaint');
        $model=OfferRequest::findOne($paymentComplaint['offerRequestId']);

        if (!$model || $model->offer->user_id!=Yii::$app->user->id) {
            throw new \yii\web\NotFoundHttpException();
        }

        if ($model->status!=OfferRequest::STATUS_ACCEPTED || $model->pay_status==OfferRequest::PAY_STATUS_CONFIRMED || $model->payment_complaint) {
            throw new \yii\web\NotFoundHttpException();
        }

        $eventModels=\app\models\UserEvent::addOfferRequestPaymentComplaint($model,$paymentComplaint['text']);

        $model->user->updateCounters(['payment_complaints'=>1]);
        $model->user->refresh();
        $model->payment_complaint=1;
        $model->save();
        Yii::$app->mailer->sendEmail($model->user,'mahnung',['text'=>$paymentComplaint['text']]);
        if ($model->user->payment_complaints==3) {
            \app\models\UserEvent::addMahnungWarning($model);
            Yii::$app->mailer->sendEmail($model->user,'mahnung-warning');
        }
        if ($model->user->payment_complaints==4) {
            $model->user->block('mahnung-block');
        }

        $trx->commit();

        $data=['result'=>true];
        $data['events']=[];
        foreach($eventModels as $eventModel) {
            $data['events'][]=[
                'id'=>$eventModel->id,
                'type'=>$eventModel->type,
                'text'=>$eventModel->text,
            ];
        }

        return $data;
    }

    /*
        public function actionReject() {
            return Yii::$app->db->transaction(function($db){
                $model=SearchRequestOffer::find()->andWhere(['id'=>Yii::$app->request->getBodyParams()['id']])->one();

                if (!$model || $model->searchRequest->user_id!=Yii::$app->user->id) {
                    throw new \yii\web\NotFoundHttpException();
                }

                $model->status=\app\models\SearchRequestOffer::STATUS_REJECTED;
                $model->save();

                return ['result'=>true];
            });
        }
    */

}
