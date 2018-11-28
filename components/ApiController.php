<?php

namespace app\components;

use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use app\models\User;
use Yii;

class ApiController extends Controller {
    use \app\components\ControllerDeadlockHandler;

    public function beforeAction($action) {
        if (!parent::beforeAction($action)) return false;

        if (Yii::$app->user->isGuest && !preg_match('%^(api-funds-pay-in-data/(confirm|process-hbci-transactions))%',$this->route)) {
            throw new ForbiddenHttpException();
        }

        if (Yii::$app->user->identity->status==User::STATUS_REGISTERED &&
            //!preg_match('%^(api-user/status$|api-registration-payment|api-funds-pay-in-data/)%',$this->route)) {
            !preg_match('%^(api-user/(status|auto-update-country|update-pixel-registration-notified)|api-dashboard/index(-new)?|api-user-profile/settings|api-profile/save-desktop)$%',$this->route)) {
            //Yii::$app->session->destroy();
            throw new ForbiddenHttpException();
        }

        if (Yii::$app->user->identity->status==User::STATUS_BLOCKED ||
            Yii::$app->user->identity->status==User::STATUS_DELETED) {
            Yii::$app->session->destroy();
            throw new ForbiddenHttpException();
        }

        if (!Yii::$app->user->isGuest && $this->route=='api-user/status') {
            Yii::$app->user->identity->logActivity();
        }

        Language::setLanguage();

        return true;
    }


    public function afterAction($action, $result)
    {
        $result['serverTime']=(new \app\components\EDateTime())->js();

        $result = parent::afterAction($action, $result);

        Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
        //Yii::$app->response->content=$result;

        $content = json_encode($result);

        // protection against JSONP attacks (see JSON Vulnerability Protection in $http documentation)
        $content = ")]}',\n" . $content;

        Yii::$app->response->getHeaders()->set('Content-Type', 'application/json; charset=UTF-8');
        // set content length for correct working of nginx gzip_min_len directive
        Yii::$app->response->getHeaders()->set('Content-Length', strlen($content));

        Yii::$app->response->content=$content;
    }

}