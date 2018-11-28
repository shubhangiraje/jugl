<?php

namespace app\controllers;

use Yii;
use app\models\PayInRequest;
use yii\web\ForbiddenHttpException;


class ApiFundsPayInDataController extends \app\components\ApiController {

    public function actionIndex($requestId,$retUrl) {
        $payInRequest=PayInRequest::findOne($requestId);

        if (!$payInRequest) {
            throw new ForbiddenHttpException();
        }

        if ($payInRequest->user_id!=Yii::$app->user->id || $payInRequest->return_status!=PayInRequest::RETURN_STATUS_AWAITING) {
            throw new ForbiddenHttpException();
        }

        return $payInRequest->getPaymentMethodData($retUrl);
    }

    public function beforeAction($action)
    {
        if ($this->action->id == 'confirm' || $this->action->id == 'process-hbci-transactions') {
            Yii::$app->controller->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionProcessHbciTransactions() {
        $transactionsData=Yii::$app->request->getBodyParams()['data'];
        $signData=Yii::$app->request->getBodyParams()['sign'];

        if (sha1($transactionsData.Yii::$app->params['HBCISignKey'])!=$signData) {
            echo "INVALID SIGNATURE\n";
            exit;
        }

        $transactions=json_decode($transactionsData,true);

        $countTransactions=count($transactions);
        $countMarkedTransactions=0;
        $countInvalidTransactions=0;
        $countNewTransactions=0;

        $transactionsByCodes=[];
        foreach($transactions as $tx) {
            if (preg_match('#\d{8}-[0-9A-Za-z]{6}#',$tx['purpose'],$matches)) {
                $code=strtoupper($matches[0]);
                $transactionsByCodes[$code][]=$tx;
                $countMarkedTransactions++;
            }
        }

        $payInRequests=PayInRequest::find()->where(['ext_code'=>array_keys($transactionsByCodes)])->all();
        $countInvalidTransactions+=$countMarkedTransactions-count($payInRequests);

        foreach($payInRequests as $payInRequest) {
            $payInRequest->refresh();

            foreach($transactionsByCodes[$payInRequest->ext_code] as $tx) {
                if ($tx['currency']=='EUR' && $tx['sum']+1e-6>=$payInRequest->currency_sum) {
                    if ($payInRequest->confirm_status!=PayInRequest::CONFIRM_STATUS_SUCCESS) {
                        $details=[];
                        foreach($tx as $k=>$v) {
                            $details[]="$k: $v\n";
                        }
                        $payInRequest->details=implode("\n",$details);

                        $trx=Yii::$app->db->beginTransaction();
                        $payInRequest->confirmSuccess();
                        $trx->commit();

                        $countNewTransactions++;
                    }
                } else {
                    $countInvalidTransactions++;
                }
            }
        }

        echo "GOT $countTransactions TRANSACTIONS, $countMarkedTransactions MARKED ($countInvalidTransactions INVALID, $countNewTransactions NEW)\n";
        exit;
    }

    public function actionConfirm() {
        $message="Request params: \n";
        foreach(Yii::$app->request->getBodyParams() as $k=>$v) {
            $message.="$k: $v\n";
        }

        $payInRequest=PayInRequest::findOne(Yii::$app->request->getBodyParam('shopname_payin_id'));

        Yii::info($message,'payment');

        if ($payInRequest) {
            $payInRequest->confirm();
        }

        $message="new payInRequest: ";
        foreach($payInRequest->attributes as $k=>$v) {
            $message.="$k: $v\n";
        }

        Yii::info($message,'payment');

    }
}