<?php

namespace app\controllers;

use Yii;
use app\models\PayInRequest;
use yii\web\ForbiddenHttpException;


class ApiFundsPayInResultController extends \app\components\ApiController {

    public function actionIndex() {
        $requestId=Yii::$app->request->getBodyParam('requestId');
        $returnStatus=Yii::$app->request->getBodyParam('returnStatus');

        $payInRequest=PayInRequest::findOne($requestId);

        if (!$payInRequest) {
            throw new ForbiddenHttpException();
        }

        if ($payInRequest->user_id!=Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }

        if ($payInRequest->return_status==PayInRequest::RETURN_STATUS_AWAITING) {
            $payInRequest->return_status = $returnStatus;
            $payInRequest->save();
            $payInRequest->refresh();
        }

        return ['payInRequest'=>$payInRequest->toArray(['id','return_status','confirm_status'])];
    }
}