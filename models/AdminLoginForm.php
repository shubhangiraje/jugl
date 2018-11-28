<?php

namespace app\models;

use Yii;


class AdminLoginForm extends \app\components\Model
{
    public $username;
    public $password;
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
            [['username', 'password','captcha'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            ['verifyCode', 'captcha'],
            // password is validated by validatePassword()
            ['password', 'validatePassword','skipOnError'=>true],
            ['password','validateStatus','skipOnError'=>true]
        ];
    }

    public function attributeLabels() {
        return [
            'username'=>Yii::t('app','Email'),
            'password'=>Yii::t('app','Password'),
            'rememberMe'=>Yii::t('app','Remember Me'),
            'verifyCode'=>Yii::t('app','Verify Code')
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
                $this->addError('password', Yii::t('app','Incorrect username or password.'));
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

            if ($user->status==Admin::STATUS_BLOCKED) {
                $this->addError('password', Yii::t('app','Your account is blocked, please contact support.'));
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

            return Yii::$app->admin->login($user, $this->rememberMe ? 3600*24*30 : 0);
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
            $this->_user = \app\models\Admin::findByUsername($this->username);
        }

        return $this->_user;
    }

    public function scenarios() {
        return array_merge(parent::scenarios(),[
            'normal'=>['username','password','rememberMe'],
            'withCaptcha'=>['username','password','rememberMe','verifyCode']
        ]);
    }
}
