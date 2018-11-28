<?php

namespace app\controllers;

use Yii;
use app\models\TokenDepositGuarantee;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

/**
 * AdminUserController implements the CRUD actions for User model.
 */
class AdminTokenDepositGuaranteeController extends AdminController
{
    public function actionIndex()
    {
        $query = TokenDepositGuarantee::find()->andWhere(['status'=>TokenDepositGuarantee::STATUS_ACTIVE])->with(['tokenDepositGuaranteeFiles','tokenDepositGuaranteeFiles.file']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder'=>['id'=>SORT_DESC]
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new TokenDepositGuarantee();
        $model->sum=0;

        if ($_REQUEST['TokenDepositGuaranteeFile']) {
            $TDGFiles=[];
            foreach($_REQUEST['TokenDepositGuaranteeFile'] as $k=>$file) {
                $file_id=$file['file_id'];
                if ($file_id) {
                    $TDGFile=new \app\models\TokenDepositGuaranteeFile;
                    $TDGFile->sort_order=$k;
                    $TDGFile->file_id=$file_id;
                    $TDGFiles[]=$TDGFile;
                }
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->save();

                \app\models\TokenDepositGuaranteeFile::deleteAll(['token_deposit_guarantee_id'=>$model->id]);

                foreach($TDGFiles as $TDGFile) {
                    $TDGFile->token_deposit_guarantee_id=$model->id;
                    $TDGFile->save();
                }

                return $this->redirect(['index']);
            }
        }

        $TDGFiles=$model->tokenDepositGuaranteeFiles;

        while(count($TDGFiles)<20) {
            $TDGFiles[]=new \app\models\TokenDepositGuaranteeFile();
        }

        return $this->render('create', [
            'model' => $model,
            'TDGFiles' => $TDGFiles
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($_REQUEST['TokenDepositGuaranteeFile']) {
            $TDGFiles=[];
            foreach($_REQUEST['TokenDepositGuaranteeFile'] as $k=>$file) {
                $file_id=$file['file_id'];
                if ($file_id) {
                    $TDGFile=new \app\models\TokenDepositGuaranteeFile;
                    $TDGFile->sort_order=$k;
                    $TDGFile->file_id=$file_id;
                    $TDGFiles[]=$TDGFile;
                }
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->save();

                \app\models\TokenDepositGuaranteeFile::deleteAll(['token_deposit_guarantee_id'=>$model->id]);

                foreach($TDGFiles as $TDGFile) {
                    $TDGFile->token_deposit_guarantee_id=$model->id;
                    $TDGFile->save();
                }

                return $this->redirect(['index']);
            }
        }

        $TDGFiles=$model->tokenDepositGuaranteeFiles;

        while(count($TDGFiles)<20) {
            $TDGFiles[]=new \app\models\TokenDepositGuaranteeFile();
        }

        return $this->render('update', [
            'model' => $model,
            'TDGFiles' => $TDGFiles
        ]);
    }

    public function actionDelete($id)
    {
        try {
            $model=$this->findModel($id);
            $model->status=TokenDepositGuarantee::STATUS_DELETED;
            $model->save();
        } catch (\yii\base\Exception $e) {
            return $this->pjaxRefreshAlert(Yii::t('app',"Can't delete this item, it is use by another item(s)"));
        }
        return $this->pjaxRefresh();
    }

    protected function findModel($id)
    {
        if (($model = TokenDepositGuarantee::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}