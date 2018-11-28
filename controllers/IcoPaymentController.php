<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PayInRequest;
use app\models\Setting;
use yii\web\Response;
use app\models\IcoPayment;
use yii\widgets\ActiveForm;

class IcoPaymentController extends \app\components\IcoController {

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

        $model = new IcoPayment();
        $model->payment_method=IcoPayment::PAYMENT_METHOD_JUGL;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->payment_method!=IcoPayment::PAYMENT_METHOD_JUGL) {
                $payInRequest=new PayInRequest();
                $payInRequest->type=PayInRequest::TYPE_PAY_IN_TOKEN;
                $payInRequest->jugl_sum=$model->tokens;
                $payInRequest->currency_sum=\app\models\Setting::get('TOKEN_TO_EURO_EXCHANGE_RATE')*$model->tokens;
                $payInRequest->dt=(new \app\components\EDateTime())->sqlDateTime();
                $payInRequest->payment_method=$model->payment_method;
                $payInRequest->return_status=PayInRequest::RETURN_STATUS_AWAITING;
                $payInRequest->confirm_status=PayInRequest::CONFIRM_STATUS_AWAITING;
                $payInRequest->user_id=Yii::$app->user->id;
                $payInRequest->save();

                $data=$payInRequest->getPaymentMethodData(Yii::$app->params['buyTokenSite'].\yii\helpers\Url::toRoute(['index']).
                    '?id='.$this->getProtectedPayInRequestId($payInRequest).'&');

                if ($data['message']) {
                    return $this->render('elv',['data'=>$data]);
                }

                return $this->renderPartial('payment',['data'=>$data]);
            } else {
                $trx=Yii::$app->db->beginTransaction();

                Yii::$app->user->identity->lockForUpdate();
                $juglsSum=Setting::get('TOKEN_TO_JUGL_EXCHANGE_RATE')*$model->tokens;
                if (Yii::$app->user->identity->balance<$juglsSum) {
                    $model->addError('tokens', Yii::t('app', 'You have not enough amount of jugls {sum}', [
                        'sum' => $juglsSum
                    ]));
                } else {
                    $comment=Yii::t('app','{tokens} Tokens für {jugls} Jugls gekauft',[
                        'tokens'=>$model->tokens,
                        'jugls'=>$juglsSum
                    ]);

                    Yii::$app->user->identity->addBalanceLogItem(\app\models\BalanceLog::TYPE_OUT,-$juglsSum,Yii::$app->user->identity,$comment);

                    Yii::$app->user->identity->distributeTokenReferralPayment($model->tokens, Yii::$app->user->identity,
                        \app\models\BalanceTokenLog::TYPE_IN,
                        \app\models\BalanceTokenLog::TYPE_IN_REF,
                        \app\models\BalanceTokenLog::TYPE_IN_REF_REF,
                        Yii::t('app', '{jugl_sum} Tokens für {currency_sum} Jugls gekauft', [
                            'jugl_sum' => $model->tokens,
                            'currency_sum' => $juglsSum
                        ]),0,'','','',true,false,true);

                    $payInRequest=new PayInRequest();
                    $payInRequest->type=PayInRequest::TYPE_PAY_IN_TOKEN;
                    $payInRequest->jugl_sum=$model->tokens;
                    $payInRequest->currency_sum=\app\models\Setting::get('TOKEN_TO_JUGL_EXCHANGE_RATE')*$model->tokens;
                    $payInRequest->dt=(new \app\components\EDateTime())->sqlDateTime();
                    $payInRequest->payment_method=$model->payment_method;
                    $payInRequest->return_status=PayInRequest::RETURN_STATUS_SUCCESS;
                    $payInRequest->confirm_status=PayInRequest::CONFIRM_STATUS_SUCCESS;
                    $payInRequest->user_id=Yii::$app->user->id;
                    $payInRequest->save();

                }

                $trx->commit();

                return $this->redirect(['success']);
            }
        }

        return $this->render('index', ['model'=>$model]);
    }

    public function actionValidate() {
        $model = new IcoPayment();
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