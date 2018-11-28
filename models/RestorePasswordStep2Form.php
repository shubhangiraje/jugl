<?php

namespace app\models;

use Yii;
use yii\base\Model;

class RestorePasswordStep2Form extends Model
{
    public $object;
    public $code;
    public $email;
	public $password;
    public $password_repeat;

	/**
	 * @return array the validation rules.
	 */
	public function rules()
	{
		return [
            [['email','password','password_repeat','object','code'], 'required'],
            [['password'],'compare','compareAttribute'=>'password_repeat'],
			[['email'], 'exist','targetClass'=>'app\models\User','targetAttribute'=>'email','message'=>Yii::t('app',"User with this email doesn't exist")],
            [['code'],'validateAccessCode'],
            [['password'],'isPassword']
		];
	}

    public function validateAccessCode($attribute) {
        if (!AccessCode::isCodeValid(AccessCode::TYPE_RESTORE_PASSWORD,$this->object,$this->code)) {
            $this->addError($attribute,Yii::t('app','Invalid protection code'));
        }
    }

    public function isPassword() {
        if(strlen($this->password) < 6) {
            $this->addError('password', Yii::t('app', 'Das eingegebene Passwort muss mindestens 6 Zeichen haben'));
            return;
        }

        if(!preg_match('/(?=.*\d)(?=.*[a-zA-Z]).*$/', $this->password)) {
            $this->addError('password', Yii::t('app', 'Das eingegebene Passwort muss mindestens eine Buchstabe und eine Ziffer beinhalten'));
            return;
        }
    }

    public function attributeLabels() {
        return [
            'email'=>Yii::t('app','Email'),
            'password'=>Yii::t('app','New Password'),
            'password_repeat'=>Yii::t('app','Password Repeat')
        ];
    }

    public function restorePassword() {
        $this->object=$_GET['object'];
        $this->email=$_GET['object'];
        $this->code=$_GET['code'];
        if (!$this->validate(['code'])) return false;

        if ($this->load($_POST) && $this->validate()) {
            $trx=Yii::$app->db->beginTransaction();

            AccessCode::deleteCode(AccessCode::TYPE_RESTORE_PASSWORD,$this->object);

            $user=User::find()->where(['email'=>$this->email])->one();
            if ($user) {
                $user->password=$this->password;
                $user->encryptPwd();
                $user->failed_logins=0;
                $user->save();
            }

            $trx->commit();

            return true;
        }

        return false;
    }
}
