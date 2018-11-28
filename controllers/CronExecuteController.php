<?php
namespace app\controllers;

use yii\web\Controller;
use app\controllers\AdminVideoController;
use Yii;

class CronExecuteController extends Controller {
	
	public function actionIndex(){
		AdminVideoController::actionImportmanually(true);
	}
}	
