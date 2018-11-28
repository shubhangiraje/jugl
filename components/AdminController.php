<?php

namespace app\components;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

if (!function_exists('getallheaders'))  {
    function getallheaders()
    {
        if (!is_array($_SERVER)) {
            return array();
        }

        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

class AdminController extends Controller {

    public $layout='admin';
    private $adminActionLog;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'user' => 'admin',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }


    public function beforeAction($action) {
        Yii::$app->errorHandler->errorAction='admin-site/error';

        if (!parent::beforeAction($action)) return false;

        if ($this->id!='admin-site' && !Yii::$app->admin->identity->hasAccess($this->route,Yii::$app->request->method)) {
            throw new ForbiddenHttpException();
        }

        if (!Yii::$app->admin->isGuest && Yii::$app->request->isPost) {
            $adminActionLog = new \app\models\AdminActionLog;
            $adminActionLog->admin_id = Yii::$app->admin->id;
            $adminActionLog->dt = (new \app\components\EDateTime())->sqlDateTime();
            $adminActionLog->action = $this->route;
            $adminActionLog->save();
            $this->adminActionLog=$adminActionLog;
        }

        return true;
    }

    public function afterAction($action, $result)
    {
        if (!\Yii::$app->admin->isGuest) {
            \Yii::$app->admin->identity->pollSessionInLog();
        }

        return parent::afterAction($action, $result);
    }


    public function addAdminActionLogComment($comment) {
        if ($this->adminActionLog) {
            if ($this->adminActionLog->comment!='') {
                $this->adminActionLog->comment=$this->adminActionLog->comment."\n";
            }
            $this->adminActionLog->comment=$this->adminActionLog->comment.$comment;
            $this->adminActionLog->save();
        }
    }

    public function pjaxRefresh() {
        $headers=[];

        foreach(getallheaders() as $k=>$v) {
            if (preg_match('%^(cookie|x-|authorization)%i',$k)) {
                $headers[]=$k.': '.$v;
            }
        }

        Yii::$app->session->close();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, Yii::$app->request->referrer);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $content = curl_exec($ch);
        curl_close($ch);

        Yii::$app->response->getHeaders()->set('X-PJAX-URL',Yii::$app->request->referrer);

        return $content;
    }

    public function pjaxRefreshAlert($message) {
        return $this->pjaxRefresh().'<script>setTimeout(function(){alert("'.str_replace('"','\\"',$message).'");},0);</script>';
    }


    public function historyBack() {
        echo '<script>window.history.go(-2);</script>';
        Yii::$app->end();
    }


}