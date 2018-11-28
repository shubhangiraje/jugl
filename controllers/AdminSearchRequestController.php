<?php

namespace app\controllers;

use app\models\SearchRequestCommentSearch;
use Yii;
use app\models\SearchRequest;
use app\models\SearchRequestSearch;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\DefaultText;
use app\models\UserSpamReport;
use app\components\EDateTime;

/**
 * AdminSearchRequestController implements the CRUD actions for SearchRequest model.
 */
class AdminSearchRequestController extends AdminController
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
     * Lists all SearchRequest models.
     * @return mixed
     */
    public function actionIndex()
    {
        SearchRequest::setExpiredStatus();
        $searchModel = new SearchRequestSearch();
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
        $model->status=SearchRequest::STATUS_DELETED;
        $model->save();
        return $this->pjaxRefresh();
    }

    public function actionDeleteSearchRequest($id) {
        $searchRequest = $this->findModel($id);
        $model = new DefaultText();
        $model->setScenario('default-text');
        if ($model->load(Yii::$app->request->post())) {
            $searchRequest->status=SearchRequest::STATUS_DELETED;
            $searchRequest->save();

            $defaultText = DefaultText::findOne($model->default_text_id);
            $text = str_replace(DefaultText::PLACEHOLDER_SEARCH_REQUEST_DELETE,$searchRequest->title,$defaultText->text);

            \app\models\UserEvent::addBroadcastMessage([$searchRequest->user_id], $text);
            Yii::$app->mailer->sendEmail($searchRequest->user, 'default-text', [
                'subject'=>Yii::t('app','Suchaufträge löschen'),
                'text'=>$text
            ]);
            return $this->redirect(['index']);
        } else {
            return $this->renderAjax('delete', [
                'model'=>$model
            ]);
        }
    }

    /**
     * Updates an existing SearchRequest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $isModel = new \app\models\InterestSelection();
        $isModel->type=\app\models\UserInterest::TYPE_SEARCH_REQUEST;
		
        $isModel->loadFromSearchRequest($model);
        if ($_REQUEST['SearchRequestFile']) {
            $searchRequestFiles=[];
            foreach($_REQUEST['SearchRequestFile'] as $k=>$file) {
                $file_id=$file['file_id'];
                if ($file_id) {
                    $searchRequestFile=new \app\models\SearchRequestFile();
                    $searchRequestFile->sort_order=$k;
                    $searchRequestFile->file_id=$file_id;
                    $searchRequestFiles[]=$searchRequestFile;
                }
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            $isModel->load(Yii::$app->request->post());
            if ($model->validate() && $isModel->validate()) {
                $trx=Yii::$app->db->beginTransaction();
				
				if($model->validation_status == \app\models\SearchRequest::VALIDATION_STATUS_ACCEPTED){
					 $model_user_spam_report = new UserSpamReport();
					 $result_user_spam_report = $model_user_spam_report->removeSpamUserPoints('', $id);
				 }
				 if($model->validation_status == \app\models\SearchRequest::VALIDATION_STATUS_REJECTED){
					 $model_user_spam_report = new UserSpamReport();
					$result_user_spam_report = $model_user_spam_report->addSpamUserPoints('', $id);
				 }
				
                $model->save();
                $isModel->saveForSearchRequest($model);

                \app\models\SearchRequestFile::deleteAll(['search_request_id'=>$model->id]);

                foreach($searchRequestFiles as $searchRequestFile) {
                    $searchRequestFile->search_request_id=$model->id;
                    $searchRequestFile->save();
                }

                $trx->commit();
                return $this->redirect(['index']);
            }
        }

        $searchRequestOffersDataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\SearchRequestOffer::find()->where(['search_request_id'=>$_REQUEST['id']])->with(['user']),
            'sort' => [
                'attributes' => [],
                'defaultOrder'=>['id'=>SORT_ASC]
            ]
        ]);
		
		$searchRequestSpamlistDataProvider = new \yii\data\ActiveDataProvider([
            'query' => \app\models\UserSpamReport::find()->where(['search_request_id'=>$_REQUEST['id']])->with(['user']),
            'sort' => [
                'attributes' => [],
                'defaultOrder'=>['id'=>SORT_ASC]
            ]
        ]);
		
        $searchRequestFiles=$model->searchRequestFiles;
        while(count($searchRequestFiles)<30) {
            $searchRequestFiles[]=new \app\models\SearchRequestFile();
        }

        //search request comments
        $searchRequestCommentSearchModel = new SearchRequestCommentSearch();
        $searchRequestCommentDataProvider = $searchRequestCommentSearchModel->search($id, Yii::$app->request->queryParams);

        return $this->render('update', [
            'model' => $model,
            'isModel' => $isModel,
            'searchRequestOffersDataProvider' => $searchRequestOffersDataProvider,
            'searchRequestFiles' =>$searchRequestFiles,
            'searchRequestCommentDataProvider'=>$searchRequestCommentDataProvider,
			'searchRequestSpamlistDataProvider' => $searchRequestSpamlistDataProvider
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
        $searchModel = new SearchRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);

        return $this->render('control', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAccept($id) {
        $searchRequest = $this->findModel($id);
        $model = new DefaultText();
        $model->setScenario('default-text-edit');

        if ($model->load(Yii::$app->request->post())) {

            $searchRequest->validation_status = \app\models\SearchRequest::VALIDATION_STATUS_ACCEPTED;
            $searchRequest->status=\app\models\SearchRequest::STATUS_ACTIVE;
			$model_user_spam_report = new UserSpamReport();
			$result_user_spam_report = $model_user_spam_report->removeSpamUserPoints('', $id);
            $searchRequest->save();
            $searchRequest->afterInsert();

            $text = Yii::t('app','Dein Suchauftrag "{title}" wurde geprüft und ist jetzt bei jugl.net online.', ['title'=>$searchRequest->title]).' '.$model->default_text_edit;
            \app\models\UserEvent::addBroadcastMessage([$searchRequest->user_id], $text);
            Yii::$app->mailer->sendEmail($searchRequest->user, 'default-text', [
                'subject'=>Yii::t('app','Suchauftrag freigegeben'),
                'text'=>$text,
            ]);
            return $this->redirect(['control']);

        } else {
            return $this->renderAjax('accept', [
                'model'=>$model
            ]);
        }

    }

    public function actionReject($id) {
        $searchRequest = $this->findModel($id);
        $model = new DefaultText();
        $model->setScenario('default-text-edit');

        if ($model->load(Yii::$app->request->post())) {
            $searchRequest->status = SearchRequest::STATUS_REJECTED;
            $searchRequest->validation_status = \app\models\SearchRequest::VALIDATION_STATUS_REJECTED;
			$model_user_spam_report = new UserSpamReport();
			$result_user_spam_report = $model_user_spam_report->addSpamUserPoints('', $id);
            $searchRequest->save();

            $text = Yii::t('app','Dein Suchauftrag "{title}" wurde von der jugl Administration abgelehnt.', ['title'=>$searchRequest->title]).' '.$model->default_text_edit;
            \app\models\UserEvent::addBroadcastMessage([$searchRequest->user_id], $text);
            Yii::$app->mailer->sendEmail($searchRequest->user, 'default-text', [
                'subject'=>Yii::t('app','Suchauftrag abgelehnt'),
                'text'=>$text,
            ]);

            return $this->redirect(['control']);
        } else {
            return $this->renderAjax('reject', [
                'model'=>$model,
                'searchRequest'=>$searchRequest
            ]);
        }
    }


    public function actionPause($id) {
        $model = $this->findModel($id);
        if($model->oldAttributes['validation_status']==SearchRequest::VALIDATION_STATUS_AWAITING_LATER) {
            $model->validation_status = SearchRequest::VALIDATION_STATUS_AWAITING;
        } else {
            $model->validation_status = SearchRequest::VALIDATION_STATUS_AWAITING_LATER;
        }
        $model->save();
        return $this->redirect('control');
    }

    /**
     * Finds the SearchRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SearchRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SearchRequest::find()->where(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	public function actionCreate()
    {
        $model = new SearchRequest();
		$isModel = new \app\models\InterestSelection();
        $isModel->type=\app\models\UserInterest::TYPE_SEARCH_REQUEST;
		
		$model->user_id = 198;
		$model->create_dt = (new EDateTime)->sqlDateTime();
		$model->isAdmin = true;
			
			$model->setScenario('saveAdmin');

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->save();
				$isModel->loadFromSearchRequest($model);
				$isModel->load(Yii::$app->request->post());
				if($isModel->validate()){
					$isModel->saveForSearchRequest($model);
				}
				return $this->redirect(['index']);
            }
        
		
		return $this->render('create', [
            'model' => $model,
            'isModel' => $isModel
        ]);
    }
	
	public function actionImporttradetracker() {
       \app\components\ExtService::setTradetrackerConversion();
    }
}