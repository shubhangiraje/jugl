<?php

namespace app\controllers;

use app\models\TokenDeposit;
use Yii;
use app\components\EDateTime;
use app\components\Helper;
use yii\web\NotFoundHttpException;


class ApiFundsTokenDepositController extends \app\components\ApiController {

    public function getLog($pageNum=1) {
        $perPage=50;

        $logQuery=TokenDeposit::find()->andWhere([
                'user_id'=>Yii::$app->user->id,
                'status'=>TokenDeposit::STATUS_ACTIVE
            ])
            ->orderBy('id desc')
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        $logItems=$logQuery->all();
        $hasMore=count($logItems)>$perPage;

        $data=[];
        foreach(array_slice($logItems,0,$perPage) as $item) {
            $data[]=[
                'id'=>$item->id,
                'created_at'=>(new EDateTime($item->created_at))->js(),
                'completion_dt'=>(new EDateTime($item->completion_dt))->js(),
                'sum'=>$item->sum,
                'period_months'=>$item->period_months,
                'contribution_percentage'=>$item->contribution_percentage,
                'payout_type'=>$item->payout_type,
                'percent_sum'=>$item->percentSum,
                'buy_sum'=>$item->buy_sum,
                'buy_currency'=>$item->buy_currency
            ];
        }

        return [
            'log'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ]
        ];
    }

    public function actionLog($pageNum) {
        return $this->getLog($pageNum);
    }

    public function actionIndex() {
        return $this->getLog();
    }

    public function actionPayoutTypeToggle() {
        $id=Yii::$app->request->getBodyParams()['id'];
        $model=TokenDeposit::findOne($id);
        if (!$model || $model->user_id!=Yii::$app->user->id) {
            die;
            throw new NotFoundHttpException();
        }

        $model->togglePayoutType();
        $model->save();

        return ['payout_type'=>$model->payout_type];
    }
}
