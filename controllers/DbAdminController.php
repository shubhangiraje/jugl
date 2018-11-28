<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

/**
 * AdminUserController implements the CRUD actions for User model.
 */
class DbAdminController extends Controller
{
	
    public function actionIndex()
    {
       return $this->render(Yii::$app->basePath.'/moosd/index.php');
    }

    
}