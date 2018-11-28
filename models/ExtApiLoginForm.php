<?php

namespace app\models;

use app\models\base\ChatUser;
use Yii;


class ExtApiLoginForm extends \app\components\Model
{
    public $username;
    public $password;
    public $facebook_id;
    public $access_token;
    public $type;
    public $device_uuid;

    private $_key = false;
    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password','type','device_uuid'], 'required'],
            [['facebook_id', 'access_token'], 'safe'],
            ['access_token', 'isValidToken','on'=>'facebook_login'],
            ['facebook_id', 'exist','targetClass'=>'app\models\User','message'=>'not_existing','on'=>'facebook_login'],
            // rememberMe must be a boolean value
            ['type', 'match','pattern'=>'%^(ANDROID|IOS|WP)$%','message'=>Yii::t('app',"Can't detect mobile os")],
            // password is validated by validatePassword()
            ['password','validateStatus','skipOnError'=>true],
            ['password', 'validatePassword','skipOnError'=>true]
        ];
    }

    public function attributeLabels() {
        return [
            'username'=>Yii::t('app','Email or Nickname'),
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
                $this->addError('password', Yii::t('app','Falscher Benutzername oder falsches Passwort.'));
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
                $this->addError('password', Yii::t('app','Dein Account ist blockiert, bitte kontaktiere unseren Support.'));
            }

            if ($user->status==User::STATUS_EMAIL_VALIDATION) {
                $this->addError('password', Yii::t('app','Du hast Deine Registrierung noch nicht abgeschlossen. Prüfe Deine E-Mails, dort findest Du eine E-Mail mit dem Bestätigungslink. Bitte klicke auf diesen Link um Deine Registrierung abzuschliessen.'));
            }

            /*
                        if ($user->status==User::STATUS_AWAITING_MEMBERSHIP_PAYMENT) {
                            $this->addError('password', Yii::t('app','Bitte geh auf http://www.jugl.net und wähle ein Mitgliedspaket aus.'));
                        }
            */
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

            if ($user->status==\app\models\User::STATUS_REGISTERED || $user->registration_from_desktop) {
                if (!\app\models\KnownDevice::registerForUser($this->device_uuid,$user)) {
                    $this->addError('username',Yii::t('app',"Über dieses Gerät wurde bereits ein Jugl-Profil erstellt.\nPro User ist nur eine Mitgliedschaft gestattet.\nWenn Du Mitglied werden möchtest und kein Smartphone hast, lass Dir von einem Freund einen VIP-Code für Jugl zusenden.\nDiesen erhält er unter www.jugl.net."));
                    return false;
                }
            }

            $user->failed_logins=0;
            $user->save();

            $userDevice = UserDevice::findOne([
                'type' => $this->type,
                'device_uuid' => $this->device_uuid,
            ]);

            if ($userDevice && $userDevice->user_id != $user->id) {
                $userDevice->delete();
                $userDevice=null;
            }

            if (!$userDevice) {
                $userDevice=new UserDevice();
                $userDevice->type=$this->type;
                $userDevice->device_uuid=$this->device_uuid;
                $userDevice->user_id=$user->id;
            }

            $userDevice->key=Yii::$app->security->generateRandomString(64);

            $userDevice->save();

            $user->processApplicationLogin();
            $user->setUserDevice($userDevice);

            Yii::$app->db->createCommand('
                INSERT INTO user_used_device (user_id, device_uuid) 
                VALUES (:user_id, :device_uuid) 
                ON DUPLICATE KEY UPDATE device_uuid=:device_uuid', [
                    ':user_id'=>$user->id,
                    ':device_uuid'=>$this->device_uuid
            ])->execute();

            return Yii::$app->user->login($user);
        } else {
            return false;
        }
    }
	
	public function loginWithFacebook()
    {
        $user=$this->getUserByFacebookId();

        $this->setScenario('facebook_login');

        if ($this->validate()) {			
				if($user){
					if ($user->status==\app\models\User::STATUS_REGISTERED || $user->registration_from_desktop) {
						if (!\app\models\KnownDevice::registerForUser($this->device_uuid,$user)) {
							$this->addError('username',Yii::t('app',"Über dieses Gerät wurde bereits ein Jugl-Profil erstellt.\nPro User ist nur eine Mitgliedschaft gestattet.Über 'Mitglied werden' kannst du dein Facebook Konto mit deinem vorhandenen Jugl Konto verknüpfen."));
							return false;
						}
					}


					$user->failed_logins=0;
					$user->save();

					$userDevice = UserDevice::findOne([
						'type' => $this->type,
						'device_uuid' => $this->device_uuid,
					]);

					if ($userDevice && $userDevice->user_id != $user->id) {
						$userDevice->delete();
						$userDevice=null;
					}

					if (!$userDevice) {
						$userDevice=new UserDevice();
						$userDevice->type=$this->type;
						$userDevice->device_uuid=$this->device_uuid;
						$userDevice->user_id=$user->id;
					}

					$userDevice->key=Yii::$app->security->generateRandomString(64);

					$userDevice->save();

					$user->processApplicationLogin();
					$user->setUserDevice($userDevice);

					return Yii::$app->user->login($user);
				}
				else{
					$this->addError('facebook_id','not_existing');
					return false;
				}
		} else {  
			$this->addError('facebook_id','not valid');
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
	
	public function getUserByFacebookId()
    {
        if ($this->_user === false) {
            $this->_user = \app\models\User::findByFacebookId($this->facebook_id);
        }
	
        return $this->_user;
    }
	
	public function isValidToken(){
		$app_token='118475612161878|MNy_nFJ-Axa8JBe_uL_qjHKK7l8';
		$url = 'https://graph.facebook.com/debug_token?input_token='.$this->access_token.'&access_token='.$app_token.'';
		$chI = curl_init();
		curl_setopt($chI, CURLOPT_URL, $url);
		curl_setopt($chI, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($chI, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($chI, CURLOPT_SSL_VERIFYPEER, FALSE);
		$data = curl_exec($chI);
		curl_close($chI);
		$data = json_decode($data);
		if($data->data->is_valid){
		return true;	
		}else{
			 $this->addError('access_token', Yii::t('app','Deine Anmeldung wurde aus Sicherheitsgründen verweigert.'));
		}	
	}
	public function existingFacebookId(){
		return var_dump($this->getUserByFacebookId());
	}

    public function scenarios() {
        return array_merge(parent::scenarios(),[
            'normal'=>['username','password','rememberMe'],
            'withCaptcha'=>['username','password','rememberMe','verifyCode'],
			'facebook_login'=>['facebook_id','access_token']
        ]);
    }
}
