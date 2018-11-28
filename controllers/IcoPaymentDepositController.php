<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PayInRequest;
use app\models\Setting;
use yii\web\Response;
use app\models\IcoPaymentDeposit;
use yii\widgets\ActiveForm;

class IcoPaymentDepositController extends \app\components\IcoController {

    public $enableCsrfValidation=false;

    private function getPayInRequestModel($id) {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/');
        }

        $id=Yii::$app->security->validateData($id,Yii::$app->params['paymentIDSecret']);
        $model=PayInRequest::findOne(['id'=>$id,'user_id'=>Yii::$app->user->id]);

        if (!$model) {
            return $this->redirect('/');
        }

        return $model;
    }

    private function getProtectedPayInRequestId($model) {
        return Yii::$app->security->hashData($model->id,Yii::$app->params['paymentIDSecret']);
    }

    public function actionSuccess() {
        if ($_GET['id']) {
            $model=$this->getPayInRequestModel($_GET['id']);

            if ($model instanceof \yii\web\Response) {
                return $model;
            }
        } else {
            $model=false;
        }

        return $this->render('success',['model'=>$model]);
    }

    public function actionFailure() {
        $model=$this->getPayInRequestModel($_GET['id']);

        if ($model instanceof \yii\web\Response) {
            return $model;
        }

        return $this->render('failure',['model'=>$model]);
    }

    public function actionIndex() {
        // set session cookie from get param

        $trx=Yii::$app->db->beginTransaction();

        if ($_GET[session_name()]) {
            session_id($_GET[session_name()]);
            Yii::$app->session->open();
        }

        if (Yii::$app->user->isGuest) {
            return $this->redirect(['ico-site/login']);
        }

        if ($_GET['id']) {
            $model=$this->getPayInRequestModel($_GET['id']);

            if ($model instanceof \yii\web\Response) {
                return $model;
            }

            if (isset($_GET['success'])) {
                if ($model->confirm_status==PayInRequest::RETURN_STATUS_SUCCESS) {
                    return $this->redirect(['success','id'=>$this->getProtectedPayInRequestId($model)]);
                }
            }
//            if (isset($_GET['failure'])) {
            if (isset($_GET['cancel'])) {
                return $this->redirect(['failure','id'=>$this->getProtectedPayInRequestId($model)]);
            }

            //if (isset($_GET['cancel'])) {
            //    return $this->redirect(['index']);
            //}
        }

        $model = new IcoPaymentDeposit();
        $model->payment_method=IcoPaymentDeposit::PAYMENT_METHOD_JUGL;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $td= new \app\models\TokenDeposit();
            $td->status=\app\models\TokenDeposit::STATUS_AWAITING_PAYMENT;
            $td->user_id=Yii::$app->user->id;
            $td->sum=$model->tokens;
            $td->period_months=$model->period_months;
            $td->contribution_percentage=\app\models\Setting::get('TOKEN_DEPOSIT_PERCENT_'.$td->period_months.'_MONTHS');
            $td->token_deposit_guarantee_id=$model->token_deposit_guarantee_id;
            $td->buy_currency=$model->payment_method==IcoPaymentDeposit::PAYMENT_METHOD_JUGL ? \app\models\TokenDeposit::BUY_CURRENCY_JUGLS:\app\models\TokenDeposit::BUY_CURRENCY_EUR;
            //$td->save();

            if ($model->payment_method!=IcoPaymentDeposit::PAYMENT_METHOD_JUGL) {
                $payInRequest=new PayInRequest();
                $payInRequest->type=PayInRequest::TYPE_PAY_IN_TOKEN_DEPOSIT;
                $payInRequest->jugl_sum=$model->tokens;
                $payInRequest->currency_sum=\app\models\Setting::TOKEN_DEPOSIT_TOKEN_TO_EURO_EXCHANGE_RATE*$model->tokens;
                $payInRequest->dt=(new \app\components\EDateTime())->sqlDateTime();
                $payInRequest->payment_method=$model->payment_method;
                $payInRequest->return_status=PayInRequest::RETURN_STATUS_AWAITING;
                $payInRequest->confirm_status=PayInRequest::CONFIRM_STATUS_AWAITING;
                $payInRequest->user_id=Yii::$app->user->id;
                $payInRequest->save();

                $td->pay_in_request_id=$payInRequest->id;
                $td->buy_sum=$payInRequest->currency_sum;
                $td->save();

                $trx->commit();

                $data=$payInRequest->getPaymentMethodData(Yii::$app->params['buyTokenSite'].\yii\helpers\Url::toRoute(['index']).
                    '?id='.$this->getProtectedPayInRequestId($payInRequest).'&');

                if ($data['message']) {
                    return $this->render('elv',['data'=>$data]);
                }

                return $this->renderPartial('payment',['data'=>$data]);
            } else {

                Yii::$app->user->identity->lockForUpdate();
                $juglsSum=\app\models\Setting::TOKEN_DEPOSIT_TOKEN_TO_JUGL_EXCHANGE_RATE*$model->tokens;
                if (Yii::$app->user->identity->balance<$juglsSum) {
                    $model->addError('tokens', Yii::t('app', 'You have not enough amount of jugls {sum}', [
                        'sum' => $juglsSum
                    ]));
                } else {

                    $comment=Yii::t('app','{tokens} Tokens für {jugls} Jugls festgelegt',[
                        'tokens'=>$model->tokens,
                        'jugls'=>$juglsSum
                    ]);

                    Yii::$app->user->identity->addBalanceLogItem(\app\models\BalanceLog::TYPE_OUT,-$juglsSum,Yii::$app->user->identity,$comment);

                    $payInRequest=new PayInRequest();
                    $payInRequest->type=PayInRequest::TYPE_PAY_IN_TOKEN_DEPOSIT;
                    $payInRequest->jugl_sum=$model->tokens;
                    $payInRequest->currency_sum=\app\models\Setting::TOKEN_DEPOSIT_TOKEN_TO_JUGL_EXCHANGE_RATE*$model->tokens;
                    $payInRequest->dt=(new \app\components\EDateTime())->sqlDateTime();
                    $payInRequest->payment_method=$model->payment_method;
                    $payInRequest->return_status=PayInRequest::RETURN_STATUS_SUCCESS;
                    $payInRequest->confirm_status=PayInRequest::CONFIRM_STATUS_SUCCESS;
                    $payInRequest->user_id=Yii::$app->user->id;
                    $payInRequest->save();

                    $td->pay_in_request_id=$payInRequest->id;
                    $td->buy_sum=$payInRequest->currency_sum;
                    $td->save();
                    // changing status is checked in model's beforeUpdate
                    $td->status=\app\models\TokenDeposit::STATUS_ACTIVE;
                    $td->save();
                }

                $trx->commit();

                return $this->redirect(['success']);
            }
        }

        return $this->render('index', ['model'=>$model]);
    }

    public function actionValidate() {
        $model = new IcoPaymentDeposit();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
            \Yii::$app->end();
        }
    }



    public function actionMessage() {
        return $this->render('message');
    }



}