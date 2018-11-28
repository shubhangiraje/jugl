<?php

namespace app\models;

use Yii;
use app\components\EDateTime;
use yii\web\IdentityInterface;

class Admin extends \app\models\base\Admin implements IdentityInterface
{
    const STATUS_ACTIVE='ACTIVE';
    const STATUS_BLOCKED='BLOCKED';

    const TYPE_SUPERVISOR='SUPERVISOR';
    const TYPE_MANAGER='MANAGER';

    private $_plainPassword;

    public static function getList() {
        $items=[];
        foreach(static::find()->orderBy('first_name asc, last_name asc')->all() as $model) {
            $items[$model->id]=$model->name;
        }
        return $items;
    }

    public function getName() {
        return trim($this->first_name.' '.$this->last_name);
    }

    public static function getStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::STATUS_ACTIVE=>Yii::t('app','ADMIN_STATUS_ACTIVE'),
                static::STATUS_BLOCKED=>Yii::t('app','ADMIN_STATUS_BLOCKED'),
            ];
        }

        return $items;
    }

    public static function getTypeList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::TYPE_SUPERVISOR=>Yii::t('app','Supervisor'),
                static::TYPE_MANAGER=>Yii::t('app','Manager'),
            ];
        }

        return $items;
    }

    public function getStatusLabel() {
        return static::getStatusList()[$this->status];
    }

    public function getTypeLabel() {
        return static::getTypeList()[$this->type];
    }

    public function __toString() {
        return $this->email;
    }

    public function getPlainPassword() {
        return $this->_plainPassword;
    }

    public function setPlainPassword($value) {
        $this->_plainPassword=$value;

        if ($value!='') {
            $this->password = Yii::$app->security->generatePasswordHash($value);
        }
    }

    public function attributeLabels() {
        return array_merge(parent::attributeLabels(),[
            'status' => Yii::t('app', 'Status'),
            'type' => Yii::t('app', 'Berechtigung'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'plainPassword'=>Yii::t('app','New Password'),
            'access_dashboard'=>Yii::t('app','Sichtbarkeit der Auswertung auf der "Startseite" des Backends'),
            'access_user_view'=>Yii::t('app','Nutzerdaten einsehen'),
            'access_user_update'=>Yii::t('app','Nutzerdaten bearbeiten'),
            'access_user_validation'=>Yii::t('app','Identitätsprüfungen durchführen'),
            'access_payouts'=>Yii::t('app','Auszahlungen durchführen'),
            'access_interests'=>Yii::t('app','Interessen bearbeiten'),
            'access_search_request_view'=>Yii::t('app','Suchaufträge einsehen'),
            'access_search_request_validate'=>Yii::t('app','Suchaufträge bestätigen/ablehnen'),
            'access_search_request_update'=>Yii::t('app','Suchaufträge bearbeiten'),
            'access_offer_view'=>Yii::t('app','Angebote einsehen'),
            'access_offer_validate'=>Yii::t('app','Angebote bestätigen/ablehnen'),
            'access_offer_update'=>Yii::t('app','Angebote bearbeiten'),
            'access_settings'=>Yii::t('app','Einstellungen bearbeiten'),
            'access_broadcast'=>Yii::t('app','Nachricht an alle User senden'),
            'access_news'=>Yii::t('app','News erstellen und bearbeiten'),
			'access_translator'=>Yii::t('app','Übersetzer'),
        ]);
    }

    public function scenarios() {
        $scenarios=parent::scenarios();

        $scenarios['update']=['email','status','first_name','last_name','plainPassword','type',
            'access_dashboard',
            'access_user_view',
            'access_user_update',
            'access_user_validation',
            'access_payouts',
            'access_interests',
            'access_search_request_view',
            'access_search_request_validate',
            'access_search_request_update',
            'access_offer_view',
            'access_offer_validate',
            'access_offer_update',
            'access_settings',
            'access_broadcast',
            'access_news',
            'access_translator',
        ];

        $scenarios['create']=$scenarios['update'];

        return $scenarios;
    }

    public function rules() {
        return array_merge(parent::rules(),[
            ['plainPassword','required','on'=>'create'],
        ]);
    }

    public function beforeValidate() {
        if ($this->getScenario()=='create') {
            $this->access_token=Yii::$app->security->generateRandomString(32);
            $this->auth_key=Yii::$app->security->generateRandomString(32);
        }
        return parent::beforeValidate();
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function getId()
    {
        return $this->id;
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public static function findByUsername($username) {
        return static::find()->where('email=:username',[':username'=>$username])->one();
    }

    public function validatePassword($password) {
        $passwordMatch=Yii::$app->security->validatePassword($password,$this->password);

        if (!$passwordMatch) {
            $this->failed_logins++;
            $this->save();
        }

        return $passwordMatch;
    }

    public function encryptPwd()
    {
        $this->password = Yii::$app->security->generatePasswordHash($this->password);
    }


    public function hasAccess($route,$method=false) {
/*
        echo "PONG";
        if ($_POST['OK']) {var_dump($method);exit;}
        echo "PING";
*/
        $allowed=[];

        if ($this->type==static::TYPE_SUPERVISOR) {
            $allowed[]='.*';
        }

        if ($this->access_dashboard) {
            $allowed[]='admin-site/index';
        }

        if ($this->access_user_view) {
            $allowed[]='admin-user/(index|update#GET)';
        }

        if ($this->access_user_validation) {
            $allowed[]='admin-user-validation/.*';
        }

        if ($this->access_user_update) {
            $allowed[]='admin-user/.*';
        }

        if ($this->access_payouts) {
            $allowed[]='admin-pay-out-request/.*|admin-user/update#GET';
        }

        if ($this->access_interests) {
            $allowed[]='admin-(interest|interest-param|param|param-value)/.*';
        }

        if ($this->access_search_request_view) {
            $allowed[]='admin-search-request/(index|update#GET)';
            $allowed[]='admin-search-request-offer/.*#GET';
        }

        if ($this->access_offer_view) {
            $allowed[]='admin-offer/(index|update#GET)';
            $allowed[]='admin-offer-request/.*#GET';
        }

        if ($this->access_search_request_update) {
            $allowed[]='admin-search-request/(?!(control|accept|reject|pause)).*';
            $allowed[]='admin-search-request-offer/.*';
        }

        if ($this->access_offer_update) {
            $allowed[]='admin-offer/(?!(control|accept|reject|pause)).*';
            $allowed[]='admin-offer-request/.*';
        }

        if ($this->access_search_request_validate) {
            $allowed[]='admin-search-request/(?!index).*#GET';
            $allowed[]='admin-search-request/(control|accept|reject|pause)';
            $allowed[]='admin-search-request-offer/.*';
            $allowed[]='/admin-user/modal-block-user';
        }

        if ($this->access_offer_validate) {
            $allowed[]='admin-offer/(?!index).*#GET';
            $allowed[]='admin-offer/(control|accept|reject|pause)';
            $allowed[]='admin-offer-request/.*';
            $allowed[]='/admin-user/modal-block-user';
        }

        if ($this->access_news) {
            $allowed[]='admin-news/.*';
        }

        if ($this->access_broadcast) {
            $allowed[]='admin-broadcast/.*';
        }

        if ($this->access_settings) {
            $allowed[]='admin-pay-out-packet/.*';
            $allowed[]='admin-pay-in-packet/.*';
            $allowed[]='admin-setting/.*';
            $allowed[]='admin-default-text/.*';
        }

        $regex='%^('.implode('|',$allowed).')$%';
/*
        if ($_POST['OK']) {
            var_dump($route);
            var_dump($method);
            var_dump($regex);//exit;
        }
*/
        $allowed=preg_match($regex,$route);
        if (!$allowed && !$method) {
            $allowed=preg_match($regex,$route.'#GET');
        }
        if (!$allowed && $method) {
            $allowed=preg_match($regex,$route.'#'.$method);
        }

        return $allowed;
    }

    public function pollSessionInLog() {
        Yii::$app->db->transaction(function($db) {

            $asl = AdminSessionLog::find()->where('session=:session and dt_end>=:dt', [
                ':session' => Yii::$app->session->id,
                ':dt' => (new EDateTime())->sqlDateTime()
            ])->one();

            if ($asl) {
                AdminSessionLog::updateAll(['dt_end' => (new EDateTime())->modify('+' . AdminSessionLog::SESSION_MAX_INACTIVITY . ' sec')->sqlDateTime()], 'session=:session and dt_end>=:dt', [
                    ':session' => Yii::$app->session->id,
                    ':dt' => (new EDateTime())->sqlDateTime()
                ]);
            } else {
                $asl = new AdminSessionLog();
                $asl->admin_id = $this->id;
                $asl->dt_start = (new EDateTime())->sqlDateTime();
                $asl->dt_end = (new EDateTime())->modify('+' . AdminSessionLog::SESSION_MAX_INACTIVITY . ' sec')->sqlDateTime();
                $asl->session = Yii::$app->session->id;
                $asl->ip = Yii::$app->request->userIp;
                $asl->user_agent = Yii::$app->request->userAgent;
                $asl->save();
            }
        });

    }


}
