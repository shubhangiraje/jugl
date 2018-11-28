<?php

namespace app\components;

use yii\web\ForbiddenHttpException;
use app\models\User;
use Yii;

class ExtApiController extends \yii\web\Controller {
    use \app\components\ControllerDeadlockHandler;

    public $enableCsrfValidation=false;

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => ExtApiCorsFilter::className(),
            ],
            'authenticator' => [
                'class' => ExtApiAuth::className(),
            ]
        ];
    }

    public function beforeAction($action) {
        if (!parent::beforeAction($action)) return false;

        // disable session
        Yii::$app->user->enableSession = false;

        // set response format
        Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
        Yii::$app->response->headers->add('Cache-Control','no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        Yii::$app->response->headers->add('Pragma','no-cache');

        $language=Yii::$app->request->getHeaders()->get('X-Ext-Api-Language');
        if ($language) {
            Yii::$app->language = $language;
        }

        return true;
    }

    public function afterAction($action, $result)
    {
        $result['serverTime']=(new \app\components\EDateTime())->js();
        return parent::afterAction($action, $result);
    }
}