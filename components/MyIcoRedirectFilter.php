<?php

namespace app\components;

use Yii;
use yii\base\ActionFilter;

class MyIcoRedirectFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        $route=$action->controller->id.'/'.$action->id;
        if (!Yii::$app->user->isGuest && !preg_match('%^ico-site/(dashboard|token-deposit-payout|token-percent-payout|logout|error|captcha)$%',$route)) {
            return Yii::$app->response->redirect(['ico-site/dashboard']);
        }

        return parent::beforeAction($action);
    }
}
