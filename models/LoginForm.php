<?php

namespace app\models;

use Yii;


class LoginForm extends \app\components\Model
{
    public $username;
    public $existing_account;
    public $facebook_id;
    public $password;
    public $existing_password;
    public $verifyCode;
    public $rememberMe;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            [['existing_account', 'existing_password'], 'required','on'=>'normal_link'],
            [['facebook_id'],'exist','targetClass'=>'app\models\User','message'=>Yii::t('app','Diese Facebook id existiert nicht!')],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            ['verifyCode', 'captcha'],
            // password is validated by validatePassword()
            ['password','validateStatus','skipOnError'=>true],
            ['existing_password','validateStatusExisting','skipOnError'=>true,'on'=>'normal_link'],
            ['password', 'validatePassword','skipOnError'=>true],
            ['existing_password', 'validatePasswordExisting','skipOnError'=>true,'on'=>'normal_link']
        ];
    }

    public function attributeLabels() {
        return [
            'username'=>Yii::t('app','Email or Nickname'),
            'password'=>Yii::t('app','Password'),
            'rememberMe'=>Yii::t('app','Remember Me'),
            'verifyCode'=>Yii::t('app','Code eingeben')
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', Yii::t('app','Falscher Benutzername oder falsches Passwort.'));
            }
        }
    }
	public function validatePasswordExisting()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserExisting();

            if (!$user || !$user->validatePassword($this->existing_password)) {
                $this->addError('existing_password', Yii::t('app','Falscher Benutzername oder falsches Passwort.'));
            }

        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validateStatus()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if ($user->status==User::STATUS_BLOCKED || $user->status==User::STATUS_DELETED) {
                $this->addError('password', Yii::t('app','Your account is blocked, please contact support.'));
            }

            if ($user->status==User::STATUS_EMAIL_VALIDATION) {
                $this->addError('password', Yii::t('app','Du hast Deine Registrierung noch nicht abgeschlossen. Prüfe Deine E-Mails, dort findest Du eine E-Mail mit dem Bestätigungslink. Bitte klicke auf diesen Link um Deine Registrierung abzuschliessen.'));
            }

        }
    }
	public function validateStatusExisting()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserExisting();

            if ($user->status==User::STATUS_BLOCKED || $user->status==User::STATUS_DELETED) {
                $this->addError('existing_password', Yii::t('app','Your account is blocked, please contact support.'));
            }

            if ($user->status==User::STATUS_EMAIL_VALIDATION) {
                $this->addError('existing_password', Yii::t('app','Du hast Deine Registrierung noch nicht abgeschlossen. Prüfe Deine E-Mails, dort findest Du eine E-Mail mit dem Bestätigungslink. Bitte klicke auf diesen Link um Deine Registrierung abzuschliessen.'));
            }

        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        $user=$this->getUser();

        $this->setScenario('normal');
        if ($user && $user->failed_logins>=3) $this->setScenario('withCaptcha');
        
        if ($this->validate()) {
            $user->failed_logins=0;
            $user->save();

            return Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0);
        } else {
            return false;
        }
    }
	public function loginExisting()
    {
        $user=$this->getUserExisting();

        $this->setScenario('normal_link');

		if ($this->validate()) {
            $user->failed_logins=0;
            $user->save();

            return Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0);
        } else {
            return false;
        }
    }
	public function loginWithFacebookId()
    {
        $user=$this->getUserByFacebookId();

        $this->setScenario('facebook_login');

        if ($this->validate()) {
            $user->failed_logins=0;
            $user->save();
            return Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0);
        } else {
            return false;
        }
    }
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = \app\models\User::findByUsername($this->username);
        }
        return $this->_user;
    }

	public function getUserExisting()
    {
        if ($this->_user === false) {
            $this->_user = \app\models\User::findByUsername($this->existing_account);
        }
        return $this->_user;
    }

	public function getUserByFacebookId()
    {
        if ($this->_user === false) {
            $this->_user = \app\models\User::findByFacebookId($this->facebook_id);
        }

        return $this->_user;
    }

    public function scenarios() {
        return array_merge(parent::scenarios(),[
            'normal'=>['username','password','rememberMe'],
            'normal_link'=>['existing_account','existing_password'],
            'withCaptcha'=>['username','password','rememberMe','verifyCode'],
			'facebook_login'=>['facebook_id']
        ]);
    }
}
