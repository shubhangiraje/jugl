<?php

namespace app\components;

use Yii;
use yii\filters\Cors;


class ExtApiCorsFilter extends Cors {

    function beforeAction($action) {
        $res=parent::beforeAction($action);

        if ($res) {
            // if this is CORS Options request, respond with empty content
            if (Yii::$app->request->isOptions) {
                Yii::$app->response->content = '';
                return false;
            }
        }

        return $res;
    }
}