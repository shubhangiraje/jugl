<?php

namespace app\controllers;

use app\models\BalanceTokenLog;
use app\models\TokenDeposit;
use Yii;
use \app\models\BalanceLog;
use \app\components\EDateTime;


class ExtApiFundsController extends \app\components\ExtApiController {

    private function getLog($pageNum,$status,$sort='-dt') {
        $perPage=30;

        $logQuery=BalanceLog::find()->andWhere(['user_id'=>Yii::$app->user->id])->andWhere('(sum>=0.01 or sum<=-0.01)')
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        switch ($sort) {
            case '-dt':
                $logQuery->orderBy(['dt'=>SORT_DESC]);
                $logQuery->from([new \yii\db\Expression('balance_log use index(user_id_dt)')]);
                break;
            case 'dt':
                $logQuery->orderBy(['dt'=>SORT_ASC]);
                $logQuery->from([new \yii\db\Expression('balance_log use index(user_id_dt)')]);
                break;
            case 'sum':
                $logQuery->orderBy(['sum'=>SORT_ASC]);
                break;
            case '-sum':
                $logQuery->orderBy(['sum'=>SORT_DESC]);
                break;
        }
        
        if ($status=='positive') {
            $logQuery->andWhere('sum>=0');
        }

        if ($status=='negative') {
            $logQuery->andWhere('sum<0');
        }

        $logItems=$logQuery->all();
        $hasMore=count($logItems)>$perPage;

        $data=[];
        foreach(array_slice($logItems,0,$perPage) as $item) {
            $data[]=[
                'dt'=>(new EDateTime($item->dt))->js(),
                'sum'=>$item->sum,
                'type'=>$item->comment,
                'user'=>$item->initiatorUser->getShortData()
            ];
        }

        return [
            'items'=>$data,
            'hasMore'=>$hasMore
        ];
    }

    public function actionLog($pageNum,$status,$sort='-dt') {
        return $this->getLog($pageNum,$status,$sort);
    }


    private function getTokenLog($pageNum,$status,$sort='-dt') {
        $perPage=30;

        $logQuery=BalanceTokenLog::find()->andWhere(['user_id'=>Yii::$app->user->id])->andWhere('(sum>=0.01 or sum<=-0.01)')
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1);

        switch ($sort) {
            case '-dt':
                $logQuery->orderBy(['dt'=>SORT_DESC]);
                $logQuery->from([new \yii\db\Expression('balance_token_log use index(user_id_dt)')]);
                break;
            case 'dt':
                $logQuery->orderBy(['dt'=>SORT_ASC]);
                $logQuery->from([new \yii\db\Expression('balance_token_log use index(user_id_dt)')]);
                break;
            case 'sum':
                $logQuery->orderBy(['sum'=>SORT_ASC]);
                break;
            case '-sum':
                $logQuery->orderBy(['sum'=>SORT_DESC]);
                break;
        }

        if ($status=='positive') {
            $logQuery->andWhere('sum>=0');
        }

        if ($status=='negative') {
            $logQuery->andWhere('sum<0');
        }

        $logItems=$logQuery->all();
        $hasMore=count($logItems)>$perPage;

        $data=[];
        foreach(array_slice($logItems,0,$perPage) as $item) {
            $data[]=[
                'dt'=>(new EDateTime($item->dt))->js(),
                'sum'=>$item->sum,
                'type'=>$item->comment,
                'user'=>$item->initiatorUser->getShortData()
            ];
        }

        return [
            'items'=>$data,
            'hasMore'=>$hasMore
        ];
    }

    public function actionTokenLog($pageNum,$status,$sort='-dt') {
        return $this->getTokenLog($pageNum,$status,$sort);
    }

    private function getTokenDepositLog($pageNum=1) {
        $perPage=30;

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
                'percent_sum'=>$item->percentSum
            ];
        }

        return [
            'items'=>$data,
            'hasMore'=>$hasMore
        ];
    }

    public function actionTokenDepositLog($pageNum) {
        return $this->getTokenDepositLog($pageNum);
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