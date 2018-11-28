<?php

namespace app\models;

use Yii;


class RegistrationDataForm extends \app\components\Model
{
    public $email;
    public $facebook_id;
    public $password;
	public $first_name;
    public $last_name;
    public $sex;
	
    public $birth_day;
    public $birth_month;
    public $birth_year;
    public $city;
	
    public $email_repeat;
    public $password_repeat;
	public $country_id;
	public $existing_account;
	public $existing_password;
	private $_user = false;

/*
    
    public $nick_name;
    public $company_name;
    public $password_repeat;
    public $birth_day;
    public $birth_month;
    public $birth_year;
    public $phone;
    public $verifyCode;
    public $terms_service;
    public $request_help;
*/

    public function rules()
    {
        return [
			[['first_name'], 'required', 'message'=>Yii::t('app', 'Das Feld Vorname darf nicht leer sein.'),'except'=>'linkFacebook'],
			[['last_name'], 'required', 'message'=>Yii::t('app', 'Das Feld Nachname darf nicht leer sein.'),'except'=>'linkFacebook'],
			//[['city'],'required', 'message'=>Yii::t('app', 'Bitte gib deinen Wohnort an.'),'except'=>['linkFacebook','becomeMember']],
			[['sex'],'required', 'message'=>Yii::t('app', 'Bitte gib Dein Geschlecht an.'),'except'=>['linkFacebook']],
			//[['birth_day'],'required','message'=> Yii::t('app','Bitte gib den Tag deiner Geburt an.'),'except'=>['linkFacebook','becomeMember']],
            //[['birth_month'],'required','message'=> Yii::t('app','Bitte gib den Monat deiner Geburt an.'),'except'=>['linkFacebook','becomeMember']],
            //[['birth_year'],'required','message'=> Yii::t('app','Bitte gib das Jahr deiner Geburt an.'),'except'=>['linkFacebook','becomeMember']],
            //['birth_day','validateBirthday','except'=>['linkFacebook','becomeMember']],
            [['email'], 'trim'],
            [['email'], 'required', 'message'=>Yii::t('app', 'Das Feld E-Mail darf nicht leer sein.'),'except'=>['helpRequest','linkFacebook']],
            [['country_id'], 'required', 'message'=>Yii::t('app', 'Das Feld Land darf nicht leer sein.'),'except'=>'linkFacebook'],
            ['email','email'],
            [['existing_account'],'email','on'=>'linkFacebook'],
            [['existing_account','facebook_id'],'required','on'=>'linkFacebook'],
            ['facebook_id','safe'],
            ['facebook_id','unique','targetClass'=>'app\models\User','message'=>Yii::t('app','User mit diesem Facebook Konto existiert bereits.'),'except'=>'linkFacebook'],
            ['existing_account','checkExistingAccount','skipOnError'=>false, 'on'=>'linkFacebook'],
            ['email','string','max'=>128],
            ['email','unique','targetClass'=>'app\models\User','message'=>Yii::t('app','User mit dieser Emailadresse existiert bereits.'),'except'=>'helpRequest'],
            [['password'], 'required', 'message'=>Yii::t('app', 'Das Feld Passwort darf nicht leer sein.')],
            [['password_repeat'], 'required', 'message'=>Yii::t('app', 'Das Feld Passwort wiederholen darf nicht leer sein.')],
            [['existing_password'], 'required', 'on'=>'linkFacebook','message'=>Yii::t('app', 'Das Feld Passwort darf nicht leer sein.')],
            [['password'],'isPassword'],
            [['email'],'validateIP'],
            [['email_repeat', 'password_repeat'], 'required', 'on'=>['becomeMember']],
            ['email_repeat','compare','compareAttribute'=>'email'],
            ['password_repeat','compare','compareAttribute'=>'password'],
			[['first_name','last_name'],'string','max'=>64]
			//[['country_id'],'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id'], 'message'=>Yii::t('app', 'Dieses Land existiert nicht.')],
/*
            [['first_name'], 'required', 'message'=>Yii::t('app', 'Das Feld Vorname darf nicht leer sein.')],
            [['last_name'], 'required', 'message'=>Yii::t('app', 'Das Feld Nachname darf nicht leer sein.')],
            [['phone'], 'required', 'message'=>Yii::t('app', 'Das Feld Telefonnummer darf nicht leer sein.'),'except'=>'helpRequest'],
            [['verifyCode'], 'required', 'message'=>Yii::t('app', 'Captcha Code darf nicht leer sein.')],
            [['password_repeat'], 'required', 'message'=>Yii::t('app', 'Das Feld Passwort wiederholen darf nicht leer sein.')],
            [['sex'],'required', 'message'=>Yii::t('app', 'Bitte gib Dein Geschlecht an')],
            [['terms_service'],'compare','compareValue'=> true, 'message'=>Yii::t('app','Bitte lesen Sie die Nutzungsbedingungen und akzeptieren diese.')],
            ['nick_name','match','pattern'=>'%@%','not'=>true,'message'=>Yii::t('app','Nickname can\'t contain symbol @')],
            [['first_name','last_name','nick_name', 'company_name'],'string','max'=>64],
            ['verifyCode','captcha','on'=>'withCaptcha', 'message'=>Yii::t('app', 'Bitte korrekten Captcha Code eingeben.'),'except'=>'helpRequest'],
            ['password_repeat','compare','compareAttribute'=>'password','message'=>Yii::t('app','Passwortwiederholung falsch')],
            [['birth_day'],'required','message'=> Yii::t('app','Bitte gib den Tag deiner Geburt an.')],
            [['birth_month'],'required','message'=> Yii::t('app','Bitte gib den Monat deiner Geburt an.')],
            [['birth_year'],'required','message'=> Yii::t('app','Bitte gib das Jahr deiner Geburt an.')],
            ['birth_day','validateBirthday'],
            ['nick_name','unique','targetClass'=>'app\models\User','message'=>Yii::t('app','Dieser Spitzname/Nickname ist bereits vergeben.')],
            ['nick_name','default','value'=>null],
            ['phone','match','pattern'=>'%^\d{4} ?\d{6,10}$%','message'=>Yii::t('app','Gib Deine Mobilnummer wie folgt ein: 0151 111222333'),'except'=>'helpRequest'],
            ['phone','unique','targetClass'=>'app\models\User','message'=>Yii::t('app','User mit dieser Telefonnummer existiert bereits.'),'except'=>'helpRequest'],
            ['request_help','safe'],
            [['phone'],'validateContacts','skipOnEmpty'=>false,'on'=>'helpRequest']
*/
        ];
    }


    public function scenarios() {
        $scenarios=parent::scenarios();

        $scenarios['becomeMember']=[
            'first_name','last_name','email','password','email_repeat','password_repeat','country_id'
        ];
		$scenarios['becomeMemberNew']=[
            'first_name','last_name','sex','city','birth_day','birth_month','birth_year','email','password','password_repeat','country_id','facebook_id'
        ];
		
		$scenarios['linkFacebook']=[
            'existing_account','existing_password'
        ];

        return $scenarios;
    }



/*
    public function saveHelpRequest($step,$user_id=null) {
        $hr=new RegistrationHelpRequest();
        $hr->load($this->attributes,'');
        $hr->dt=(new \app\components\EDateTime())->sqlDateTime();
        $hr->ip=Yii::$app->request->userIP;
        $hr->step=$step;
        $hr->user_id=$user_id;

        $this->validateBirthday(null,null);
        if (!$this->hasErrors() && !empty($this->birth_day) && !empty($this->birth_month) && !empty($this->birth_year)) {
            $date=new \app\components\EDateTime();
            $date->setDate($this->birth_year,$this->birth_month,$this->birth_day);
            $hr->birthday=$date->sqlDate();
        }

        $hr->save();
    }

    public function validateContacts($attribute,$params)
    {
        if (trim($this->phone).trim($this->email)=='') {
            $this->addError('phone', Yii::t('app','Bitte Mobilnummber oder E-mail eingeben.'));
        }
    }
	*/
    public function validateBirthday($attribute,$params) {
        if(!empty($this->birth_day) && !empty($this->birth_month) && !empty($this->birth_year)) {
            if (!checkdate($this->birth_month,$this->birth_day,$this->birth_year)) {
                $this->addError('birth_day', Yii::t('app','Bitte Geburtsdatum eingeben.'));
            }
        }
    }

    public function validateIP($attribute,$params)
    {
        /*
        $ipUsages=Yii::$app->db->createCommand("select count(*) from user where registration_ip=:ip",[':ip'=>Yii::$app->request->userIP])->queryScalar();

        if ($ipUsages>=2) {
            $this->addError('email', Yii::t('app','Aus Sicherheitsgründen können von diesem Rechner keine Accounts mehr erstellt werden. Bitte wenden Sie sich an juglapp@gmx.de'));
        }
        */
    }
	public function checkExistingAccount()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserByShortcutEmail();

			if($this->existing_password){
				if (!$user || !$user->validatePassword($this->existing_password)) {
					$this->addError('existing_account', Yii::t('app','Falscher Benutzername oder falsches Passwort.'));                            
				}
			}
			else{
			  $this->addError('existing_password', Yii::t('app','Passwort darf nicht leer sein!')); 	
			}
			return $this->_user;
			
        }
    }
	public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = \app\models\User::findByUsername($this->email);
        }
        return $this->_user;
    }
	
	public function getUserByShortcutEmail()
    {
        if ($this->_user === false) {
            $this->_user = \app\models\User::findByUsername($this->existing_account);
        }
        return $this->_user;
    }


    public function isPassword() {
        if(strlen($this->password) < 6) {
            $this->addError('password', Yii::t('app', 'Das eingegebene Passwort muss mindestens 6 Zeichen und einen Buchstaben und eine Ziffer beinhalten.'));
            return;
        }

        if(!preg_match('/(?=.*\d)(?=.*[a-zA-Z]).*$/', $this->password)) {
            $this->addError('password', Yii::t('app', 'Das eingegebene Passwort muss mindestens eine Buchstabe und eine Ziffer beinhalten'));
            return;
        }
    }
    /*
    public function scenarios() {
        $scenarios=parent::scenarios();
        $scenarios['withCaptcha']=$scenarios[static::SCENARIO_DEFAULT];
        $scenarios['helpRequest']=['phone','email'];

        return $scenarios;
    }
    */

    public function attributeLabels() {
        return [
           
            'first_name'=>Yii::t('app','First Name'),
            'last_name'=>Yii::t('app','Last Name'),
             /*'nick_name'=>Yii::t('app','Nickname'),
            */
            'email'=>Yii::t('app','Email'),
            'existing_account'=>Yii::t('app','vorhandenes Jugl Konto'),
            'existing_password'=>Yii::t('app','Passwort deines Jugl Kontos'),
            'password'=>Yii::t('app','Password'),
            'email_repeat'=>Yii::t('app', 'Email wiederholen'),
            'password_repeat'=>Yii::t('app', 'Passwort wiederholen'),
            'country_id'=>Yii::t('app', 'Land auswählen'),
			
            /*
            'password_repeat'=>Yii::t('app','Password repeat'),
            'phone_prefix'=>Yii::t('app','Phone prefix'),
            'phone_suffix'=>Yii::t('app','Phone suffix'),
            'verifyCode'=>Yii::t('app','Code'),
            'company_name' => Yii::t('app', 'Firmenname'),
            'terms_service' => Yii::t('app','Terms of service')
            */
        ];
    }
}
