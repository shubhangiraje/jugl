<?php

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;

class ApiOfferJuglSearchController extends \app\components\ApiController {

    public function actionIndex() {
        return [
            'result'=>true
        ];
    }


}
