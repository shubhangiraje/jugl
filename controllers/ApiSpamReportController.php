<?php

namespace app\controllers;

use Yii;
use app\models\UserSpamReport;


class ApiSpamReportController extends \app\components\ApiController {


    public function actionSave() {
        $data=Yii::$app->request->getBodyParams()['spamReport'];

        $trx=Yii::$app->db->beginTransaction();

        $report=new \app\models\UserSpamReport();
        $report->dt=(new \app\components\EDateTime())->sqlDateTime();
        $report->user_id=Yii::$app->user->id;
        $report->comment=$data['comment'];


        if ($data['search_request_id']) {
            $searchRequest = \app\models\SearchRequest::findOne($data['search_request_id']);
            $report->setSearchRequestObject($searchRequest);
			/* 
			 * PRÜFT WIE VIELE NUTZER DIE WERBUNG ALS SPAM GEMELDET HABEN.
			 */
			$reportCount = intval(Yii::$app->db->createCommand("select count(*) from user_spam_report where search_request_id = ".$data['search_request_id'])->queryScalar() + 1);
        }

        if ($data['offer_id']) {
            $offer = \app\models\Offer::findOne($data['offer_id']);
            $report->setOfferObject($offer);
			/* 
			 * PRÜFT WIE VIELE NUTZER DIE WERBUNG ALS SPAM GEMELDET HABEN.
			 */
			 $reportCount = intval(Yii::$app->db->createCommand("select count(*) from user_spam_report where offer_id = ".$data['offer_id'])->queryScalar() + 1);
			
        }

        if ($data['user_id']) {
            $user = \app\models\User::findOne($data['user_id']);
            $report->setUserObject($user);
        }

        $errors=[];
        $data['$allErrors']=&$errors;


        if ($report->validate()) {
			
			
			/*
			 * PRÜFT DEN NUTZER OB ER KEINE MINUS PUNKTE HAT.
			 */
			$userCheck = $report->checkUserPoints($report->user->id);
			if($userCheck === TRUE){ 
				/*
				 * ANZEIGE WIRD DEAKTIVIERT WENN REPORTCOUNT 10 MELDUNGEN VOM USER DIE SPAM_REQUEST_COUNT EINSTELLBAR IM BACKEND ÜBERSCHREITEN.
				 */

				if($reportCount >= \app\models\Setting::get('SPAM_REQUEST_COUNT')){
					if ($data['offer_id']) {

						$modelOffer = \app\models\Offer::findOne($data['offer_id']);
						$modelOffer->validation_status = \app\models\Offer::VALIDATION_STATUS_AWAITING;
						$modelOffer->status = \app\models\Offer::STATUS_AWAITING_VALIDATION;
						$modelOffer->save();
						\app\models\UserEvent::addBroadcastMessage([$report->secondUser->id], Yii::t('app', 'Deine Anzeige "{title}" wurde wegen verdacht auf Spam deaktiviert. Wir werden Dein Inserat überprüfen.',[
							'title'=>$modelOffer->title
						]));
						

				
					}

					if ($data['search_request_id']) {

						$modelSearchRequest=\app\models\SearchRequest::findOne($data['search_request_id']);
						$modelSearchRequest->validation_status = \app\models\SearchRequest::VALIDATION_STATUS_AWAITING;
						$modelSearchRequest->status = \app\models\SearchRequest::STATUS_AWAITING_VALIDATION;
						$modelSearchRequest->save();
						\app\models\UserEvent::addBroadcastMessage([$report->secondUser->id], Yii::t('app', 'Deine Suchauftrag "{title}" wurde wegen verdacht auf Spam deaktiviert. Wir werden Deine Suchanfrage überprüfen.',[
							'title'=>$modelSearchRequest->title
						]));
					}
				}
				
				
					/*
					 * SAVE SPAM-REPORT
					 */
					$report->save();
					$report->secondUser->updateCounters(['spam_reports'=>1]);
					$report->secondUser->refresh();

					\app\models\UserEvent::addBroadcastMessage([$report->secondUser->id], Yii::t('app', "Du wurdest von einem anderen Mitglied über die Spam-Funktion gemeldet. Bitte halte Dich an unsere Regeln:[br][/br]1. kein Mitglied belästigen[br][/br]2. keine Werbung auf der Plattform außerhalb des Marktplatzes[br][/br]3. kein Mitglied zu Plattformen außerhalb von jugl.net abwerben (nur als Werbung mit Werbebonus möglich)[br][/br]Bei Zuwiderhandlung kann Dein Profil blockiert werden."));
					\app\models\UserEvent::addBroadcastMessage([$report->user->id], Yii::t('app', "Du hast {user} als Spammer gemeldet. Du hilfst der Community damit sehr, unseriöse Mitglieder zu erkennen, vielen Dank. [spamReportDeactivate:{id}]",[
						'user'=>$report->secondUser->name,
						'id'=>$report->id
					]));

					$autoBlockThreshold=\app\models\Setting::get('AUTO_SPAM_BLOCK_AFTER_REPORTS');
					if ($autoBlockThreshold>0 && $autoBlockThreshold<=$report->secondUser->spam_reports) {
						$report->secondUser->block();
					}
			}
			$trx->commit();
			return ['result'=>true];
			
        } else {
            $data['$errors']=$report->getFirstErrors();
            $errors=array_unique(array_merge($errors,array_values($data['$errors'])));
        }

        return ['spamReport'=>$data];
    }

	
    public function actionDeactivate() {
        $model=\app\models\UserSpamReport::findOne(Yii::$app->request->getBodyParam('id'));

        $trx=Yii::$app->db->beginTransaction();

        if ($model && $model->user_id=Yii::$app->user->id) {
            $model->is_active=0;
            $model->save();
        }

        $eModel=\app\models\UserEvent::findOne(Yii::$app->request->getBodyParam('event_id'));
        if ($eModel->user_id==Yii::$app->user->id) {
            $eModel->text=preg_replace('%\[spamReportDeactivate.*%','',$eModel->text);
            $eModel->save();
        }

        \app\models\UserEvent::addBroadcastMessage([$eModel->user_id], Yii::t('app', "Du hast {user} als kein Spammer gemeldet. Du hilfst der Community damit sehr, unseriöse Mitglieder zu erkennen, vielen Dank.",[
            'user'=>$model->secondUser->name,
        ]));

        $trx->commit();

        return ['result'=>true,
            'events'=>[[
                'id'=>$eModel->id,
                'dt'=>(new \app\components\EDateTime($eModel->dt))->js(),
                'type'=>$eModel->type,
                'text'=>$eModel->text,
                'user'=>!$eModel->second_user_id ? \app\models\User::getAdministrationUser()->getShortData():$eModel->secondUser->getShortData()
            ]]];
    }

}