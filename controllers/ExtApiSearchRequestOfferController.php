<?php

namespace app\controllers;

use Yii;
use \yii\web\NotFoundHttpException;
use \app\models\SearchRequest;
use \app\models\SearchRequestOffer;
use \app\models\SearchRequestParamValue;
use \app\models\SearchRequestOfferParamValue;
use app\components\EDateTime;


class ExtApiSearchRequestOfferController extends \app\components\ExtApiController {

    public function actionAdd($searchRequestId) {
		$sr=SearchRequest::find()->where('id=:id and active_till>=CAST(NOW() AS DATE) and search_request_type=:type',[':id'=>$searchRequestId, ':type'=>SearchRequest::SEARCH_REQUEST_TYPE_STANDART])->with([
            'searchRequestParamValues',
            'searchRequestParamValues.param',
        ])->one();
		
        if (!$sr) {
            throw new NotFoundHttpException();
        }

        $data=[
            'searchRequestOffer'=>[
                'search_request_id'=>$searchRequestId,
                'files'=>[],
                'details_files'=>[]
            ]
        ];

        $data['searchRequestOffer']['searchRequestOfferParamValues']=[];
        foreach($sr->searchRequestParamValues as $pv) {
            $data['searchRequestOffer']['searchRequestOfferParamValues'][]=[
                'id'=>$pv->id,
                'title'=>strval($pv->param),
                'value'=>$pv->param->type==\app\models\Param::TYPE_LIST ? strval($pv->paramValue):$pv->param_value,
                'match'=>false
            ];
        }

        return $data;
    }

    public function actionSave() {
        $data=Yii::$app->request->getBodyParams()['searchRequestOffer'];

        $searchRequest=SearchRequest::find()->where('id=:id and active_till>=CAST(NOW() AS DATE)',[':id'=>$data['search_request_id']])->one();
		if($searchRequest->search_request_type!=SearchRequest::SEARCH_REQUEST_TYPE_EXTERNAL_AD){
			if (!$searchRequest) {
				throw new NotFoundHttpException();
			}

			$errors=[];
			$data['$allErrors']=&$errors;

			$trx=Yii::$app->db->beginTransaction();

			if ($data['id']) {
				//$searchRequest=$this->findModel($data['id']);
			} else {
				$searchRequestOffer=new SearchRequestOffer;
				$searchRequestOffer->user_id=Yii::$app->user->id;
				$searchRequestOffer->search_request_id=$searchRequest->id;
				$searchRequestOffer->create_dt=(new EDateTime)->sqlDateTime();
			}

			$searchRequestOffer->setScenario('save');

			$searchRequestOffer->load($data,'');

			if ($searchRequestOffer->validate()) {
				$searchRequestOffer->save();
			} else {
				$data['$errors']=$searchRequestOffer->getFirstErrors();
				$errors=array_unique(array_merge($errors,array_values($data['$errors'])));
			}

			if (!empty($errors)) {
				$trx->rollBack();
				return ['searchRequestOffer'=>$data];
			}

			$searchRequestOffer->relinkFilesWithSortOrder($data['files'],'files','searchRequestOfferFiles');
			$searchRequestOffer->relinkFilesWithSortOrder($data['details_files'],'detailsFiles','searchRequestOfferDetailsFiles');

			// save param values
			foreach($searchRequestOffer->searchRequestOfferParamValues as $pv) {
				$pv->delete();
			}

			$searchRequestParamValues=SearchRequestParamValue::find()->where(['search_request_id'=>$searchRequest->id])->indexBy('id')->all();
			$matchedPv=0;
			$countPv=0;

			foreach($data['searchRequestOfferParamValues'] as $param) {
				if ($searchRequestParamValues[$param['id']]) {
					$sropv = new SearchRequestOfferParamValue();
					$sropv->search_request_offer_id = $searchRequestOffer->id;
					$sropv->param_id = $searchRequestParamValues[$param['id']]->param_id;
					$sropv->match = $param['match'] ? 1:0;

					$countPv++;
					if ($sropv->match) {
						$matchedPv++;
					}
					$sropv->save();
				}
			}

			$searchRequestOffer->relevancy=$countPv>0 ? floor(0.5+100*$matchedPv/$countPv):0;
			$searchRequestOffer->save();

			if (!empty($errors)) {
				$trx->rollBack();
				return ['searchRequest'=>$data];
			}

			\app\models\UserEvent::addNewSearchRequestOffer($searchRequestOffer);

			Yii::$app->user->identity->packetCanBeSelected();

			$trx->commit();

			return ['result'=>true];
		}
		else{
			//Fallback for older App Versions
			 return ['searchRequestOffer'=>['$allErrors'=>['Dies ist nicht möglich!']]];
		}
    }

    public function actionDetails($id) {
        $model=SearchRequestOffer::find()->andWhere(['id'=>$id])->with([
            'files',
            'user',
            'user.avatarFile',
            'searchRequest',
            'searchRequest.searchRequestParamValues',
            'searchRequest.searchRequestParamValues.param',
        ])->one();

        if (!$model || ($model->searchRequest->user_id!=Yii::$app->user->id && $model->user_id!=Yii::$app->user->id)) {
            throw new NotFoundHttpException();
        }

        $data=$model->toArray(['id','description','price_from','price_to','status','details']);

        $data['user']=$model->user->getShortData(['rating', 'feedback_count', 'packet']);

        $data['images']=[];

        foreach($model->files as $image) {
            $data['images'][] = $image->getThumbUrl('searchRequest');
        }

        if (empty($data['images'])) {
            $data['images']=[\app\components\Thumb::createUrl('/static/images/account/default_interest.png','offer',true)];
        }

        foreach($model->files as $image) {
            $data['bigImages'][]=$image->getThumbUrl('fancybox');
        }

        $blurDetails=$model->searchRequest->user_id==Yii::$app->user->id && $model->status!=SearchRequestOffer::STATUS_ACCEPTED;
        $data['blurDetails']=$blurDetails;

        $data['detals_images']=[];
        foreach($model->detailsFiles as $image) {
            $data['details_images'][]=[
                'small'=>$image->getThumbUrl($blurDetails ? 'searchRequestBlur':'searchRequest'),
            ];
        }

        $data['paramValues']=[];
        $params=\yii\helpers\ArrayHelper::index($model->searchRequestOfferParamValues,'param_id');
        foreach($model->searchRequest->searchRequestParamValues as $pv) {
            $data['paramValues'][]=[
                'title'=>strval($pv->param),
                'value'=>$pv->param->type==\app\models\Param::TYPE_LIST ? strval($pv->paramValue):$pv->param_value,
                'match'=>boolval($params[$pv->param_id]->match)
            ];
        }

        $data['forMe']=$model->searchRequest->user_id==Yii::$app->user->id;

        return [
            'searchRequestOffer'=>$data
        ];
    }


    public function actionAccept() {
        $trx = Yii::$app->db->beginTransaction();

        Yii::$app->user->identity->lockForUpdate();

        $model = SearchRequestOffer::find()->andWhere(['id' => Yii::$app->request->getBodyParams()['id']])->one();

        if (!$model || $model->searchRequest->user_id != Yii::$app->user->id) {
            throw new \yii\web\NotFoundHttpException();
        }

        $result=null;

        if ($model->searchRequest->user->balance>=$model->searchRequest->bonus) {
            $model->status = \app\models\SearchRequestOffer::STATUS_ACCEPTED;
            $model->save();

            \app\models\UserEvent::addSearchRequestOfferAccepted($model);

            if ($model->searchRequest->bonus>0) {
                $comment = Yii::t('app','Du hast das Angebot auf den Suchauftrag [searchRequest:{searchRequestId}]"{searchRequestTitle}"[/searchRequest] angenommen und zahlst [user][/user] einen Vermittlungsbonus von [sum][/sum]',[
                    'searchRequestId'=>$model->searchRequest->id,
                    'searchRequestTitle'=>$model->searchRequest->title,
                ]);

                $model->searchRequest->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_OUT, -$model->searchRequest->bonus, $model->user, $comment);

                $comment = Yii::t('app','Hat Dein Angebot auf den Suchauftrag [searchRequest:{searchRequestId}]"{searchRequestTitle}"[/searchRequest]  angenommen. Du erhältst einen Vermittlungsbonus von [sum][/sum]',[
                    'searchRequestId'=>$model->searchRequest->id,
                    'searchRequestTitle'=>$model->searchRequest->title,
                ]);

                $commentOut = Yii::t('app','Hat Dich zu jugl.net eingeladen. Deshalb gibst Du [sum][/sum] Deiner Einnahmen für „{user} hat Dein Angebot auf einen Suchauftrag angenommen“ an [user][/user] ab',[
                    'searchRequestId'=>$model->searchRequest->id,
                    'searchRequestTitle'=>$model->searchRequest->title,
                    'user'=>$model->searchRequest->user->name
                ]);

                $commentInRef = Yii::t('app','Hat für ein Angebot auf den Suchauftrag [searchRequest:{searchRequestId}]"{searchRequestTitle}"[/searchRequest] einen Vermittlungsbonus erhalten. Dafür erhältst Du anteilig [sum][/sum]',[
                    'searchRequestId'=>$model->searchRequest->id,
                    'searchRequestTitle'=>$model->searchRequest->title,
                ]);

                $commentOutRef = Yii::t('app','Hat Dich zu jugl.net eingeladen. Deshalb gibst Du [sum][/sum] Deiner Einnahmen für „{user} hat Dein Angebot auf einen Suchauftrag angenommen“ an [user][/user] ab',[
                    'searchRequestId'=>$model->searchRequest->id,
                    'searchRequestTitle'=>$model->searchRequest->title,
                    'user'=>$model->searchRequest->user->name
                ]);

                $model->user->distributeReferralPayment($model->searchRequest->bonus,$model->searchRequest->user,\app\models\BalanceLog::TYPE_IN,\app\models\BalanceLog::TYPE_IN_REF,\app\models\BalanceLog::TYPE_IN_REF_REF, $comment, 0, $commentOut, $commentInRef, $commentOutRef, false);
                //$model->user->addBalanceLogItem(\app\models\BalanceLog::TYPE_IN, $model->searchRequest->bonus, $model->searchRequest->user, Yii::t('app','Handel abgeschlossen'));
            }

            $result=true;
        } else {
            $result=Yii::t('app','Leider hast Du noch nicht ausreichend Jugl zum Annehmen des Auftrages und auszahlen des Vermittlungsbonus Du kannst dein Jugl-Konto jetzt aufladen.');
        }

        if ($result===true) {
            $trx->commit();
        } else {
            $trx->rollBack();
        }

        return ['result'=>$result];
    }


    public function actionReject() {

        $trx = Yii::$app->db->beginTransaction();

        $model=SearchRequestOffer::find()->andWhere(['id'=>Yii::$app->request->getBodyParams()['id']])->one();

        $model->setScenario('reject');

        if (!$model || $model->searchRequest->user_id!=Yii::$app->user->id) {
            throw new \yii\web\NotFoundHttpException();
        }

        $data = Yii::$app->request->getBodyParams()['reject'];
        $errors = [];
        $data['$allErrors'] =& $errors;

        $model->load($data,'');

        if ($model->validate()) {
            $model->status=\app\models\SearchRequestOffer::STATUS_REJECTED;
            $model->save();
        } else {
            $data['$errors'] = $model->getFirstErrors();
            $errors = array_unique(array_merge($errors, array_values($data['$errors'])));
        }

        if (!empty($errors)) {
            $trx->rollBack();
            return ['reject' => $data];
        }

        \app\models\UserEvent::addSearchRequestOfferDeclined($model);

        $trx->commit();
        return ['result'=>true];

    }

    public function actionMarkAsContacted()
    {
        $model=SearchRequestOffer::findOne(Yii::$app->request->post()['id']);

        if (!$model || $model->searchRequest->user_id!=Yii::$app->user->id) {
            throw new \yii\web\NotFoundHttpException;
        }

        $model->status=SearchRequestOffer::STATUS_CONTACTED;
        $model->save();

        \app\components\ChatServer::sendSystemMessage(Yii::$app->user->id,$model->user_id,
            Yii::t('app','Nachricht bezüglich des Angebots auf den Suchauftrag "{title}"',['title'=>$model->searchRequest->title])
        );

        return ['result'=>true];
    }


    public function actionOffersList($id) {
        $searchRequest=SearchRequest::find()
            ->where(['id'=>$id])->one();

        $data=$searchRequest->toArray(['id','title','price_from','price_to','bonus']);
        $data['create_dt']=(new EDateTime($searchRequest->create_dt))->js();

        if (count($searchRequest->searchRequestInterests)>0) {
            $data['level1Interest']=strval($searchRequest->searchRequestInterests[0]->level1Interest);
            $data['level2Interest']=strval($searchRequest->searchRequestInterests[0]->level2Interest);

            $level3Interests=[];
            foreach($searchRequest->searchRequestInterests as $sri) {
                $level3Interests[]=$sri->level3Interest;
            }
            $data['level3Interests']=implode(', ',$level3Interests);
        }

        if (count($searchRequest->files)>0) {
            $data['image']=$searchRequest->files[0]->getThumbUrl('searchRequest');
        } else {
            $data['image']=\app\components\Thumb::createUrl('/static/images/account/default_interest.png','searchRequest',true);
        }

        $data['searchRequestOffers']=[];
        foreach($searchRequest->searchRequestActiveOffers as $offer) {
            $odata=$offer->toArray(['id','price_from','price_to','status']);
            $odata['description']=\yii\helpers\StringHelper::truncate($offer->description,80);
            $data['searchRequestOffers'][]=$odata;
        }

        return [
            'searchRequest'=>$data
        ];
    }

}
