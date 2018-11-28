<?php

namespace app\controllers;

use app\components\Language;
use app\models\Invitation;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\components\Thumb;
use app\models\LoginForm;
use app\components\MyRedirectFilter;


class DummyController extends Controller
{
    public $enableCsrfValidation=false;

    public function behaviors()
    {
        return [
        ];
    }

    public function actionIndex()
    {
        return '';
    }
}
