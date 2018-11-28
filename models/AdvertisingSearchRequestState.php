<?php

namespace app\models;

use Yii;
use \app\models\SearchRequest;
use \app\components\EDateTime;
use yii\web\NotFoundHttpException;

class AdvertisingSearchRequestState extends \app\models\base\AdvertisingSearchRequestState
{
	public $allErrors;

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
			'conversion_id' => Yii::t('app', 'conversion_id'),
			'user_id' => Yii::t('app', 'user_id'),
			'dt' => Yii::t('app', 'dt'),
			'provider_id' => Yii::t('app', 'provider ID'),
			'campaign_name' => Yii::t('app', 'campaign_name'),
			'transactionType' => Yii::t('app', 'transactionType'),
			'transactionStatus' => Yii::t('app', 'transactionStatus'),
			'numTouchPointsTotal' => Yii::t('app', 'numTouchPointsTotal'),
			'numTouchPointsAttributed' => Yii::t('app', 'numTouchPointsAttributed'),
			'attributableCommission' => Yii::t('app', 'attributableCommission'),
			'description' => Yii::t('app', 'description'),
			'currency' => Yii::t('app', 'currency'),
			'commission' => Yii::t('app', 'commission'),
			'orderAmount' => Yii::t('app', 'orderAmount'),
			'IP' => Yii::t('app', 'IP'),
			'registrationDate' => Yii::t('app','registrationDate'),
			'assessmentDate' => Yii::t('app', 'assessmentDate'),
			'clickToConversion' => Yii::t('app', 'clickToConversion'),
			'originatingClickDate' => Yii::t('app', 'originatingClickDate'),
			'rejectionReason' => Yii::t('app', 'rejectionReason'),
			'paidOut' => Yii::t('app', 'paidOut'),
			'countryCode' => Yii::t('app', 'countryCode'),
			'attributionModel' => Yii::t('app', 'attributionModel'),
        ];
    }
	
	 /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'campaign_name', 'transactionType', 'transactionStatus', 'commission', 'provider_id'], 'required'],
			[['user_id', 'numTouchPointsTotal', 'numTouchPointsAttributed', 'provider_id'], 'integer'],
            [['conversion_id', 'dt', 'description', 'currency', 'IP', 'assessmentDate', 'clickToConversion', 'originatingClickDate', 'rejectionReason', 'countryCode', 'attributionModel', 'paidOut'], 'safe'],
			[['attributableCommission', 'commission', 'orderAmount'], 'number'],
			//[['conversion_id'], 'checkExist'],
        ];
    }
	
	public function checkExist(){
	$model = AdvertisingSearchRequestState::find()->where(['conversion_id'=>$this->conversion_id])->count();
		if($model){
			$this->addError('conversion_id', 'Error Advertising Search Request State');
		}
	}
	
	public function setSearchRequestOffer($model){
		$data = array();
		$searchRequest=SearchRequest::find()->where('id=:id and active_till>=CAST(NOW() AS DATE)',[':id'=>$model->search_request_id])->one();
		
		$data['description'] = $searchRequest->title;
		$data['price_from'] = $searchRequest->price_from;
		$data['price_to'] = $searchRequest->price_to;
		$data['files'] = '';
		$data['details_files'] = '';
		
		if (!$searchRequest) {
			throw new NotFoundHttpException();
		}
		
		$errors=[];
		$data['$allErrors']=&$errors;

		$trx=Yii::$app->db->beginTransaction();
		$searchRequestOffer=new SearchRequestOffer;
		$searchRequestOffer->user_id=$model->user_id;
		$searchRequestOffer->search_request_id=$model->search_request_id;
		$searchRequestOffer->create_dt=(new EDateTime)->sqlDateTime();
		$searchRequestOffer->setScenario('save_advertising');
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

		$searchRequestOffer->relevancy=100;
		$searchRequestOffer->save();

		if (!empty($errors)) {
			$trx->rollBack();
			return false;
		}
		\app\models\UserEvent::addNewSearchRequestOffer($searchRequestOffer);
										
		$trx->commit();
		AdvertisingSearchRequestState::setSearchRequestOfferAccept($searchRequestOffer);
	}
	
	public function setSearchRequestOfferAccept($data){
		$trx = Yii::$app->db->beginTransaction();
		$result=null;
        $model = SearchRequestOffer::find()->andWhere(['id' => $data->id])->one();

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
			}

            $result=true;
        } else {
            $result=Yii::t('app','Leider hast Du noch nicht ausreichend Jugl zum Annehmen des Auftrages und auszahlen des Vermittlungsbonus Du kannst dein Jugl-Konto jetzt aufladen.');
        }

        if ($result===true) {
            $trx->commit();
			AdvertisingSearchRequestState::setSearchRequestOfferFeedback($model);
		} else {
            $trx->rollBack();
			exit;
        }
      
	}
	
	public function setSearchRequestOfferFeedback($datas){	
		$model=\app\models\UserFeedback::findOne($datas->id);
        if ($datas->id) {
            $sro= \app\models\SearchRequestOffer::findOne($datas->id);
            $model=$sro->counterUserFeedback;
            if (!$model) {
                $model=new \app\models\UserFeedback();
                $model->user_id=$sro->user_id;
                $model->second_user_id=$sro->searchRequest->user_id;
                $model->create_dt=(new EDateTime())->sqlDateTime();
            }
        }
		
		$modelUseTmp = \app\models\User::findOne($sro->user_id);
		$country_shortname = \app\models\Country::getCountryLanguage($modelUser->country_id);
		
		if($country_shortname == 'en'){
			$model->feedback = $sro->searchRequest->feedback_text_en;
		}elseif($country_shortname == 'ru'){
			$model->feedback = $sro->searchRequest->feedback_text_ru;
		}else{
			$model->feedback = $sro->searchRequest->feedback_text_de;
		}
		
		$data = array('rating' => 100, 'response' => 'NULL' , 'feedback' => $model->feedback );
        
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
		
        $events = [];
        if (count($model->counterSearchRequestOffers)>0) {
            \app\models\UserEvent::addDealCounterFeedback($model->counterSearchRequestOffers[0]);
            $events=\app\models\UserEvent::find()->where(['user_id'=>$model->counterSearchRequestOffers[0]->user_id,'type'=>\app\models\UserEvent::TYPE_SEARCH_REQUEST_FEEDBACK_NOTIFICATION])
                ->andWhere('text like(:text)',[':text'=>"%[searchRequestOfferCounterFeedback:{$model->counterSearchRequestOffers[0]->id}%"])->all();
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
