<?php

namespace app\components;

use Yii;
use yii\web\Controller;

class IcoController extends Controller {
    public $layout = 'ico-payment';

    public function beforeAction($action)
    {
        Yii::$app->request->setHostInfo(Yii::$app->params['buyTokenSite']);
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}