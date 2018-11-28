<?php

namespace app\components;


use Yii;
use app\models\AdvertisingSearchRequestState;
use app\models\AdvertisingSearchRequestProvider;

class ExtService {
	
	public function setTradetrackerConversion(){
		$url = 'http://ws.tradetracker.com/soap/affiliate?wsdl';
		$params = array();
		$siteID = 288811;
		$dateFrom = '2017-11-01';
		$dateTo = '2050-12-31';
		$provider_id = 1;
		
		$client = new \SoapClient($url, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
		$client->authenticate(161362, '60122be3952573454bc491ed7af789fe0bd50c02');

		foreach ($client->getConversionTransactions($siteID, array ('registrationDateFrom' => $dateFrom, 'registrationDateTo' => $dateTo)) as $transaction) {
			$refAry = explode("|", $transaction->reference);
			$model = new AdvertisingSearchRequestState;	
			$model->user_id = $refAry[0];
			$model->search_request_id = $refAry[1];
			$model->campaign_name = $transaction->campaign->name;
			$model->transactionType = $transaction->transactionType;
			$model->transactionStatus = $transaction->transactionStatus;
			$model->commission = $transaction->commission;
			$model->conversion_id = intval($provider_id.$provider_id.$provider_id.$provider_id.$transaction->ID);
			$model->numTouchPointsTotal = $transaction->numTouchPointsTotal;
			$model->numTouchPointsAttributed = $transaction->numTouchPointsAttributed;
			$model->paidOut = $transaction->paidOut;
			$model->provider_id = $provider_id;
			$model->description = $transaction->description;
			$model->currency = $transaction->currency;
			$model->IP = $transaction->IP;
			$model->registrationDate = date('Y-m-d H:i:s',strtotime($transaction->registrationDate));
			$model->assessmentDate = date('Y-m-d H:i:s',strtotime($transaction->assessmentDate));
			$model->clickToConversion = $transaction->clickToConversion;
			$model->originatingClickDate = $transaction->originatingClickDate;
			$model->rejectionReason = $transaction->rejectionReason;
			$model->countryCode = $transaction->countryCode; 
			$model->attributionModel = $transaction->attributionModel;
			$model->attributableCommission = $transaction->attributableCommission;
			$model->orderAmount = $transaction->orderAmount;
			if($model->validate()){
				$model->save();
				if($model->transactionStatus == 'accept'){
					AdvertisingSearchRequestState::setSearchRequestOffer($model);
				}else{
					$message .= 'Datensatz mit der UserId '.$model->user_id.' vom Provider "Tradetracker" wurde gespeichert<br /><br />
						ID Suchauftrag: '.$model->search_request_id.'<br />
						Campaignname:'.$model->campaign_name.'<br />
						transactionType:'.$model->transactionType.'<br />
						commission:'.$model->commission.'<br />
						registrationDate:'.$model->registrationDate.'<br />
						assessmentDate:'.$model->assessmentDate.'<br />
						conversion_id:'.$model->conversion_id.'<br />
						transactionStatus:'.$model->transactionStatus.'<br />';
				}
			}else{
				$message .= 'Datensatz mit der UserId '.$model->user_id.' vom Provider "Tradetracker" konnte nicht gespeichert<br /><br />
				ID Suchauftrag: '.$model->search_request_id.'<br />
				Campaignname:'.$model->campaign_name.'<br />
				transactionType:'.$model->transactionType.'<br />
				commission:'.$model->commission.'<br />
				registrationDate:'.$model->registrationDate.'<br />
				assessmentDate:'.$model->assessmentDate.'<br />
				conversion_id:'.$model->conversion_id.'<br />
				transactionStatus:'.$model->transactionStatus.'<br />';
			}
		}
		Yii::info($message, 'tradetracker');
	}
	
	public function setCashface($params){
		$provider_id = 2;
		$refAry = explode("|", $params['ref']);
		$auth_token = $refAry[2];
		
		$modelProvider = AdvertisingSearchRequestProvider::find()->where('provider_id=:provider_id and auth_token=:auth_token',[':provider_id'=>$provider_id, ':auth_token'=>$auth_token])->one();
		
		if($modelProvider){

			$model = new AdvertisingSearchRequestState;	
			$model->user_id = $refAry[0];
			$model->search_request_id = $refAry[1];
			$model->provider_id = $provider_id;
			$model->campaign_name = $params['campaignname'];
			$model->transactionType = $params['type'];
			$model->transactionStatus = 'accept';
			$model->commission = $params['amount'];
			$model->conversion_id = intval($provider_id.$provider_id.$provider_id.$provider_id.$params['conversionId']);
			if($model->validate()){
				$model->save();
				$message .= 'Angebot gespeichert.';
				if($model->transactionStatus == 'accept'){
					$message .= 'Angebot angenommen.';
					AdvertisingSearchRequestState::setSearchRequestOffer($model);
				}
			}else{
				$message .= 'Datensatz mit der UserId '.$model->user_id.' vom Provider "Cashface" konnte nicht gespeichert<br /><br />
				ID Suchauftrag: '.$model->search_request_id.'<br />
				Campaignname:'.$model->campaign_name.'<br />
				transactionType:'.$model->transactionType.'<br />
				commission:'.$model->commission.'<br />
				conversion_id:'.$model->conversion_id.'<br />
				transactionStatus:'.$model->transactionStatus.'<br />';
			}			
		}else{
			$message .= 'PROVIDER OR AUTHTOKEN NOT FOUND<br /><br />
			PROVIDER: Cashface<br />
			AUTHTOKEN: '.$auth_token;
		}
		Yii::info($message, 'cashface');
		echo $message;
	}
}