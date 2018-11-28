<?php

namespace app\controllers;

use Yii;
use app\components\EDateTime;
use app\models\BalanceLog;
use app\components\Helper;


class ApiFundsLogController extends \app\components\ApiController {

    public function getLog($year='', $month='', $pageNum=1, $status='all', $sort='-dt') {
        $perPage=50;

        $logQuery=BalanceLog::find()->andWhere(['user_id'=>Yii::$app->user->id])//->andWhere('(sum>=0.01 or sum<=-0.01)')
            ->from([new \yii\db\Expression('balance_log use index(user_id_dt)')])
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage+1)
            ->with(['initiatorUser','initiatorUser.avatarFile']);


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

        if ($year>0 && $month>0) {
            $dateFrom=(new EDateTime())->setDate($year,$month,1);
            $dateTo=$dateFrom->modifiedCopy('+1 MONTH');
            $logQuery->andWhere('dt>=:date_from and dt<:date_to',[
                ':date_from'=>$dateFrom->sql(),
                ':date_to'=>$dateTo->sql()
            ]);
        }

        if ($year>0 && $month=='') {
            $dateFrom=(new EDateTime())->setDate($year,1,1);
            $dateTo=$dateFrom->modifiedCopy('+1 YEAR');
            $logQuery->andWhere('dt>=:date_from and dt<:date_to',[
                ':date_from'=>$dateFrom->sqlDate(),
                ':date_to'=>$dateTo->sqlDate()
            ]);
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
                'type'=>$item->comment,
                'sum'=>$item->sum,
                'user'=>$item->initiatorUser->getShortData()
            ];
        }

        return [
            'log'=>[
                'items'=>$data,
                'hasMore'=>$hasMore
            ]
        ];
    }

    public function actionLog($year,$month,$pageNum,$status,$sort) {
        return $this->getLog($year,$month,$pageNum,$status,$sort);
    }

    public function actionIndex() {
        return array_merge(
            [
                'stats'=>[
                    'balance'=>Yii::$app->user->identity->balance,
                    'earned_this_year'=>Yii::$app->user->identity->earnedThisYear,
                    'earned_today'=>Yii::$app->user->identity->earnedToday,
                    'earned_yesterday'=>Yii::$app->user->identity->earnedYesterday,
                    'earned_this_month'=>Yii::$app->user->identity->earnedThisMonth,
                    'earned_total'=>Yii::$app->user->identity->earnedTotal
                ],
                'monthList'=>Helper::assocToRecords(Helper::addEmptyValue(Helper::getMonthsList(0),0,'')),
                'yearList'=>Helper::assocToRecords(Helper::addEmptyValue(Helper::getYearsFromToList(
                    (new EDateTime(Yii::$app->user->identity->registration_dt))->format('Y'),
                    (new EDateTime(''))->format('Y')
                ),0)),
                'currentYear'=>intval((new EDateTime(''))->format('Y')),
                'statusList'=>Helper::assocToRecords(Helper::getStatusList()),
            ],
            $this->getLog((new EDateTime(''))->format('Y'))
        );
    }
}
