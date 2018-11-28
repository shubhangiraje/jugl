<?php

namespace app\controllers;

use Yii;
use app\models\Offer;
use app\models\OfferSearch;
use app\components\AdminController;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\models\DefaultText;
use app\models\UserSpamReport;

/**
 * AdminOfferController implements the CRUD actions for Offer model.
 */
class AdminOfferController extends AdminController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete-user' => ['post'],
                    'block-user' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Lists all Offer models.
     * @return mixed
     */
    public function actionIndex()
    {
        Offer::setExpiredStatus();
        $searchModel = new OfferSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing Admin model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model=$this->findModel($id);
        $model->status=Offer::STATUS_DELETED;
        $model->save();
        return $this->pjaxRefresh();
    }

    public function actionCreate($user_id)
    {
        $model= new \app\models\Offer();
        $model->setScenario('saveAdmin');
        $model->user_id=$user_id;
        $model->created_by_admin=1;
        $model->uf_packet='ALL';
        $model->uf_sex='A';
        $model->create_dt=(new \app\components\EDateTime)->sqlDateTime();

        $isModel = new \app\models\InterestSelection();
        $isModel->type=\app\models\UserInterest::TYPE_OFFER;

        if ($_REQUEST['OfferFile']) {
            $offerFiles=[];
            foreach($_REQUEST['OfferFile'] as $k=>$file) {
                $file_id=$file['file_id'];
                if ($file_id) {
                    $offerFile=new \app\models\OfferFile;
                    $offerFile->sort_order=$k;
                    $offerFile->file_id=$file_id;
                    $offerFiles[]=$offerFile;
                }
            }
        }

//        echo '<pre>';
//        print_r($model->rules());
//        echo '</pre>';
//        exit;


        if ($model->load(Yii::$app->request->post()) && $isModel->load(Yii::$app->request->post())) {

            if (!$model->view_bonus_used) $model->view_bonus_used=0;

            if($model->without_view_bonus) {
                $model->view_bonus = null;
                $model->view_bonus_total = null;
            }

            $r1=$model->validate();
            $r2=$isModel->validate();
            if ($r1 && $r2) {
                $trx=Yii::$app->db->beginTransaction();

                $model->save();
                $isModel->saveForOffer($model);
                foreach($offerFiles as $offerFile) {
                    $offerFile->offer_id=$model->id;
                    $offerFile->save();
                }
                $model->afterInsert();
                $trx->commit();
                return $this->redirect(['admin-user/update','id'=>$model->user_id]);
            }
        }

        while(count($offerFiles)<30) {
            $offerFiles[]=new \app\models\OfferFile();
        }

        return $this->render('create', [
            'model' => $model,
            'offerFiles' =>$offerFiles,
            'isModel' => $isModel,
        ]);
    }

    /**
     * Updates an existing Offer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('saveAdmin');

        if(!$model->view_bonus && !$model->view_bonus_total) {
            $model->without_view_bonus = 1;
        }

        $isModel = new \app\models\InterestSelection();
        $isModel->type=\app\models\UserInterest::TYPE_OFFER;
        $isModel->loadFromOffer($model);

        if ($_REQUEST['OfferFile']) {
            $offerFiles=[];
            foreach($_REQUEST['OfferFile'] as $k=>$file) {
                $file_id=$file['file_id'];
                if ($file_id) {
                    $offerFile=new \app\models\OfferFile;
                    $offerFile->sort_order=$k;
                    $offerFile->file_id=$file_id;
                    $offerFiles[]=$offerFile;
                }
            }
        }

        if ($model->load(Yii::$app->request->post())) {

            if($model->without_view_bonus) {
                $model->view_bonus = null;
                $model->view_bonus_total = null;
            }

            $isModel->load(Yii::$app->request->post());

            if ($model->validate() && $isModel->validate()) {
                $trx=Yii::$app->db->beginTransaction();
				
				if($model->validation_status == \app\models\Offer::VALIDATION_STATUS_ACCEPTED){
					 $model_user_spam_report = new UserSpamReport();
					 $result_user_spam_report = $model_user_spam_report->removeSpamUserPoints('', $id);
				 }
				 if($model->validation_status == \app\models\Offer::VALIDATION_STATUS_REJECTED){
					 $model_user_spam_report = new UserSpamReport();
					 $result_user_spam_report = $model_user_spam_report->addSpamUserPoints('', $id);
				 }
				
                $model->save();
                $isModel->saveForOffer($model);

                \app\models\OfferFile::deleteAll(['offer_id'=>$model->id]);

                foreach($offerFiles as $offerFile) {
                    $offerFile->offer_id=$model->id;
                    $offerFile->save();
                }
				
				

                $trx->commit();
                return $this->redirect(['index']);
            }
        }

        $OfferRequestsDataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\OfferRequest::find()->where(['offer_id'=>$_REQUEST['id']])->with(['user']),
            'sort' => [
                'attributes' => [],
                'defaultOrder'=>['id'=>SORT_ASC]
            ]
        ]);
		
		$OfferSpamlistDataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\UserSpamReport::find()->where(['offer_id'=>$_REQUEST['id']])->with(['user']),
            'sort' => [
                'attributes' => [],
                'defaultOrder'=>['id'=>SORT_ASC]
            ]
        ]);

        $offerFiles=$model->offerFiles;
        while(count($offerFiles)<30) {
            $offerFiles[]=new \app\models\OfferFile();
        }

        return $this->render('update', [
            'model' => $model,
            'isModel' => $isModel,
            'OfferRequestsDataProvider' => $OfferRequestsDataProvider,
			'OfferSpamlistDataProvider' => $OfferSpamlistDataProvider,
            'offerFiles' =>$offerFiles
        ]);
    }

    public function actionInterestNestedLevel2() {
        $level1_id=$_REQUEST['depdrop_all_params']['level1-id'];

        $items=\app\models\InterestSelection::getNestedLevelList($level1_id);

        $data=['output'=>[],'selected'=>''];
        foreach($items as $k=>$v) {
            $data['output'][]=['id'=>$k,'name'=>$v];
        }

        echo json_encode($data);
    }

    public function actionInterestNestedLevel3() {
        $level2_id=$_REQUEST['depdrop_all_params']['level2-id'];

        $items=\app\models\InterestSelection::getNestedLevelList($level2_id);

        $data=['output'=>[],'selected'=>''];
        foreach($items as $k=>$v) {
            $data['output'][]=['id'=>$k,'name'=>$v];
        }

        echo json_encode($data);
    }


    public function actionControl() {
        $searchModel = new OfferSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);

        return $this->render('control', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAccept($id) {

        $offer = $this->findModel($id);
        $model = new DefaultText();

        $model->setScenario('default-text-edit');
        if ($model->load(Yii::$app->request->post())) {

            $offer->validation_status = \app\models\Offer::VALIDATION_STATUS_ACCEPTED;
            $offer->status=\app\models\Offer::STATUS_ACTIVE;
			$model_user_spam_report = new UserSpamReport();
			$result_user_spaam_report = $model_user_spam_report->removeSpamUserPoints($id, '');
			
			
            $offer->save();
            $offer->afterInsert();

            $text = Yii::t('app','Deine Anzeige "{title}" wurde geprüft und ist jetzt bei jugl.net online.', ['title'=>$offer->title]).' '.$model->default_text_edit;
            \app\models\UserEvent::addBroadcastMessage([$offer->user_id], $text);
            Yii::$app->mailer->sendEmail($offer->user, 'default-text', [
                'subject'=>Yii::t('app','Werbung freigegeben'),
                'text'=>$text
            ]);

            return $this->redirect(['control']);

        } else {
            return $this->renderAjax('accept', [
                'model'=>$model
            ]);
        }
    }

    public function actionReject($id) {
        $offer = $this->findModel($id);
        $model = new DefaultText();

        $model->setScenario('default-text-edit');
        if ($model->load(Yii::$app->request->post())) {
            $offer->status = Offer::STATUS_REJECTED;
            $offer->validation_status = \app\models\Offer::VALIDATION_STATUS_REJECTED;
			$model_user_spam_report = new UserSpamReport();
			$result_user_spaam_report = $model_user_spam_report->addSpamUserPoints($id, '');
			
            $offer->save();

            $text = Yii::t('app','Deine Werbung "{title}" wurde von der jugl Administration abgelehnt.', ['title'=>$offer->title]).' '.$model->default_text_edit;
            \app\models\UserEvent::addBroadcastMessage([$offer->user_id], $text);
            Yii::$app->mailer->sendEmail($offer->user, 'default-text', [
                'subject'=>Yii::t('app','Werbung abgelehnt'),
                'text'=>$text,
            ]);

            return $this->redirect(['control']);
        } else {
            return $this->renderAjax('reject', [
                'model'=>$model,
                'offer'=>$offer
            ]);
        }
    }

    public function actionPause($id) {
        $model = $this->findModel($id);
        if($model->oldAttributes['validation_status']==Offer::VALIDATION_STATUS_AWAITING_LATER) {
            $model->validation_status = Offer::VALIDATION_STATUS_AWAITING;
        } else {
            $model->validation_status = Offer::VALIDATION_STATUS_AWAITING_LATER;
        }
        $model->save();
        return $this->redirect('control');
    }

    public function actionDeleteOffer($id) {
        $offer = $this->findModel($id);
        $model = new DefaultText();
        $model->setScenario('default-text');
        if ($model->load(Yii::$app->request->post())) {
            $offer->status=Offer::STATUS_DELETED;
            $offer->save();
            $defaultText = DefaultText::findOne($model->default_text_id);
            $text = str_replace(DefaultText::PLACEHOLDER_OFFER_DELETE,$offer->title,$defaultText->text);

            \app\models\UserEvent::addBroadcastMessage([$offer->user_id], $text);
            Yii::$app->mailer->sendEmail($offer->user, 'default-text', [
                'subject'=>Yii::t('app','Werbung löschen'),
                'text'=>$text
            ]);
            return $this->redirect(['index']);
        } else {
            return $this->renderAjax('delete', [
                'model'=>$model
            ]);
        }
    }

    public function actionNotifyUpdateCategory($id) {
        $offer = $this->findModel($id);
        $model = new DefaultText();
        $model->setScenario('default-text-edit');
        if ($model->load(Yii::$app->request->post())) {
            $text = Yii::t('app', 'Bezgl. Deiner Werbung "{title}": ', ['title'=>$offer->title]).$model->default_text_edit;
            \app\models\UserEvent::addBroadcastMessage([$offer->user_id], $text);
            Yii::$app->mailer->sendEmail($offer->user, 'default-text', [
                'subject'=>Yii::t('app','Kategorie Deiner Werbung wurde geändert'),
                'text'=>$text
            ]);
            return true;
        } else {
            return $this->renderAjax('notify-update-category', [
                'model'=>$model
            ]);
        }
    }

    /**
     * Finds the Offer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Offer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Offer::find()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}