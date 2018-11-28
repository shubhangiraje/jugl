<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use \app\models\RemoteLog;


class RemoteLogController extends Controller
{
    public $enableCsrfValidation=false;

    public function behaviors()
    {
        return [
            'cors' => [
                'class' => \app\components\ExtApiCorsFilter::className(),
                'actions' => ['log']
            ]
        ];
    }

    public function actionLog() {
        Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;

        $data=Yii::$app->request->getBodyParams();
        if (!$data['sessionId']) {
            return ['result'=>'sessionId is missing'];
        }

        if (!is_array($data['log'])) {
            return ['result'=>'log is not array'];
        }

        $trx=Yii::$app->db->beginTransaction();

        foreach($data['log'] as $logItemData) {
            $rLog=new RemoteLog();

            $ms=$logItemData['dt']%1000;
            while(strlen($ms)<3) $ms="0$ms";

            $rLog->dt=date('Y-m-d H:i:s',floor($logItemData['dt']/1000)).'.'.$ms;
            $rLog->session=$data['sessionId'];
            $rLog->type=$logItemData['type'];

            $message=[];
            if (is_string($logItemData['args'])) {
                $logItemData['args']=[$logItemData['args']];
            }
            foreach($logItemData['args'] as $arg) {
                if (is_array($arg)) {
                    $arg=json_encode($arg);
                }
                $message[]=$arg;
            }

            $rLog->message=implode('|',$message);
            $rLog->save();
        }

        $trx->commit();

        return ['result'=>'OK'];
    }

}
