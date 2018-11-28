<?php

namespace app\components;

use Yii;
use yii\base\ActionFilter;

class MyRedirectFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        $route=$action->controller->id.'/'.$action->id;
        if (!Yii::$app->user->isGuest && !preg_match('%^site/(my|logout|set-language|error|captcha|generate-thumbnail|app-membership-payment|test-.*)$%',$route)) {
            return Yii::$app->response->redirect(['site/my']);
        }

        return parent::beforeAction($action);
    }
}
