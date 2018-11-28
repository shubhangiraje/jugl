<?php

namespace app\controllers;

use Yii;
use app\models\TrollboxMessage;
use app\components\AdminController;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

/**
 * AdminUserController implements the CRUD actions for User model.
 */
class AdminTrollboxMessageController extends AdminController
{
    public function actionIndex()
    {
        $searchModel = new \app\models\TrollboxMessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('apiSave');

        $groupChatModeratorLastVisitProvider = new ActiveDataProvider([
            'query' => \app\models\GroupChatModeratorLastVisit::find()->with(['moderatorUser'])->where(['group_chat_id'=>$model->group_chat_user_id]),
            'sort' => [
                'defaultOrder'=>['dt'=>SORT_DESC]
            ]
        ]);

        $trollboxMessageStatusHistoryProvider = new ActiveDataProvider([
            'query' => \app\models\TrollboxMessageStatusHistory::find()->with(['user'])->where(['trollbox_message_id'=>$model->id]),
            'sort' => [
                'defaultOrder'=>['dt'=>SORT_DESC]
            ]
        ]);

        $chatUserIgnoreProvider = new ActiveDataProvider([
            'query' => \app\models\ChatUserIgnore::find()->with(['ignoreUser','moderatorUser'])->where(['user_id'=>$model->group_chat_user_id]),
            'sort' => [
                'defaultOrder'=>['dt'=>SORT_DESC]
            ]
        ]);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->save();
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'groupChatModeratorLastVisitProvider'=>$groupChatModeratorLastVisitProvider,
            'trollboxMessageStatusHistoryProvider'=>$trollboxMessageStatusHistoryProvider,
            'chatUserIgnoreProvider'=>$chatUserIgnoreProvider
        ]);
    }

    public function actionSetSticky($id, $returl) {
        $model=$this->findModel($id);
        $model->is_sticky=1;
        $model->save();

        return $this->redirect($returl);
    }

    public function actionUnsetSticky($id, $returl) {
        $model=$this->findModel($id);
        $model->is_sticky=0;
        $model->save();

        return $this->redirect($returl);
    }

    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
        } catch (\yii\base\Exception $e) {
            return $this->pjaxRefreshAlert(Yii::t('app',"Can't delete this item, it is use by another item(s)"));
        }
        return $this->pjaxRefresh();
    }

    protected function findModel($id)
    {
        if (($model = TrollboxMessage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}