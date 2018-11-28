<?php

namespace app\models;

use app\components\ChatServer;
use Yii;
use yii\web\IdentityInterface;
use app\models\BalanceLog;
use app\models\UserActivityLog;
use yii\db\Expression;
use app\components\EDateTime;
use app\models\UserReferral;


class User extends \app\models\base\User implements IdentityInterface
{
    const SEX_M = 'M';
    const SEX_F = 'F';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_BLOCKED = 'BLOCKED';
    const STATUS_DELETED = 'DELETED';
    const STATUS_REGISTERED = 'REGISTERED';
    const STATUS_LOGINED = 'LOGINED';
    const STATUS_EMAIL_VALIDATION = 'EMAIL_VALIDATION';
	
	const AD_STATUS_AUTO_ALLOW = 'AD_STATUS_AUTO_ALLOW';
    const AD_STATUS_AUTO_DISABLE = 'AD_STATUS_AUTO_DISABLE';

    const VISIBILITY_NONE = 'none';
    const VISIBILITY_FRIENDS = 'friends';
    const VISIBILITY_ALL = 'all';

    const MARTIAL_STATUS_SINGLE='single';
    const MARTIAL_STATUS_MARRIED='married';
    const MARTIAL_STATUS_VERGEBEN='vergeben';

    const VALIDATION_STATUS_NOT_VALIDATED='NOT_VALIDATED';
    const VALIDATION_STATUS_AWAITING='AWAITING';
    const VALIDATION_STATUS_SUCCESS='SUCCESS';
    const VALIDATION_STATUS_FAILURE='FAILURE';

    const VALIDATION_TYPE_PHOTOS='PHOTOS';

    const PACKET_STANDART='STANDART';
    const PACKET_VIP='VIP';
    const PACKET_VIP_PLUS='VIP_PLUS';

    const VALIDATION_PHONE_STATUS_NOT_VALIDATED='NOT_VALIDATED';
    const VALIDATION_PHONE_STATUS_SEND_CODE='SEND_CODE';
    const VALIDATION_PHONE_STATUS_VALIDATED='VALIDATED';

    const VIDEO_IDENTIFICATION_STATUS_NONE = 'NONE';
    const VIDEO_IDENTIFICATION_STATUS_ACCEPTED_AUTO = 'ACCEPTED_AUTO';
    const VIDEO_IDENTIFICATION_STATUS_ACCEPTED_MANUAL = 'ACCEPTED_MANUAL';
    const VIDEO_IDENTIFICATION_STATUS_REJECTED = 'REJECTED';
    const VIDEO_IDENTIFICATION_STATUS_AWAITING = 'AWAITING';

    const SUPERADMIN_ID=68;

    // for admin-registration-limit/index
    public $cnt,$lim;

    public $oldPassword;
    public $_newPassword;
    public $_newPasswordRepeat;

    public $_birthDay;
    public $_birthMonth;
    public $_birthYear;

    public $last_spam_report_dt;

    private $_plainPassword;

    private $_userDevice;

    public $validation_code_form;
	public $get_user_invited_today;							

	public $_ur_level;

    public function getUserDevice() {
        return $this->_userDevice;
    }

    public function setUserDevice($userDevice) {
        $this->_userDevice=$userDevice;
    }

    public static function blockWithoutParent() {
        $users=static::find()->where('registration_dt<=:dt and parent_id is null and status!=:status_blocked and status!=:status_deleted and registered_by_become_member=1',[
            ':dt'=>(new \app\components\EDateTime())->modify("-".\app\models\Setting::get('TEAM_CHANGE_PERIOD_DAYS')." minute")->sql(),
            ':status_deleted'=>static::STATUS_DELETED,
            ':status_blocked'=>static::STATUS_BLOCKED
        ])->all();


        foreach($users as $user) {
            $trx=Yii::$app->db->beginTransaction();
            $user->status=static::STATUS_BLOCKED;
            $user->save();
            $trx->commit();
        }
    }

    public static function getValidationStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::VALIDATION_STATUS_NOT_VALIDATED=>Yii::t('app','USER_VALIDATION_STATUS_NOT_VALIDATED'),
                static::VALIDATION_STATUS_AWAITING=>Yii::t('app','USER_VALIDATION_STATUS_AWAITING'),
                static::VALIDATION_STATUS_SUCCESS=>Yii::t('app','USER_VALIDATION_STATUS_SUCCESS'),
                static::VALIDATION_STATUS_FAILURE=>Yii::t('app','USER_VALIDATION_STATUS_FAILURE'),
            ];
        }

        return $items;
    }

    public function getValidationStatusLabel() {
        return static::getValidationStatusList()[$this->validation_status];
    }

    public static function getValidationTypeList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::VALIDATION_TYPE_PHOTOS=>Yii::t('app','USER_VALIDATION_TYPE_PHOTOS'),
            ];
        }

        return $items;
    }

    public static function getStatusList() {
        static $items;
        if (!isset($items)) {
            $items=[
                static::STATUS_EMAIL_VALIDATION=>Yii::t('app','USER_STATUS_EMAIL_VALIDATION'),
                static::STATUS_REGISTERED=>Yii::t('app','USER_STATUS_REGISTERED'),
                static::STATUS_LOGINED=>Yii::t('app','USER_STATUS_LOGINED'),
                static::STATUS_ACTIVE=>Yii::t('app','USER_STATUS_ACTIVE'),
                static::STATUS_BLOCKED=>Yii::t('app','USER_STATUS_BLOCKED'),
                static::STATUS_DELETED=>Yii::t('app','USER_STATUS_DELETED'),
            ];
        }
        return $items;
    }
	
	
    public static function getExtStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::STATUS_EMAIL_VALIDATION=>Yii::t('app','USER_STATUS_EMAIL_VALIDATION'),
                static::STATUS_REGISTERED=>Yii::t('app','USER_STATUS_REGISTERED'),
                static::STATUS_LOGINED=>Yii::t('app','USER_STATUS_LOGINED'),
                static::STATUS_ACTIVE=>Yii::t('app','USER_STATUS_ACTIVE'),
                static::STATUS_ACTIVE.'|'.static::PACKET_STANDART=>Yii::t('app','USER_STATUS_ACTIVE').' ('.static::getPacketList()[static::PACKET_STANDART].')',
                static::STATUS_ACTIVE.'|'.static::PACKET_VIP=>Yii::t('app','USER_STATUS_ACTIVE').' ('.static::getPacketList()[static::PACKET_VIP].')',
                static::STATUS_ACTIVE.'|'.static::PACKET_VIP_PLUS=>Yii::t('app','USER_STATUS_ACTIVE').' ('.static::getPacketList()[static::PACKET_VIP_PLUS].')',
                static::STATUS_BLOCKED=>Yii::t('app','USER_STATUS_BLOCKED'),
                static::STATUS_DELETED=>Yii::t('app','USER_STATUS_DELETED'),
            ];
        }

        return $items;
    }
	
	public static function getAdStatusAutoList() {
        static $items;
		
        if (!isset($items)) {
            $items=[
                static::AD_STATUS_AUTO_ALLOW=>Yii::t('app','Erlauben'),
                static::AD_STATUS_AUTO_DISABLE=>Yii::t('app','Nicht Erlauben'),
            ];
        }

        return $items;
    }
	
	public static function getExtAdStatusAutoList() {
        static $items;
		
        if (!isset($items)) {
            $items=[
                static::AD_STATUS_AUTO_ALLOW=>Yii::t('app','USER_AD_STATUS_AUTO_ALLOW'),
                static::AD_STATUS_AUTO_DISABLE=>Yii::t('app','USER_AD_STATUS_AUTO_DISABLE'),
            ];
        }

        return $items;
    }

    public static function getPacketList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::PACKET_VIP_PLUS=>Yii::t('app','PremiumPlus'),
                static::PACKET_VIP=>Yii::t('app','Premium'),
                static::PACKET_STANDART=>Yii::t('app','Standard'),
            ];
        }

        return $items;
    }

    public static function getVisibilityList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::VISIBILITY_NONE=>Yii::t('app','USER_VISIBILITY_NONE'),
                static::VISIBILITY_FRIENDS=>Yii::t('app','USER_VISIBILITY_FRIENDS'),
                static::VISIBILITY_ALL=>Yii::t('app','USER_VISIBILITY_ALL')
            ];
        }

        return $items;
    }

    public function getStatusLabel() {
        return static::getStatusList()[$this->status];
    }
	
	public function getAdStatusAutoLabel(){
		 return static::getAdStatusAutoList()[$this->ad_status_auto];
	}

    public function getPacketLabel() {
        return static::getPacketList()[$this->packet];
    }

    public static function getVideoIdentificationStatusList() {
        static $items;

        if (!isset($items)) {
            $items=[
                static::VIDEO_IDENTIFICATION_STATUS_AWAITING=>Yii::t('app','In Erwartung'),
                static::VIDEO_IDENTIFICATION_STATUS_ACCEPTED_AUTO=>Yii::t('app','Freigegeben (automatisch)'),
                static::VIDEO_IDENTIFICATION_STATUS_ACCEPTED_MANUAL=>Yii::t('app','Freigegeben (manuell)'),
                static::VIDEO_IDENTIFICATION_STATUS_REJECTED=>Yii::t('app','Abgelehnt'),
            ];
        }

        return $items;
    }

    public function getVideoIdentificationStatusLabel() {
        return static::getVideoIdentificationStatusList()[$this->video_identification_status];
    }


    public function getNewPassword() {
        return $this->_newPassword;
    }

    public function setNewPassword($value) {
        $this->_newPassword=$value;
    }

    public function getNewPasswordRepeat() {
        return $this->_newPasswordRepeat;
    }

    public function setNewPasswordRepeat($value) {
        $this->_newPasswordRepeat=$value;
    }

    public function recalcHierarchyNetworkStats() {
        $this->recalcNetworkStats();

        if ($this->parent) {
            $this->parent->recalcNetworkStats();
        }
    }

    public function recalcNetworkStats() {
        Yii::$app->db->createCommand("
            update user u
            left outer join (
                select parent_id as n_user_id,sum(network_size-IF(status='ACTIVE',0,1)) as n_size,max(network_levels) as n_levels,SUM(IF(status in ('ACTIVE','BLOCKED','DELETED'),1,0)) as referrals
                from user
                where parent_id=:parent_id
                group by parent_id
            ) as t
            on (u.id=t.n_user_id)
            set network_size=coalesce(n_size,0)+1,network_levels=coalesce(n_levels,0)+1,invitations=coalesce(referrals,0)
            where u.id=:parent_id
        ",[
            ':parent_id'=>$this->id
        ])->execute();

        Yii::$app->db->createCommand("delete from user_referral where user_id=:user_id",[':user_id'=>$this->id])->execute();
        Yii::$app->db->createCommand("insert into user_referral (user_id,referral_user_id,level) select parent_id,id,1 from `user` where parent_id=:user_id",[':user_id'=>$this->id])->execute();
        Yii::$app->db->createCommand("
            insert into user_referral (user_id,referral_user_id,level)
            select ur2.user_id,ur.referral_user_id,ur.level+1
            from user_referral ur
            join user_referral ur2 on (ur2.referral_user_id=ur.user_id and ur2.user_id=:user_id)
            on duplicate key update level=values(level)
        ",[
            ':user_id'=>$this->id
        ])->execute();


    }

    public function deleteContactChatHistory($contactId) {
        $trx=Yii::$app->db->beginTransaction();

        $chatUser=\app\models\ChatUser::findOne($contactId);

        if ($chatUser && $chatUser->is_group_chat) {
            $trollboxMessage=\app\models\TrollboxMessage::findOne(['group_chat_user_id'=>$chatUser->user_id]);
            if ($trollboxMessage && $trollboxMessage->user_id==Yii::$app->user->id) {
                $params=[
                    ':chat_user_id'=>$contactId
                ];

                Yii::$app->db->createCommand('delete from chat_conversation where (user_id!=:chat_user_id and second_user_id=:chat_user_id)',$params)->execute();
                Yii::$app->db->createCommand('update chat_message set outgoing_chat_message_id=null where (second_user_id=:chat_user_id)',$params)->execute();
                Yii::$app->db->createCommand('update chat_message,chat_file set chat_message_id=null where (chat_message_id=chat_message.id) and (chat_message.user_id!=:chat_user_id and second_user_id=:chat_user_id)',$params)->execute();
                Yii::$app->db->createCommand('delete from chat_message where (user_id!=:chat_user_id and second_user_id=:chat_user_id)',$params)->execute();

                $userIds=Yii::$app->db->createCommand("select second_user_id from chat_user_contact where user_id=:user_id",[
                    ':user_id'=>$contactId
                ])->queryColumn();

                Yii::$app->db->createCommand('delete from chat_user_contact where (user_id=:chat_user_id or second_user_id=:chat_user_id)',$params)->execute();

                $trollboxMessage->status=\app\models\TrollboxMessage::STATUS_REJECTED;
                $trollboxMessage->status_changed_dt=(new \app\components\EDateTime())->sql();
                $trollboxMessage->status_changed_user_id=Yii::$app->user->id;
                $trollboxMessage->save();

                $tmsh=new \app\models\TrollboxMessageStatusHistory();
                $tmsh->trollbox_message_id=$trollboxMessage->id;
                $tmsh->status=$trollboxMessage->status;
                $tmsh->dt=$trollboxMessage->status_changed_dt;
                $tmsh->user_id=$trollboxMessage->status_changed_user_id;
                $tmsh->save();

                $trx->commit();

                \app\components\ChatServer::updateInitInfo($userIds);

                foreach($userIds as $userId) {
                    if ($userId!=Yii::$app->user->id) {
                        UserEvent::addSystemMessage($userId,Yii::t('app','{user} hat den Chat "{title}" gelöscht.',[
                            'user'=>Yii::$app->user->identity->name,
                            'title'=>$chatUser->group_chat_title
                        ]));
                    }
                }
            } else {
                $params=[
                    ':user_id'=>$this->id,
                    ':chat_user_id'=>$contactId
                ];

                Yii::$app->db->createCommand('delete from chat_conversation where (user_id=:user_id and second_user_id=:chat_user_id)',$params)->execute();
                Yii::$app->db->createCommand('update chat_message set outgoing_chat_message_id=null where (sender_user_id=:user_id and second_user_id=:chat_user_id)',$params)->execute();
                Yii::$app->db->createCommand('update chat_message,chat_file set chat_message_id=null where (chat_message_id=chat_message.id) and (chat_message.user_id=:user_id and second_user_id=:chat_user_id)',$params)->execute();
                Yii::$app->db->createCommand('delete from chat_message where (user_id=:user_id and second_user_id=:chat_user_id)',$params)->execute();
                Yii::$app->db->createCommand('delete from chat_user_contact where (user_id=:user_id and second_user_id=:chat_user_id)',$params)->execute();

                $trx->commit();
            }

        } else {
            $params=[
                ':user_id'=>$this->id,
                ':contact_id'=>$contactId
            ];

            Yii::$app->db->createCommand('delete from chat_conversation where (user_id=:user_id and second_user_id=:contact_id)',$params)->execute();
            Yii::$app->db->createCommand('update chat_message set outgoing_chat_message_id=null where (user_id=:user_id and second_user_id=:contact_id) or (user_id=:contact_id and second_user_id=:user_id)',$params)->execute();
            Yii::$app->db->createCommand('update chat_message,chat_file set chat_message_id=null where (chat_message_id=chat_message.id) and (chat_message.user_id=:user_id and second_user_id=:contact_id)',$params)->execute();
            Yii::$app->db->createCommand('delete from chat_message where (user_id=:user_id and second_user_id=:contact_id)',$params)->execute();

            $trx->commit();
        }

    }
    
    public function deleteFriend($friendId,$sendNotification=true) {
        $trx=Yii::$app->db->beginTransaction();

        $params=[
            ':user_id'=>$this->id,
            ':friend_user_id'=>$friendId
        ];

        UserFriend::deleteAll('(user_id=:user_id and friend_user_id=:friend_user_id) or (user_id=:friend_user_id and friend_user_id=:user_id)',$params);
        ChatUserContact::deleteAll('(user_id=:user_id and second_user_id=:friend_user_id) or (user_id=:friend_user_id and second_user_id=:user_id)',$params);
        UserFriendRequest::deleteAll('(user_id=:user_id and friend_user_id=:friend_user_id) or (user_id=:friend_user_id and friend_user_id=:user_id)',$params);

        // cleanup chat messages
        Yii::$app->db->createCommand('delete from chat_conversation where (user_id=:user_id and second_user_id=:friend_user_id) or (user_id=:friend_user_id and second_user_id=:user_id)',$params)->execute();
        Yii::$app->db->createCommand('update chat_message set outgoing_chat_message_id=null where (user_id=:user_id and second_user_id=:friend_user_id) or (user_id=:friend_user_id and second_user_id=:user_id)',$params)->execute();
        Yii::$app->db->createCommand('update chat_message,chat_file set chat_message_id=null where (chat_message_id=chat_message.id) and ((chat_message.user_id=:user_id and second_user_id=:friend_user_id) or (chat_message.user_id=:friend_user_id and second_user_id=:user_id))',$params)->execute();
        Yii::$app->db->createCommand('delete from chat_message where (user_id=:user_id and second_user_id=:friend_user_id) or (user_id=:friend_user_id and second_user_id=:user_id)',$params)->execute();

        $trx->commit();

        if ($sendNotification) {
            \app\components\ChatServer::updateInitInfo([$this->id, $friendId]);
        }
    }

    public function updateInitInfo() {
        \app\components\ChatServer::updateInitInfo([$this->id]);
    }

    public function addFriend($friendId,$addChatUserContactAndSendNotification=true) {
        $trx=Yii::$app->db->beginTransaction();

        $userFriend1=new UserFriend();
        $userFriend1->user_id=$this->id;
        $userFriend1->friend_user_id=$friendId;
        $userFriend1->save();

        $userFriend2=new UserFriend();
        $userFriend2->user_id=$friendId;
        $userFriend2->friend_user_id=$this->id;
        $userFriend2->save();

        if ($addChatUserContactAndSendNotification) {
            \app\models\ChatUserContact::add($this->id, $friendId);
            \app\models\ChatUserContact::add($friendId,$this->id);
        }

        $trx->commit();

        if ($addChatUserContactAndSendNotification) {
            \app\components\ChatServer::updateInitInfo([$friendId,$this->id]);
        }
    }

    private function getBirthParts() {
       if ($this->birthday!='') {
           if($this->birthday!='0000-00-00'){
			   $d = new EDateTime($this->birthday);
			   $this->_birthDay = intval($d->format('d'));
			   $this->_birthMonth = intval($d->format('m'));
			   $this->_birthYear = intval($d->format('Y'));  
		   }
		   else{
			  $this->_birthDay = 0;
			  $this->_birthMonth = 0;
			  $this->_birthYear = 0;   
		   }
		   
       }
    }
     

    public function getBirthDay() {
        if ($this->_birthDay===null) {
            $this->getBirthParts();
        }
        return $this->_birthDay;
    }

    public function setBirthDay($value) {
        $this->_birthDay=$value;
    }

    public function getBirthMonth() {
        return $this->_birthMonth;
    }

    public function setBirthMonth($value) {
        $this->_birthMonth=$value;
    }

    public function getBirthYear() {
        return $this->_birthYear;
    }

    public function setBirthYear($value) {
        $this->_birthYear=$value;
    }

    public static function getMaritalStatusList() {
        return [
            static::MARTIAL_STATUS_MARRIED=>Yii::t('app','Married'),
            static::MARTIAL_STATUS_VERGEBEN=>Yii::t('app','Vergeben'),
            static::MARTIAL_STATUS_SINGLE=>Yii::t('app','Single')
        ];
    }

    public function getArrMaritalStatusList() {
        return [
            ['value'=>static::MARTIAL_STATUS_MARRIED, 'name'=>Yii::t('app','Married')],
            ['value'=>static::MARTIAL_STATUS_VERGEBEN, 'name'=>Yii::t('app','Vergeben')],
            ['value'=>static::MARTIAL_STATUS_SINGLE, 'name'=>Yii::t('app','Single')],
        ];
    }

    public function getMaritalStatusLabel() {
        return $this->getMaritalStatusList()[$this->marital_status];
    }

    public static function getSexList()
    {
        return [
            static::SEX_M => Yii::t('app', 'Man'),
            static::SEX_F => Yii::t('app', 'Woman'),
        ];
    }

    public function getSexLabel() {
        return $this->getSexList()[$this->sex];
    }

    public function getChatAuthorizationKey() {
        return $this->id.hash('sha256',$this->id.Yii::$app->params['chat']['authorizationSecret']);
    }


    public static function userProfileDeleteList() {
        return [
            0 => Yii::t('app', 'Admin'),
            1 => Yii::t('app', 'User'),
        ];
    }

    public function userProfileDeleteLabel() {
        return $this->userProfileDeleteList()[$this->is_user_profile_delete];
    }

    public function getCountryShortName() {
        return $this->country_id ? Country::getListShort($this->country_id) : 'de';
    }

    public function rules() {
        $rules=parent::rules();

        $this->deleteRules($rules,[
            [['nick_name'],'unique'],
            [['email'],'unique']
        ]);

        $this->addRules($rules,[
            [['nick_name'],'default','value'=>null],
            [['sex','first_name','last_name'],'required','on'=>'profileFillup'],
            [['sex','first_name','last_name','city'],'required','on'=>'profileFillup2'],
            [['email'],'required','on'=>['update','profile'],'skipOnEmpty'=>false],
            [['email','paypal_email'],'email'],
            ['nick_name','match','pattern'=>'%@%','not'=>true,'message'=>Yii::t('app','Nickname can\'t contain symbol @')],
            [['nick_name'], 'unique','message'=>Yii::t('app','User with this Nickname already exists')],
            [['email'], 'unique', 'message'=>Yii::t('app','User with this E-mail already exists')],
            [['newPassword'],'safe','on'=>'profile'],
            [['newPasswordRepeat'],'compare','compareAttribute'=>'newPassword','on'=>'profile'],
            [['birthDay'],'required','on'=>'profile','message'=> Yii::t('app','Bitte gib Dein Gebutsdatum an')],
			[['country_id'],'required', 'on' => 'profile','message'=> Yii::t('app','Bitte gib Dein Land an')],
            [['birthMonth','birthYear'],'safe','on'=>'profile'],
            [['facebook_id'],'safe','on'=>'profile'],
            ['birthDay','validateBirthday','on'=>'profile'],
            [['oldPassword'],'validateOldPassword','on'=>'profile'],
            [['newPassword'],'isPassword'],
            [['impressum'],'string', 'max' => 2000],
            [['agb'],'string', 'max' => 100000],
            [['company_name'], 'required', 'on'=>['profile','profileFillup2'], 'when' => function ($model) {return $model->is_company_name;}],
            [['company_manager','impressum','agb'], 'required', 'on'=>['profile','profileFillup2'], 'when' => function ($model) {return $model->is_company_name;}],
            ['birthDay','validateBirthday','on'=>'profileFillup2', 'skipOnEmpty'=>false],
            [['validation_phone'], 'trim'],
            [['validation_phone'], 'required', 'on'=>'validationPhone'],
            ['validation_phone','match','pattern'=>'%^\+?\d{2,4} ?\d{7,11}$%','message'=>Yii::t('app','Gib Deine Handynummer wie folgt ein: +4917612345678')],
            ['validation_phone', 'unique', 'message'=>Yii::t('app','Die eingetragene Telefonnummer wird bereits von einem anderen User verwendet.')],
            [['validation_code_form'], 'required', 'on'=>'validationCode'],
            [['first_name','last_name','city'],
                'match','pattern'=>'/[äöüÄÖÜßa-zA-Z]+$/s',
                'message'=>Yii::t('app','Diese Felder dürfen nur Buchstaben beinhalten.'),
                'on'=>['profile', 'profileFillup', 'profileFillup2']],
            [['company_name','company_manager','impressum','agb'], 'required', 'on'=>['update'],
                'when' => function ($model) {return $model->is_company_name;},
                'whenClient' => "function (attribute, value) {
                    return $('#user-is_company_name').is(':checked');
                }"
            ],
            ['trollbox_messages_limit_per_day','number', 'min'=>1, 'on'=>'update'],
            ['validation_phone','validateSmsCountValidator','on'=>'validationPhone']
        ]);

        return $rules;
    }

    public function validateSmsCountValidator($attribute,$params) {
        $count=Yii::$app->db->createCommand("select `count` from phone_sms_count where phone=:phone",[
            ':phone'=>Yii::$app->sms->normalizePhone($this->validation_phone)
        ])->queryScalar();

        if ($count>=2) {
            $this->addError('validation_phone',Yii::t('app','Du hast die zulässige SMS-Anzahl überschritten.'));
        }
    }

    public function validateBirthday($attribute,$params) {
        if (!checkdate($this->birthMonth,$this->birthDay,$this->birthYear)) {
            $this->addError('birthDay', Yii::t('app','Bitte gib Dein Geburtsdatum an'));
            $this->addError('birthMonth', Yii::t('app','Bitte gib Dein Geburtsdatum an'));
            $this->addError('birthYear', Yii::t('app','Bitte gib Dein Geburtsdatum an'));
        }
    }

    public function validateOldPassword() {
        if(!empty($this->oldPassword)) {
            if(!Yii::$app->security->validatePassword($this->oldPassword, $this->password)) {
                $this->addError('oldPassword', Yii::t('app','Falsche Altes Passwort.'));
            }
        }
    }

    public function isPassword() {
        if(!empty($this->oldPassword)) {
            if(strlen($this->newPassword) < 6) {
                $this->addError('newPassword', Yii::t('app', 'Das eingegebene Passwort muss mindestens 6 Zeichen haben'));
                return;
            }
            if(!preg_match('/(?=.*\d)(?=.*[a-zA-Z]).*$/', $this->newPassword)) {
                $this->addError('newPassword', Yii::t('app', 'Das eingegebene Passwort muss mindestens eine Buchstabe und eine Ziffer beinhalten'));
                return;
            }
        } else {
            $this->addError('newPassword', Yii::t('app', 'Alt passwort darf nicht leer sein.'));
        }
    }




    public function attributeLabels()
    {
        return [
            'sex' => Yii::t('app','Sex'),
            'registration_ip' => Yii::t('app','Registration IP'),
            'payment_complaints' => Yii::t('app','Mahnungen'),
            'email' => Yii::t('app','Email'),
            'deleted_email' => Yii::t('app','Email'),
            'password' => Yii::t('app','Password'),
            'first_name' => Yii::t('app','First Name'),
            'deleted_first_name' => Yii::t('app','First Name'),
            'last_name' => Yii::t('app','Last Name'),
            'deleted_last_name' => Yii::t('app','Last Name'),
            'nick_name' => Yii::t('app','Nick Name'),
            'status' => Yii::t('app','Status'),
            'birthday' => Yii::t('app','Geburtstag'),
            'phone' => Yii::t('app','Phone'),
            'street' => Yii::t('app','Street'),
            'house_number' => Yii::t('app','House Number'),
            'visibility_address1' => Yii::t('app','Visibility Address1'),
            'zip' => Yii::t('app','Zip'),
            'city' => Yii::t('app','City'),
            'visibility_address2' => Yii::t('app','Visibility Address2'),
            'profession' => Yii::t('app','Profession'),
            'visibility_profession' => Yii::t('app', 'Visibility Profession'),
            'marital_status' => Yii::t('app','Marital Status'),
            'visibility_marital_status' => Yii::t('app', 'Visibility Marital Status'),
            'about' => Yii::t('app','About'),
            'visibility_about' => Yii::t('app', 'Visibility About'),
            'validation_type' => Yii::t('app', 'Validation Type'),
            'validation_status' => Yii::t('app', 'Validation Status'),
            'validation_details' => Yii::t('app', 'Validation Details'),
            'oldPassword' => Yii::t('app','Altes Passwort'),
            'newPassword' => Yii::t('app','Neues Passwort'),
            'newPasswordRepeat' => Yii::t('app','Neues Passwort wiederholen'),
            'plainPassword'=>Yii::t('app','New Password'),
            'balance' => Yii::t('app', 'Balance'),
            'balance_buyed' => Yii::t('app', 'Kontostand nicht auszahlbar'),
            'balance_earned' => Yii::t('app', 'Kontostand auszahlbar ohne Zinsen'),
            'balance_token_deposit_percent' => Yii::t('app','Kontostand Zinsen'),
            'balance_token' => Yii::t('app', 'Token Balance'),
            'balance_token_buyed' => Yii::t('app', 'Tokenkontostand nicht auszahlbar'),
            'balance_token_earned' => Yii::t('app', 'Tokenkontostand auszahlbar'),
            'registration_dt' => Yii::t('app', 'Registration Dt'),
            'avatar_file_id' => Yii::t('app', 'Avatar'),
            'company_name' => Yii::t('app', 'Company Name'),
            'spam_reports' => Yii::t('app','Anzahl der Spam-Meldungen'),
            'stat_offer_year_turnover'=>Yii::t('app','Umsatz in Jugls'),
            'stat_messages_per_day'=>Yii::t('app','Durchschnittswert Nachrichten User pro 24Std.'),
            'stat_active_search_requests'=>Yii::t('app','Anzahl Suchanzeigen Online'),
            'stat_offers_view_buy_ratio'=>Yii::t('app','Verhältnis gekaufer Artikel zu gelesener Werbung (Kaufbonus erhalten) 1:'),
            'paypal'=>Yii::t('app','Paypal'),
            'country_id'=>Yii::t('app','Land'),
            'deleted_dt'=>Yii::t('app','Deleted'),
            'network_size'=>Yii::t('app','Mitglieder im Netzwerk'),
            'invitations'=>Yii::t('app','Anzahl Einladungen'),
            'free_registrations_limit'=>Yii::t('app','Einladungskontingent'),
            'stat_buyed_jugl'=>Yii::t('app','Gekaufte Punkte'),
            'is_user_profile_delete'=>Yii::t('app','Wer hat gelöscht'),
            'validation_changelog'=>Yii::t('app','Änderungsprotokoll'),
            'dt_status_change'=>Yii::t('app','Status'),
            'is_moderator'=>Yii::t('app','Moderator'),
            'is_company_name'=>Yii::t('app','Gewerblich'),
            'last_spam_report_dt'=>Yii::t('app','Letzter Spam'),
            'company_manager'=>Yii::t('app','Geschäftsführer'),
            'impressum'=>Yii::t('app','Impressum'),
            'agb'=>Yii::t('app','AGBs'),
            'validation_phone'=>Yii::t('app', 'Handynummer'),
            'validation_code_form'=>Yii::t('app', 'Verifikation code'),
            'publish_offer_wo_validation'=>Yii::t('app','Upload Werbungen ohne Kontroll'),
            'vip_active_till'=>Yii::t('app','Premium Midgliedschaft bis'),
            'vip_lifetime'=>Yii::t('app','Lebenslang Premium Midgliedshaft'),
            'packet'=>Yii::t('app','Midgliedschaft'),
            'publish_search_request_wo_validation'=>Yii::t('app','Upload Suchaufträge ohne Kontroll'),
			'ad_status_auto'=>Yii::t('app','Such- und Werbeaufträge werden automatisch aktiviert'),
            'is_blocked_in_trollbox'=>Yii::t('app','Für alle Foren sperren'),
            'trollbox_messages_limit_per_day'=>Yii::t('app','Anzahl der Beiträge pro Tag'),
            'allow_moderator_country_change'=>Yii::t('app','Berechtigung zur Landänderung'),
            'allow_country_change'=>Yii::t('app','Landänderung erlaubt'),
	        'access_translator'=>Yii::t('app','Übersetzer')
        ];
   }


    public function generateCode() {
        $symbols='23456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $code='';
        for ($i=0;$i<8;$i++) {
            $code.=$symbols[rand(0,strlen($symbols)-1)];
        }
        $this->validation_code=$code;
    }

    public function getCanSendInvitationSMS() {
        $this->refresh();
        return $this->sms_limit>$this->sms_sent ? true:Yii::t('app','Du hast dein SMS-Einladungskontingent überschritten. Bitte wende dich an Administration des Portals.');
    }

    public function invitationSmsSent() {
        $this->updateCounters(['sms_sent'=>1]);
    }

    public function getRegistrationsCount() {
        $registrations=static::find()->where(['parent_id'=>$this->id])->count();

        return $registrations;
    }

    public function getRegistrationsLimit() {
        if ($this->free_registrations_limit!==null) return $this->free_registrations_limit;

        $packet=$this->packet!='' ? $this->packet:static::PACKET_STANDART;

        return \app\models\Setting::get($packet.'_FREE_REGISTRATIONS_LIMIT');
    }

    public function getName() {
        if($this->is_company_name) {
            return $this->company_name;
        }
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getFlag() {
        $flag = Country::getListShort()[$this->country_id];
        return $flag;
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
        return static::find()->where('email=:username or nick_name=:username',[':username'=>$username])->one();
    }
	
	public static function findByFacebookId($facebook_id) {
        return static::find()->where('facebook_id=:facebookid',[':facebookid'=>$facebook_id])->one();
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

    public function updateChatContactsAfterRegistration() {
        $userIds=[$this->id];
        if ($this->parent) {
            $userIds[]=$this->parent->id;
        }

        \app\components\ChatServer::updateInitInfo($userIds);
    }
/*
    public function registrationPaymentProcessed() {
        $trx=Yii::$app->db->beginTransaction();
        $this->status=static::STATUS_ACTIVE;
        $this->save();
        $this->addReferralToParent();
        $sum=Setting::get('REGISTRATION_COST_JUGL');
        if ($this->parent) {
            $this->parent->distributeReferralPayment($sum, $this, BalanceLog::TYPE_IN_REG_REF, BalanceLog::TYPE_IN_REG_REF, BalanceLog::TYPE_IN_REG_REF_REF,Yii::t('app','Registrierung'));
        }
        $trx->commit();

        $this->updateChatContactsAfterRegistration();
    }
*/
    public function removeReferralFromParent()
    {
        $referralIds=Yii::$app->db->createCommand("select referral_user_id from user_referral where user_id=:user_id",[':user_id'=>$this->id])->queryColumn();

        $parent=$this->parent;
        do {
//            echo -$this->network_size-1;echo " ";echo $parent->id." ####";
            static::updateAllCounters(['network_size'=>-$this->network_size-1],['id'=>$parent->id]);
            \app\models\UserReferral::deleteAll(['user_id'=>$parent->id,'referral_user_id'=>$referralIds]);
            $parent=$parent->parent;
        } while ($parent);

        $this->parent_id=null;
        $this->save();
//        die('ok!');

    }

    public function addReferralToParent($level=2,$newUser=null) {
        if (!$newUser) {
            $newUser=$this;
        }

        $user=$this->parent;

        if ($level==2 && $user) {
            $user->updateCounters(['invitations'=>1]);
        }

        if ($user) {
            if ($user->id==$newUser->parent_id) {
                // only direct referral is friend

                $trx2=Yii::$app->db->beginTransaction();
                try {
                    $userFriend1 = new UserFriend;
                    $userFriend1->user_id = $user->id;
                    $userFriend1->friend_user_id = $newUser->id;
                    $userFriend1->dt = new Expression('NOW()');
                    $userFriend1->save();

                    $userFriend2 = new UserFriend;
                    $userFriend2->user_id = $newUser->id;
                    $userFriend2->friend_user_id = $user->id;
                    $userFriend2->dt = new Expression('NOW()');
                    $userFriend2->save();
                    $trx2->commit();
                } catch (\yii\base\Exception $e) {
                    $trx2->rollBack();
                }

                $trx=Yii::$app->db->beginTransaction();
                try {
                    $chatUserContact1 = new \app\models\ChatUserContact();
                    $chatUserContact1->user_id = $user->id;
                    $chatUserContact1->second_user_id = $newUser->id;
                    $chatUserContact1->decision_needed = 0;
                    $chatUserContact1->save();

                    $chatUserContact2 = new \app\models\ChatUserContact();
                    $chatUserContact2->user_id = $newUser->id;
                    $chatUserContact2->second_user_id = $user->id;
                    $chatUserContact2->decision_needed = 0;
                    $chatUserContact2->save();
                    $trx->commit();
                } catch (\yii\base\Exception $e) {
                    $trx->rollBack();
                }

                Yii::$app->on(\yii\web\Application::EVENT_AFTER_ACTION, [$newUser,'sendWelcomeMessageFromParentUser']);

                // send notification only for parent
                \app\models\UserEvent::addNewNetworkMember($user,$newUser);
                \app\models\UserFollowerEvent::addNewNetworkMember($user,$newUser);
            }

            $userReferral=UserReferral::findOne(['user_id'=>$user->id,'referral_user_id'=>$newUser->id]);
            if (!$userReferral) {
                $userReferral=new UserReferral();
                $userReferral->user_id=$user->id;
                $userReferral->referral_user_id=$newUser->id;
            }
            $userReferral->level=$level;
            $userReferral->save();

            User::updateAllCounters(['network_size' => 1, 'new_network_members' => 1], ['id' => $user->id]);

            Yii::$app->db->createCommand('update user set network_levels=:level where id=:id and network_levels<:level',[
                ':id'=>$user->id,
                ':level'=>$level-1
            ])->query();

            $user->addReferralToParent($level+1,$newUser);
        }
    }

    public function sendWelcomeMessageFromParentUser() {
        if ($this->parent_id) {
            \app\components\ChatServer::sendTextMessage($this->parent_id, $this->id,
               // Yii::t('app',"Herzlich Willkommen in meinem Netzwerk! Bei allen Fragen stehe ich dir zukünftig zur Verfügung.\nDu kannst auch links oben auf das Navigationsfeld klicken und dann auf \"Startseite\". Auf der Startseite findest du das Jugl-Forum. Bitte beachte auch das \"i\" für Information (meist rechts am Rand), einfach draufklicken, dort findest du zur jeweiligen Funktion die entsprechende Beschreibung. Zugegeben, für viele ist Jugl.net nicht leicht zu verstehen, aber du solltest dir etwas Zeit nehmen, denn es rentiert sich und hat echt viel Potential.\nHast du vielleicht schon eine bestimmte Frage? Dann schreib mir einfach. Lieben Gruß"));
				Yii::t('app',"Herzlich Willkommen in meinem Netzwerk! Bei allen Fragen stehe ich Dir zukünftig zur Verfügung. Du kannst auch links oben auf das Navigationsfeld klicken und dann auf \"Startseite\". Auf der Startseite findest du das Jugl-Forum. Bitte beachte auch das \"i\" für Information (meist rechts am Rand), einfach draufklicken, dort findest du zur jeweiligen Funktion die entsprechende Beschreibung. Zugegeben, für viele ist Jugl.net nicht leicht zu verstehen, aber Du solltest Dir etwas Zeit nehmen, denn es rentiert sich und hat echt viel Potential.\nUnter folgendem Link findest Du das aktuelle Präsentationsvideo zu Jugl.net: https://youtu.be/d987MsWRcXA\nHast du vielleicht schon eine bestimmte Frage? Dann schreib mir einfach. Lieben Gruß"));
		
		}
    }


    private function distributionSum($sum,$percent,$small=false) {
        if($small){
			$sum =($sum*$percent)/100;
			$sum = number_format($sum,5);
		}
		else{
			$sum=floor($sum*$percent*1000)/100000;
		}
        return $sum;
    }

    public function resetNewEventsCount() {
        if ($this->new_events>0) {
            $this->new_events = 0;
            $this->save();
            \app\components\ChatServer::statusUpdate($this->id);
        }
    }

    public function resetNewFollowerEventsCount() {
        if ($this->new_follower_events>0) {
            $this->new_follower_events=0;
            $this->save();
            \app\components\ChatServer::statusUpdate($this->id);
        }
    }

    /*
    // used for distributing bonuses for incoming payments
    public function distributeReferralPayment($sum,$initiatorUser,$typeMe,$typeRef,$typeRefRef,$comment='') {
        $parentsPercent=Setting::get('PROFIT_DISTRIBUTION_PARENTS_PERCENT');
        $juglPercent=Setting::get('PROFIT_DISTRIBUTION_JUGL_PERCENT');
        $mePercent=100-$parentsPercent-$juglPercent;

        $type=$typeRefRef;
        if ($initiatorUser->id==$this->id) {
            $type==$typeMe;
        }
        if ($initiatorUser->parent && $initiatorUser->parent->id==$this->id) {
            $type=$typeRef;
        }

        $this->addBalanceLogItem($type,$this->distributionSum($sum,$mePercent),$initiatorUser,$comment);

        if ($this->parent) {
            $this->parent->distributeReferralPayment($this->distributionSum($sum,$parentsPercent),$initiatorUser,$typeMe,$typeRef,$typeRefRef,$comment);
        }
    }
    */


    private function processPaymentText($text,$sum,$user) {
        $text=str_replace('[sum][/sum]','[nobrStart][/nobrStart]'. abs($sum)>=0.01 ? \app\components\Helper::formatPrice(abs($sum)) : abs(number_format($sum,5)).' [jugl][/jugl][nobrEnd][/nobrEnd]',$text);
        $text=str_replace('[user][/user]',$user->name,$text);

        return $text;
    }

    private function processPaymentTokenText($text,$sum,$user) {
        $text=str_replace('[sum][/sum]','[nobrStart][/nobrStart]'. abs($sum)>=0.01 ? \app\components\Helper::formatPrice(abs($sum)) : abs(number_format($sum,5)).' Tokens[nobrEnd][/nobrEnd]',$text);
        $text=str_replace('[user][/user]',$user->name,$text);

        return $text;
    }

    // used for distributins bonuses for in-system payments
    public function distributeReferralPayment($sum,$initiatorUser,$typeMe,$typeRef,$typeRefRef,$comment='',$level=0,$commentOut='',$commentInRef='',$commentOutRef='',$useAlwaysInitiateUser=true,$small=false,$sumIsTokenDepositPercent=false) {
        if (in_array($this->packet,[\app\models\User::PACKET_VIP,\app\models\User::PACKET_VIP_PLUS])) {
            $parentsPercent=Setting::get('VIP_PROFIT_DISTRIBUTION_PARENTS_PERCENT');
            $juglPercent=Setting::get('VIP_PROFIT_DISTRIBUTION_JUGL_PERCENT');
        } else {
            $parentsPercent=Setting::get('PROFIT_DISTRIBUTION_PARENTS_PERCENT');
            $juglPercent=Setting::get('PROFIT_DISTRIBUTION_JUGL_PERCENT');
        }
        $mePercent=100-$parentsPercent-$juglPercent;

        $type=$typeRefRef;
        if ($level==0) {
            $type=$typeMe;
        }
        if ($level==1) {
            $type=$typeRef;
        }

        $liSum=$this->distributionSum($sum,100,$small ? $small : false);
        $liUser=$initiatorUser;
        $logItem=$this->addBalanceLogItem($type,$liSum,$liUser,$this->processPaymentText($comment,$liSum,$liUser),false,false,$sumIsTokenDepositPercent && $level==0);

        if ($this->parent) {
            $outComment=$level==0 ? $commentOut:$commentOutRef;
            if ($outComment=='') {
                $outComment=Yii::t('app','Abgabe ins Netzwerk');
            }

            $liSum=-$this->distributionSum($sum,100-$mePercent,$small ? $small : false);
            $liUser=$this->parent;
            $this->addBalanceLogItem($type,$liSum,$liUser,$this->processPaymentText($outComment,$liSum,$liUser),false,false,false,$sumIsTokenDepositPercent && $level==0);

            $comment=$commentInRef;
            if ($comment=='') {
                $comment=Yii::t('app','Gewinn aus dem Netzwerk');
            }
            $commentOut=$commentOutRef;
            if (!$useAlwaysInitiateUser) {
                $initiatorUser=$this;
            }
            $this->parent->distributeReferralPayment($this->distributionSum($sum,$parentsPercent,$small ? $small : false),$initiatorUser,$typeMe,$typeRef,$typeRefRef,$comment,$level+1,$commentOut,$commentInRef,$commentOutRef,true);
        }

        return $logItem;
    }

    // used for distributins bonuses for in-system payments
    public function distributeTokenReferralPayment($sum,$initiatorUser,$typeMe,$typeRef,$typeRefRef,$comment='',$level=0,$commentOut='',$commentInRef='',$commentOutRef='',$useAlwaysInitiateUser=true,$small=false,$isBuyed=false) {
        $parentsTokenPercent=Setting::get('TOKEN_DISTRIBUTION_PARENTS_PERCENT_TOKEN');
        $parentsPercent=Setting::get('TOKEN_DISTRIBUTION_PARENTS_PERCENT_JUGL');
        $tokenToJuglExchangeRate=Setting::get('TOKEN_TO_JUGL_EXCHANGE_RATE');

        $mePercent=100-$parentsTokenPercent-$parentsPercent;

        $type=$typeRefRef;
        if ($level==0) {
            $type=$typeMe;
        }
        if ($level==1) {
            $type=$typeRef;
        }

        $liSum=$this->distributionSum($sum,100,$small ? $small : false);
        $liUser=$initiatorUser;
        $logItem=$this->addBalanceTokenLogItem($type,$liSum,$liUser,$this->processPaymentText($comment,$liSum,$liUser),$isBuyed);

        if ($this->parent) {
            $outComment=$level==0 ? $commentOut:$commentOutRef;
            if ($outComment=='') {
                $outComment=Yii::t('app','Abgabe ins Netzwerk');
            }

            // JUGL-252 (T02) do not decrease
            if ($level>0) {
                $liSum=-$this->distributionSum($sum,100-$mePercent,$small ? $small : false);
                $liUser=$this->parent;
                $this->addBalanceTokenLogItem($type,$liSum,$liUser,$this->processPaymentTokenText($outComment,$liSum,$liUser));
            }

            $comment=$commentInRef;
            if ($comment=='') {
                $comment=Yii::t('app','Gewinn aus dem Netzwerk');
            }
            $commentOut=$commentOutRef;
            if (!$useAlwaysInitiateUser) {
                $initiatorUser=$this;
            }
            $this->parent->distributeTokenReferralPayment($this->distributionSum($sum,$parentsTokenPercent,$small ? $small : false),$initiatorUser,$typeMe,$typeRef,$typeRefRef,$comment,$level+1,$commentOut,$commentInRef,$commentOutRef,true);
            $this->parent->distributeReferralPayment($this->distributionSum($sum,$parentsPercent,$small ? $small : false)*$tokenToJuglExchangeRate,$initiatorUser,$typeMe,$typeRef,$typeRefRef,$comment,$level+1,$commentOut,$commentInRef,$commentOutRef,true);
        }

        return $logItem;
    }

    public function addBalanceLogItem($type, $sum, $initiatorUser, $comment='',$sumIsBuyed=false,$sumIsPayout=false,$sumIsTokenDepositPercent=false,$sumIsTokenDepositPercentPayout=false) {
        if (abs($sum)<0.00001) {
            return null;
        }

        $balanceLog=new BalanceLog;
        $balanceLog->dt=new Expression('NOW()');
        $balanceLog->user_id=$this->id;
        $balanceLog->type=$type;
        $balanceLog->sum=$sum;

        $sumBuyed=0;
        $sumEarned=0;
        $sumTokenDepositPercent=0;

        $rUser=static::findOne($this->id);

        if ($sum>0) {
            if ($sumIsTokenDepositPercent) {
                $sumTokenDepositPercent=$sum;
            } else {
                if ($sumIsBuyed) {
                    $sumBuyed=$sum;
                } else {
                    $sumEarned=$sum;
                }
            }
        } else {
            if ($sumIsPayout) {
                $sumLeft=$sum;
                $sumEarned=-min(abs($sumLeft),$rUser->balance_earned);
                $sumLeft-=$sumEarned;
                $sumBuyed=-min(abs($sumLeft),$rUser->balance_buyed);
                $sumLeft-=$sumBuyed;
                $sumTokenDepositPercent=$sumLeft;
            } else {
                if ($sumIsTokenDepositPercentPayout) {
                    $sumLeft=$sum;
                    $sumTokenDepositPercent=-min(abs($sumLeft),$rUser->balance_token_deposit_percent);
                    $sumLeft-=$sumTokenDepositPercent;
                    $sumBuyed=-min(abs($sumLeft),$rUser->balance_buyed);
                    $sumLeft-=$sumBuyed;
                    $sumEarned=$sumLeft;
                } else {
                    $sumLeft=$sum;
                    $sumBuyed=-min(abs($sumLeft),$rUser->balance_buyed);
                    $sumLeft-=$sumBuyed;
                    $sumEarned=-min(abs($sumLeft),$rUser->balance_earned);
                    $sumLeft-=$sumEarned;
                    $sumTokenDepositPercent=$sumLeft;
                }
            }
        }

        $balanceLog->sum_buyed=$sumBuyed;
        $balanceLog->sum_earned=$sumEarned;
        $balanceLog->sum_token_deposit_percent=$sumTokenDepositPercent;

        $balanceLog->initiator_user_id=$initiatorUser->id;
        $balanceLog->comment=$this->processPaymentText($comment,$sum,$initiatorUser);
        $balanceLog->save();

        User::updateAllCounters([
            'balance' => $sum,
            'balance_buyed'=>$sumBuyed,
            'balance_token_deposit_percent'=>$sumTokenDepositPercent,
            'balance_earned'=>$sumEarned,
            'earned_total'=>($sumEarned>0 ? $sumEarned:0)+($sumTokenDepositPercent>0 ? $sumTokenDepositPercent:0)
        ], ['id' => $this->id]);

        if ($sumEarned+$sumTokenDepositPercent>0) {
            Yii::$app->db->createCommand("
                insert into user_earned_by_date(user_id,dt,sum) values (:user_id,:dt,:sum)
                on duplicate key update sum=sum+:sum
                ",[
                    ':user_id'=>$this->id,
                    ':dt'=>(new EDateTime())->sqlDate(),
                    ':sum'=>$sumEarned+$sumTokenDepositPercent
                ]
            )->execute();
        }

        if (abs($sum)>=0.01) {
            \app\components\ChatServer::newMoneyIncoming($balanceLog);
        }

        return $balanceLog;
    }

    public function addBalanceTokenLogItem($type, $sum, $initiatorUser, $comment='',$sumIsBuyed=false,$sumIsPayout=false) {
        if (abs($sum)<0.00001) {
            return null;
        }

        $balanceLog=new BalanceTokenLog;
        $balanceLog->dt=new Expression('NOW()');
        $balanceLog->user_id=$this->id;
        $balanceLog->type=$type;
        $balanceLog->sum=$sum;

        $sumBuyed=0;
        $sumEarned=0;
        
        $rUser=static::findOne($this->id);

        if ($sum>0) {
            if ($sumIsBuyed) {
                $sumBuyed=$sum;
            } else {
                $sumEarned=$sum;
            }
        } else {
            if ($sumIsPayout) {
                if ($rUser->balance_token_earned>=-$sum) {
                    $sumEarned=$sum;
                } else {
                    $sumEarned=-$rUser->balance_token_earned;
                    $sumBuyed=$sum+$rUser->balance_token_earned;
                }
            } else {
                if ($rUser->balance_token_buyed>=-$sum) {
                    $sumBuyed=$sum;
                } else {
                    $sumBuyed=-$rUser->balance_token_buyed;
                    $sumEarned=$sum+$rUser->balance_token_buyed;
                }
            }
        }

        $balanceLog->sum_buyed=$sumBuyed;
        $balanceLog->sum_earned=$sumEarned;
        $balanceLog->initiator_user_id=$initiatorUser->id;
        $balanceLog->comment=$this->processPaymentTokenText($comment,$sum,$initiatorUser);
        $balanceLog->save();

        User::updateAllCounters(['balance_token' => $sum,'balance_token_buyed'=>$sumBuyed,'balance_token_earned'=>$sumEarned,'earned_token_total'=>$sumEarned>0 ? $sumEarned:0], ['id' => $this->id]);

        if ($sumEarned>0) {
            Yii::$app->db->createCommand("
                insert into user_token_earned_by_date(user_id,dt,sum) values (:user_id,:dt,:sum)
                on duplicate key update sum=sum+:sum
                ",[
                    ':user_id'=>$this->id,
                    ':dt'=>(new EDateTime())->sqlDate(),
                    ':sum'=>$sumEarned
                ]
            )->execute();
        }

        if (abs($sum)>=0.01) {
            \app\components\ChatServer::newMoneyTokenIncoming($balanceLog);
        }

        return $balanceLog;
    }

    public function logActivity() {
        $today=new EDateTime();

        $ual=UserActivityLog::findOne(['user_id'=>Yii::$app->user->id,'dt'=>$today->sqlDate()]);
        if (!$ual || (new EDateTime($ual->dt_full))->modify('+15 minute')<=$today) {
            Yii::$app->db->createCommand("insert into user_activity_log(user_id,dt,dt_full) values(:user_id,:dt,:dt_full) on duplicate key update dt=:dt,dt_full=:dt_full", [
                ':user_id' => Yii::$app->user->id,
                ':dt' => $today->sqlDate(),
                ':dt_full' => $today->sqlDateTime(),
            ])->execute();
        }
    }

    public function logMobileActivity() {
        $today=new EDateTime();

        $ual=UserActivityLog::findOne(['user_id'=>Yii::$app->user->id,'dt'=>$today->sqlDate()]);
        if (!$ual || (new EDateTime($ual->dt_full))->modify('+15 minute')<=$today) {
            Yii::$app->db->createCommand("insert into user_activity_log(user_id,dt,dt_full) values(:user_id,:dt,:dt_full) on duplicate key update dt=:dt,dt_full=:dt_full", [
                ':user_id' => Yii::$app->user->id,
                ':dt' => $today->sqlDate(),
                ':dt_full' => $today->sqlDateTime(),
            ])->execute();
        }
    }

    public function addVipPacket($months) {
        if ($this->packet==static::PACKET_VIP) {
            $activeTill=(new EDateTime($this->vip_active_till));
            $activeTill->modify('+ '.intval($months).' months');
            $this->vip_active_till=$activeTill->sqlDateTime();
        } else {
            $activeTill=(new EDateTime());
            $activeTill->modify('+ '.intval($months).' months');
            $this->vip_active_till=$activeTill->sqlDateTime();
            $this->packet=static::PACKET_VIP;
        }

        // REVERT_PREMIUM_MONTH
        $this->vip_active_till='2035-01-01 00:00:00';
        $this->vip_lifetime=1;

        $this->next_vip_notification_at=null;
    }

    public function addVipPlusPacket($months) {
        if ($this->packet==static::PACKET_VIP_PLUS) {
            $activeTill=(new EDateTime($this->vip_active_till));
            $activeTill->modify('+ '.intval($months).' months');
            $this->vip_active_till=$activeTill->sqlDateTime();
        } else {
            $activeTill=(new EDateTime());
            $activeTill->modify('+ '.intval($months).' months');
            $this->vip_active_till=$activeTill->sqlDateTime();
            $this->packet=static::PACKET_VIP_PLUS;
        }

        // REVERT_PREMIUM_MONTH
        $this->vip_active_till='2035-01-01 00:00:00';
        $this->vip_lifetime=1;

        $this->next_vip_notification_at=null;
    }

    public function scenarios() {
        $scenarios=parent::scenarios();

        $scenarios['profile']=[
            'street','house_number','visibility_address1',
            'zip','city','visibility_address2',
            'profession','visibility_profession',
            'marital_status','visibility_marital_status',
            'about','visibility_about','nick_name','email','phone','sex','oldPassword','newPassword','newPasswordRepeat',
            'birthDay','birthMonth','birthYear','country_id','company_name','visibility_birthday','paypal_email',
            'is_company_name','company_manager','impressum','agb'
        ];
		$scenarios['link_facebook']=[
            'facebook_id'
        ];
        $scenarios['profileFillup']=[
            'sex','first_name','last_name',
        ];

        $scenarios['profileFillup2']=[
            'sex','first_name','last_name','birthDay','birthMonth','birthYear','city','is_company_name','company_name','company_manager','impressum','agb','street','house_number'
        ];

        $scenarios['update']=[
            'status','email','country_id','sex','first_name','last_name','nick_name','plainPassword','birthday',
            'phone','street','house_number','is_company_name','company_name','company_manager','impressum','agb',
            'zip','city','profession','marital_status','about','visibility_address1','visibility_address2','visibility_profession','visibility_marital_status','visibility_about',
            'validation_type','validation_status','validation_failure_reason','validation_details','visibility_birthday','free_registrations_limit','is_moderator',
            'publish_offer_wo_validation','publish_search_request_wo_validation','packet','vip_active_till','vip_lifetime','validation_phone','ad_status_auto','is_blocked_in_trollbox',
            'trollbox_messages_limit_per_day','allow_moderator_country_change','allow_country_change', 'access_translator'
        ];

        $scenarios['validation']=[
            /*
            'sex','first_name','last_name','nick_name','birthday',
            'phone','street','house_number',
            'zip','city',
            */
            'validation_type','validation_status','validation_failure_reason','validation_details'
        ];

        $scenarios['autoSave']=[
            'street','house_number','visibility_address1',
            'zip','visibility_address2',
            'profession','visibility_profession',
            'marital_status','visibility_marital_status',
            'about','visibility_about','nick_name','phone','sex',
            'birthDay','birthMonth','birthYear','visibility_birthday','paypal_email'
        ];

        $scenarios['photos']=['avatar_file_id'];
        $scenarios['validationPhone']=['validation_phone_status','validation_phone', 'validation_code'];
        $scenarios['validationCode']=['validation_phone_status','validation_code_form'];

        return $scenarios;
    }

    public function beforeSave($insert) {
        if (checkdate($this->_birthMonth,$this->_birthDay,$this->_birthYear)) {
            $date=new EDateTime();
            $date->setDate($this->_birthYear,$this->_birthMonth,$this->_birthDay);
            $this->birthday=$date->sqlDate();
        }

        if ($this->newPassword!='') {
            $this->password=$this->newPassword;
            $this->encryptPwd();
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert,$changedAttributes);

        if ($insert) {
            $chatUser=new \app\models\ChatUser;
            $chatUser->user_id=$this->id;
            $chatUser->save();
        }

    }

    public function getOnlineFriendsPageQuery($pageNum,$perPage)
    {
        return $this->hasMany('\app\models\UserFriend', ['user_id' => 'id'])
            ->innerJoin('chat_user','(chat_user.online=1 or chat_user.online_mobile=1) and user_friend.friend_user_id=chat_user.user_id')
            ->innerJoin('user','user_friend.friend_user_id=user.id')
            ->offset(($pageNum-1)*$perPage)
            ->limit($perPage)
            ->orderBy('user.nick_name, user.first_name, user.last_name');
    }

    public function getShortData($additionalFields=array()) {
        $data=$this->toArray(array_merge(['id','first_name','last_name','nick_name','is_company_name','company_name','country_id'],$additionalFields));
        if (in_array('online',$additionalFields)) {
            $data['online'] = $this->chatUser->online ? 2:($this->chatUser->online_mobile ? 1:0);
        }

        $data['avatar']=$this->avatarUrl;
        $data['avatarSmall']=$this->getAvatarThumbUrl('avatarSmall');
        $data['avatarMobile']=$this->getAvatarThumbUrl('avatarMobile');
        $data['flag']=$this->getFlag();

        $data['isFriend']=UserFriend::isLoggedUserFriend($this->id);
        $data['isFollow']=UserFollower::isUserFollow($this->id);

        if (Yii::$app->user->id==$this->id || $this->visibility_address2) {
            $data['address']=$this->city;
        }

        return $data;
    }

    public static function getAdministrationUser() {
        $model=new self;
        $model->last_name=Yii::t('app','Administration');
        $model->first_name='';
        $model->avatar_file_id=Yii::$app->params['SystemAvatarFileId'];
        return $model;
    }

    public function getTeamChangeFinishTime($asJs=false) {
        if (!$this->registered_by_become_member || $this->is_stick_to_parent) {
            return false;
        }

        $time=(new EDateTime($this->registration_dt))->modify("+".\app\models\Setting::get('TEAM_CHANGE_PERIOD_DAYS')." minute");

        if ($time>(new EDateTime())) {
            return $asJs ? $time->js():$time;
        } else {
            return false;
        }
    }

    public function getAvatarThumbUrl($thumb) {
        return $this->avatarFile ? $this->avatarFile->getThumbUrl($thumb):(Yii::$app->controller instanceof \app\components\ExtApiController ? Yii::$app->request->hostInfo:'').'/static/images/account/default_avatar.png';
    }

    public function getAvatarUrl() {
        return $this->avatarFile ? $this->avatarFile->getThumbUrl('avatar'):(Yii::$app->controller instanceof \app\components\ExtApiController ? Yii::$app->request->hostInfo:'').'/static/images/account/default_avatar.png';
    }

    public function getEarnedTotalOld() {
        $result=Yii::$app->db->createCommand("select sum(`sum`) from balance_log where user_id=:user_id and `sum`>0 and type!=:type",[
            ':user_id'=>$this->id,
            ':type'=>BalanceLog::TYPE_PAYIN,
        ])->queryScalar();

        return floatval($result);
    }

    public function getEarnedThisMonthOld() {
        $result=Yii::$app->db->createCommand("select sum(`sum`) from balance_log where user_id=:user_id and `sum`>0 and type!=:type and dt>=:dt",[
            ':user_id'=>$this->id,
            ':type'=>BalanceLog::TYPE_PAYIN,
            ':dt'=>date('Y-m-01 00:00:00')
        ])->queryScalar();

        return floatval($result);
    }

    public function getEarnedThisYearOld() {
        $result=Yii::$app->db->createCommand("select sum(`sum`) from balance_log where user_id=:user_id and `sum`>0 and type!=:type and dt>=:dt",[
            ':user_id'=>$this->id,
            ':type'=>BalanceLog::TYPE_PAYIN,
            ':dt'=>date('Y-01-01 00:00:00')
        ])->queryScalar();

        return floatval($result);
    }

    public function getEarnedTodayOld() {
        $result=Yii::$app->db->createCommand("select sum(`sum`) from balance_log where user_id=:user_id and `sum`>0 and type!=:type and dt>=:dt",[
            ':user_id'=>$this->id,
            ':type'=>BalanceLog::TYPE_PAYIN,
            ':dt'=>date('Y-m-d 00:00:00')
        ])->queryScalar();

        return floatval($result);
    }

    public function getEarnedYesterdayOld() {
        $result=Yii::$app->db->createCommand("select sum(`sum`) from balance_log where user_id=:user_id and `sum`>0 and type!=:type and dt>=:dt_from and dt<=:dt_to",[
            ':user_id'=>$this->id,
            ':type'=>BalanceLog::TYPE_PAYIN,
            ':dt_from'=>(new \app\components\EDateTime())->modify('-1 day')->setTime(0,0,0)->sqlDateTime(),
            ':dt_to'=>(new \app\components\EDateTime())->modify('-1 day')->setTime(23,59,59)->sqlDateTime()
        ])->queryScalar();

        return floatval($result);
    }

    public function getEarnedTotal() {
        return floatval($this->earned_total);
    }

    public function getEarnedThisMonth() {
        $result = Yii::$app->db->createCommand('SELECT sum(`sum`) FROM user_earned_by_date WHERE user_id=:user_id AND dt>=:dt', [
            ':user_id'=>$this->id,
            ':dt'=>date('Y-m-01')
        ])->queryScalar();
        return floatval($result);
    }

    public function getEarnedThisYear() {
        $result = Yii::$app->db->createCommand('SELECT sum(`sum`) FROM user_earned_by_date WHERE user_id=:user_id AND dt>=:dt', [
            ':user_id'=>$this->id,
            ':dt'=>date('Y-01-01')
        ])->queryScalar();
        return floatval($result);
    }

    public function getEarnedToday() {
        $result = Yii::$app->db->createCommand('SELECT sum(`sum`) FROM user_earned_by_date WHERE user_id=:user_id AND dt>=:dt', [
            ':user_id'=>$this->id,
            ':dt'=>date('Y-m-d')
        ])->queryScalar();
        return floatval($result);
    }

    public function getEarnedYesterday() {
        $result = Yii::$app->db->createCommand('SELECT sum(`sum`) FROM user_earned_by_date WHERE user_id=:user_id AND dt=:dt', [
            ':user_id'=>$this->id,
            ':dt'=>(new EDateTime())->modify('-1 day')->sqlDate()
        ])->queryScalar();
        return floatval($result);
    }

    public function setValidationDetailsData($data) {
        $this->validation_details=json_encode($data,JSON_UNESCAPED_UNICODE);
    }

    public function getValidationDetailsData() {
        $data=json_decode($this->validation_details,true);
        if ($data===null) {
            $data=[];
        }

        return $data;
    }

    public function getUsers()
    {
        return $this->hasMany('\app\models\User', ['parent_id' => 'id'])
            ->andWhere('user.status!=:user_status1 and user.status!=:user_status2 and user.status!=:user_status3',[
                ':user_status1'=>User::STATUS_REGISTERED,
                ':user_status2'=>User::STATUS_LOGINED,
                ':user_status3'=>User::STATUS_EMAIL_VALIDATION
            ])
            ->orderBy('id desc');
    }

    public function unblock() {
        if ($this->status==static::STATUS_BLOCKED) {
            $this->status=static::STATUS_ACTIVE;
            $this->spam_reports=0;
            $this->dt_status_change=null;
            $this->save();
        }
    }

    public function block($emailTemplate='spam-block') {
        if (in_array($this->status,[static::STATUS_BLOCKED,static::STATUS_DELETED])) {
            return;
        }

        $trx=$this->db->beginTransaction();

        $this->status=static::STATUS_BLOCKED;
        $this->auth_key=Yii::$app->security->generateRandomString(32);
        $this->access_token=Yii::$app->security->generateRandomString(32);
        $this->dt_status_change=(new EDateTime())->sqlDateTime();

        $this->save();

        foreach($this->userDevices as $device) {
            $device->key=null;
            $device->save();
        }

        Yii::$app->mailer->sendEmail($this,'user-block');

        //Yii::$app->mailer->sendEmail($this,$emailTemplate);

        $trx->commit();
    }

    public function undelete() {
        if ($this->status==static::STATUS_DELETED) {
            $this->setAttributes(json_decode($this->deleted_backup,true),false);

            $this->deleted_dt=null;
            $this->deleted_backup=null;
            $this->deleted_first_name=null;
            $this->deleted_last_name=null;
            $this->deleted_email=null;
            $this->dt_status_change = null;

            $this->save();
        }
    }

    public function delete($is_user_delete = false) {
        if (in_array($this->status,[static::STATUS_DELETED])) {
            return;
        }

        $trx=$this->db->beginTransaction();

        $this->deleted_dt=(new EDateTime())->sqlDateTime();
        $this->deleted_backup=json_encode($this->getAttributes([
            'first_name','last_name','nick_name','email','avatar_file_id','status','visibility_address1','visibility_address1','visibility_profession','visibility_about','visibility_marital_status','password'
        ]));
        $this->deleted_first_name=$this->first_name;
        $this->deleted_last_name=$this->last_name;
        $this->deleted_email=$this->email;

        $this->first_name='';
        $this->last_name='';
        $this->nick_name=null;
        $this->email='jugl'.$this->id.'@jugl.net';
        $this->avatar_file_id=Yii::$app->params['DeleteAvatarFileId'];
        $this->status=static::STATUS_DELETED;
        $this->visibility_address1=static::VISIBILITY_NONE;
        $this->visibility_address2=static::VISIBILITY_NONE;
        $this->visibility_profession=static::VISIBILITY_NONE;
        $this->visibility_about=static::VISIBILITY_NONE;
        $this->visibility_marital_status=static::VISIBILITY_NONE;

        $this->auth_key=Yii::$app->security->generateRandomString(32);
        $this->access_token=Yii::$app->security->generateRandomString(32);
        $this->password=Yii::$app->security->generateRandomString(32);
        $this->encryptPwd();

        if($is_user_delete) {
            $this->is_user_profile_delete = 1;
        }

        $this->dt_status_change = (new EDateTime())->sqlDateTime();

        $this->save();

        foreach($this->userDevices as $device) {
            $device->delete();
        }

        Yii::$app->db->createCommand("UPDATE offer SET status=:status WHERE user_id=:user_id", [
            ':status'=>Offer::STATUS_DELETED,
            ':user_id'=>$this->id
        ])->execute();

        Yii::$app->db->createCommand("UPDATE search_request SET status=:status WHERE user_id=:user_id", [
            ':status'=>SearchRequest::STATUS_DELETED,
            ':user_id'=>$this->id
        ])->execute();

        Yii::$app->db->createCommand("delete from chat_conversation where second_user_id=:user_id",[
           ':user_id'=>$this->id
        ])->execute();

        Yii::$app->db->createCommand("delete from chat_user_contact where second_user_id=:user_id",[
            ':user_id'=>$this->id
        ])->execute();

        Yii::$app->db->createCommand("UPDATE trollbox_message SET status=:status WHERE user_id=:user_id", [
            ':status'=>TrollboxMessage::STATUS_DELETED,
            ':user_id'=>$this->id
        ])->execute();

        $trx->commit();

        Yii::$app->mailer->sendEmail($this,'user-delete');
    }

    public static function updateStatOfferYearTurnover($userId=null) {
        $params=[];

        $addWhere1=$addWhere2='';

        if ($userId) {
            $addWhere1=' and user_id=:user_id';
            $addWhere2=' where user.id=:user_id';
            $params[':user_id']=$userId;
        }

        $sql="
            update user
            left outer join (
              select user_id,sum(price) as sum
              from offer
              where create_dt>=DATE_SUB(NOW(), interval 1 YEAR) and status='CLOSED' $addWhere1
              group by user_id
            ) as tdata on (tdata.user_id=user.id)
            set stat_offer_year_turnover=COALESCE(tdata.sum,0)
            $addWhere2";

        Yii::$app->db->createCommand($sql,$params)->execute();
    }

    public static function updateStatActiveSearchRequests($userId=null) {
        $params=[];

        $addWhere1=$addWhere2='';

        if ($userId) {
            $addWhere1=' and user_id=:user_id';
            $addWhere2=' where user.id=:user_id';
            $params[':user_id']=$userId;
        }

        $sql="
            update user
            left outer join (
              select user_id,count(*) as cnt
              from search_request
              where status='ACTIVE' $addWhere1
              group by user_id
            ) as tdata on (tdata.user_id=user.id)
            set stat_active_search_requests=COALESCE(tdata.cnt,0)
            $addWhere2";

        Yii::$app->db->createCommand($sql,$params)->execute();
    }

    public static function updateStatOffersViewBuyRatio($userId=null) {
        $params=[];

        $addWhere1=$addWhere2='';

        if ($userId) {
            $addWhere1=' and offer_request.user_id=:user_id';
            $addWhere1a=' where user_id=:user_id';
            $addWhere2=' where user.id=:user_id';
            $params[':user_id']=$userId;
        }

        $sql="
            update user
            left outer join (
              select offer_request.user_id,count(*) as cnt
              from offer_request
              join offer on (offer.id=offer_request.offer_id)
              where offer_request.status='ACCEPTED' $addWhere1
              group by offer_request.user_id
            ) as tdata on (tdata.user_id=user.id)
            left outer join (
              select user_id,count(*) as cnt
              from offer_view
              $addWhere1a
              group by user_id
            ) as tdata2 on (tdata2.user_id=user.id)
            set stat_offers_view_buy_ratio=IF(tdata.cnt>0,COALESCE(tdata2.cnt,0)/tdata.cnt,0)
            $addWhere2";

        Yii::$app->db->createCommand($sql,$params)->execute();
    }

    public static function updateStatMessagesPerDay($userId=null) {
        $params=[];

        $addWhere1=$addWhere2='';

        if ($userId) {
            $addWhere1=' and user_id=:user_id';
            $addWhere2=' where user.id=:user_id';
            $params[':user_id']=$userId;
        }

        $sql="
            update user
            left outer join (
              select user_id,count(*) as cnt
              from chat_message
              where type in ('OUTGOING_UNDELIVERED','OUTGOING_UNREADED','OUTGOING_READED') and
              dt>=DATE_SUB(NOW(),interval 180 day) $addWhere1
              group by user_id
            ) as tdata on (tdata.user_id=user.id)
            set stat_messages_per_day=COALESCE(tdata.cnt,0)/IF(DATEDIFF(NOW(),registration_dt)>180,180,IF(DATEDIFF(NOW(),registration_dt)<=0,1,DATEDIFF(NOW(),registration_dt)))
            $addWhere2";

        Yii::$app->db->createCommand($sql,$params)->execute();
    }

    public static function updateUserOfferRequestCompletedInterest($userId=null) {
        $trx=Yii::$app->db->beginTransaction();

        $params=[];
        if ($userId) {
            $addWhere1=' where user_id=:user_id';
            $addWhere2=' where offer_request.user_id=:user_id';
            $params[':user_id']=$userId;
        }

        Yii::$app->db->createCommand("delete from user_offer_request_completed_interest $addWhere1",$params)->execute();

        Yii::$app->db->createCommand("
        insert into user_offer_request_completed_interest(user_id,interest_id) (
          select distinct offer_request.user_id,offer_interest.level1_interest_id
          from offer
          join offer_request on (offer_request.offer_id=offer.id)
          join offer_interest on (offer_interest.offer_id=offer.id)
          $addWhere2
        )",$params)->execute();

        $trx->commit();
    }

    public function addOfferSearchFilterConditions($query) {
		
		$query->innerJoin('(select distinct level1_interest_id from user_interest where user_id=:user_id) as tmp1','tmp1.level1_interest_id=offer_interest.level1_interest_id',[':user_id'=>$this->id]);

        $zipCoords=\app\models\ZipCoords::findOne(['country_id'=>$this->country_id ? $this->country_id:64,'zip'=>$this->zip]);

        $query->leftJoin('zip_coords','zip_coords.zip=offer.uf_zip and (
            (offer.uf_country_id is null and zip_coords.country_id=64)
            or offer.uf_country_id=zip_coords.country_id
        )');

        // offer.uf_enabled=0 or 
        $query->andWhere("
             (
                    (offer.uf_age_from is null or DATE_ADD(:birthday,interval offer.uf_age_from year)<=NOW())
                and (offer.uf_age_to is null or DATE_ADD(:birthday,interval offer.uf_age_to+1 year)>NOW())

                and (offer.uf_sex='A' or offer.uf_sex=:sex)
                and (offer.uf_packet='ALL' or offer.uf_packet=:packet)

                and (offer.uf_offer_request_completed_interest_id is null or exists(
                    select * from user_offer_request_completed_interest uorci
                    where uorci.user_id=:user_id
                        and uorci.interest_id=offer.uf_offer_request_completed_interest_id
                    ))

                and (offer.uf_member_from is null or DATE_ADD(:registration_dt,interval offer.uf_member_from day)<=NOW())
                and (offer.uf_member_to is null or DATE_ADD(:registration_dt,interval offer.uf_member_to day)>NOW())

                and (offer.uf_offers_view_buy_ratio_from is null or offer.uf_offers_view_buy_ratio_from<=:stat_offers_view_buy_ratio)
                and (offer.uf_offers_view_buy_ratio_to is null or offer.uf_offers_view_buy_ratio_to>=:stat_offers_view_buy_ratio)

                and (offer.uf_city is null or offer.uf_city='' or offer.uf_city=:city)
                and (offer.uf_zip is null or offer.uf_zip='' or (
                        (offer.uf_distance_km is null and offer.uf_zip=:zip)
                        or (offer.uf_distance_km is not null and :user_have_zip_coords=1 and (

                            offer.uf_distance_km>=3959 * acos( cos( radians(:lattitude) )
                                  * cos( radians(zip_coords.lattitude) )
                                  * cos( radians(zip_coords.longitude) - radians(:longitude)) + sin(radians(:lattitude))
                                  * sin( radians(zip_coords.lattitude) ))
                        ))
                    )
                )

                and (offer.uf_country_id is null or offer.uf_country_id=:country_id)

                and (offer.uf_offer_year_turnover_from is null or offer.uf_offer_year_turnover_from<=:stat_offer_year_turnover)
                and (offer.uf_offer_year_turnover_to is null or offer.uf_offer_year_turnover_to>=:stat_offer_year_turnover)

                and (offer.uf_active_search_requests_from is null or offer.uf_active_search_requests_from<=:active_search_requests)

                and (offer.uf_messages_per_day_from is null or offer.uf_messages_per_day_from<=:stat_messages_per_day)
                and (offer.uf_messages_per_day_to is null or offer.uf_messages_per_day_to>=:stat_messages_per_day)

                and (offer.uf_balance_from is null or offer.uf_balance_from<=:balance)
            )
        ",[
            ':birthday'=>(new EDateTime($this->birthday))->sqlDate(),
            ':sex'=>$this->sex,
            ':packet'=>$this->packet,
            ':registration_dt'=>(new EDateTime($this->registration_dt))->sqlDateTime(),
            ':stat_offers_view_buy_ratio'=>$this->stat_offers_view_buy_ratio,
            ':city'=>$this->city,
            ':zip'=>$this->zip,
            ':user_have_zip_coords'=>$zipCoords ? 1:0,
            ':lattitude'=>$zipCoords ? $zipCoords->lattitude:null,
            ':longitude'=>$zipCoords ? $zipCoords->longitude:null,
            ':country_id'=>$this->country_id,
            ':active_search_requests'=>$this->stat_active_search_requests,
            ':stat_offer_year_turnover'=>$this->stat_offer_year_turnover,
            ':stat_messages_per_day'=>$this->stat_messages_per_day,
            ':balance'=>$this->balance
        ]);
		
    }

	public function addAdvertisingFilterConditions($query) {	
		$query->innerJoin('(select distinct level1_interest_id from user_interest where user_id=:user_id) as tmp1','tmp1.level1_interest_id=advertising_interest.level1_interest_id',[':user_id'=>$this->id]);	
    }

    public function getUserBankDatas()
    {
        return $this->hasMany('\app\models\UserBankData', ['user_id' => 'id'])->orderBy('sort_order');
    }

    public function getUserDeliveryAddresses()
    {
        return $this->hasMany('\app\models\UserDeliveryAddress', ['user_id' => 'id'])->orderBy('id desc');
    }

    public function getCountries() {
        $countries = [];
        foreach(Country::getList() as $key => $value) {
            $countries[] = [
                'id'=>$key,
                'country'=>$value
            ];
        }
        return $countries;
    }
	
	public function getCountryId(){
	$id=Yii::$app->user->identity->country_id;
	return $id;
	}

    public static function sendNotFinishedRegistrationNotifications() {
        $query=static::findBySql("
        select u.*
        from user u
        where u.status in (:status_registered,:status_logined,:status_active) and u.parent_id is not null and u.parent_registration_bonus=0 and
          u.registered_by_become_member=0 and 
          u.registration_dt<DATE_SUB(NOW(),INTERVAL 24 HOUR) and
          u.no_membership_payment_notified=0
        ",[
            ':status_registered'=>static::STATUS_REGISTERED,
            ':status_logined'=>static::STATUS_LOGINED,
            ':status_active'=>static::STATUS_ACTIVE
        ])->with(['parent']);

        foreach($query->batch(100) as $usersBatch) {
            Yii::$app->db->transaction(function() use ($usersBatch) {
                foreach($usersBatch as $user) {
                    UserEvent::NotFinishedRegistrationNotification($user);
                    $user->no_membership_payment_notified=1;
                    $user->save();
                }
            });
        }
    }

    public function viewedOffers() {
        if ($this->stat_new_offers>0) {
            $this->stat_new_offers=0;
            $this->save();

            \app\components\ChatServer::statusUpdate([$this->id]);
        }
    }

    public function viewedSearchRequests() {
        if ($this->stat_new_search_requests>0) {
            $this->stat_new_search_requests=0;
            $this->save();

            \app\components\ChatServer::statusUpdate([$this->id]);
        }
    }

    public function viewedOffersRequests() {
        if ($this->stat_new_offers_requests>0) {
            $this->stat_new_offers_requests=0;
            $this->save();

            \app\components\ChatServer::statusUpdate([$this->id]);
        }
    }

    public function viewedSearchRequestsOffers() {
        if ($this->stat_new_search_requests_offers>0) {
            $this->stat_new_search_requests_offers=0;
            $this->save();

            \app\components\ChatServer::statusUpdate([$this->id]);
        }
    }

    public function processApplicationLogin() {
        if ($this->status==static::STATUS_REGISTERED) {
            $trx=Yii::$app->db->beginTransaction();

            $this->invitation_notification_start=(new EDateTime())->sql();
            $this->setNextInvitationPushNotificationTime();
            $this->setNextInvitationEmailNotificationTime();
            $this->status=static::STATUS_ACTIVE;
            $this->save();

            $trx->commit();
        }

        if ($this->registration_from_desktop>0) {
            $trx=Yii::$app->db->beginTransaction();

            static::updateAll(['registration_from_desktop'=>0],['id'=>$this->id]);
            $this->addRegistrationBonusToParent();

            $trx->commit();
        }
    }

    public function setNextInvitationPushNotificationTime() {
        if (!$this->invitation_notification_start) {
            $this->invitation_notification_start=(new \app\components\EDateTime())->sql();
        }

        $next=new EDateTime();
        while(true) {
            $next->modify('+3 hours');
            $hours=intval($next->format('H'));
            if ($hours>=8 && $hours<22) {
                break;
            }
        }

        $dateInterval=$next->getTimestamp()-(new \app\components\EDateTime($this->invitation_notification_start))->getTimestamp();
        $this->next_invitation_notification_push=$dateInterval<24*3600 ?  $next->sql():null;
    }

    public function setNextInvitationEmailNotificationTime() {
        $next=new EDateTime();

        $next->modify('+1 week');
        while (true) {
            $next->modify('+1 day');

            $weekDay=intval($next->format('N'));
            if ($weekDay<=5) {
                $next->setTime(9+($weekDay-1)*3,0);
                break;
            }
        }

        $this->next_invitation_notification_email=$next->sql();
    }

    public function sendInvitationPushNotification() {
        \app\components\ChatServer::pushMessage([
            'user_id'=> $this->id,
            'link'=>'view-invite.html',
            'title'=> Yii::t('app','Erinnerung'),
            'text'=> Yii::t('app','Freunde einladen nicht vergessen! Hallo, denkst Du an den Tipp mit dem Freunde einladen? Ab 5 Partnern hast Du mehr Erfolg und Dein Netzwerk fängt an zu wachsen.'),
            'type'=>'activity'
        ]);
    }

    public function sendInvitationEmailNotification() {
        //Yii::warning('send to '.$this->email);
        Yii::$app->mailer->sendEmail($this,'friend_invitation_notification');
    }

    public function getFriendInvitationsLeftCount() {
        return 5-\app\models\Invitation::find()->where(['user_id'=>$this->id])->count();
    }

    public static function sendInvitationPushNotifications() {
        foreach(static::find()->where("next_invitation_notification_push<NOW()")->batch(100) as $users) {
            foreach($users as $user) {
                if ($user->getFriendInvitationsLeftCount()>0) {
                    $user->sendInvitationPushNotification();
                    $user->setNextInvitationPushNotificationTime();
                } else {
                    $user->next_invitation_notification_push=null;
                }
                $user->save();
            }
        }
    }

    public static function sendInvitationEmailNotifications() {
        foreach(static::find()->where("next_invitation_notification_email<NOW()")->batch(100) as $users) {
            foreach($users as $user) {
                if ($user->getFriendInvitationsLeftCount()>0) {
                    $user->sendInvitationEmailNotification();
                    $user->setNextInvitationEmailNotificationTime();
                } else {
                    $user->next_invitation_notification_push=null;
                }
                $user->save();
            }
        }
    }

    public static function sendAppLoginNotifications() {
        foreach(static::find()->where("invitation_notification_start is null and app_login_notifications_sent=0 and registration_dt<DATE_SUB(NOW(),INTERVAL 1 WEEK)")->batch(100) as $users) {
            foreach($users as $user) {
                Yii::$app->mailer->sendEmail($user,'app_login_notification');

                $user->app_login_notifications_sent=1;
                $user->save();
            }
        }

        foreach(static::find()->where("invitation_notification_start is null and app_login_notifications_sent=1 and registration_dt<DATE_SUB(NOW(),INTERVAL 2 WEEK)")->batch(100) as $users) {
            foreach($users as $user) {
                Yii::$app->sms->send($user->phone,Yii::t('app',"Hallo,\nwir wollten Dich nur noch einmal höflich daran erinnern, dass Du Dich noch nicht in der JuglApp eingeloggt hast."));

                $user->app_login_notifications_sent=2;
                $user->save();
            }
        }
    }

    public static function getPercentProfileData() {

        $userData = static::find()
            ->where(['id'=>Yii::$app->user->identity->getId()])
            ->with('userBankDatas')
            ->one();

        $profileData = $userData->toArray([
            'avatar_file_id','first_name','last_name','nick_name','company_name','phone','email','sex','birthday',
            'street','house_number','zip','city','country_id','profession','marital_status','about','paypal_email'
        ]);

        count($userData->userBankDatas)>0?$profileData = array_merge($profileData, ['bankData'=>1]):$profileData = array_merge($profileData, ['bankData'=>'']);
        $countMax = count($profileData);

        $profileDataNotEmpty = array_filter($profileData, function($element) {
            return !empty($element);
        });

        $current = count($profileDataNotEmpty);
        $percent = round(($current*100)/$countMax);

        return ['percent'=>$percent];
    }

    public function updateStatus() {
        \app\components\ChatServer::statusUpdate([$this->id]);
    }

    public function packetCanBeSelected() {
        if ($this->packet===null) {
            $this->packet='';
            $this->save();
            $this->on(\yii\web\Response::EVENT_AFTER_SEND, [$this, 'updateStatus']);
        }
    }

    public function addRegistrationBonusToParent($byBecomeMember=false) {
        if (
            $this->parent && in_array($this->packet,[\app\models\User::PACKET_VIP,\app\models\User::PACKET_VIP_PLUS,static::PACKET_STANDART])
            && (!$this->registration_from_desktop || $this->packet==\app\models\User::PACKET_VIP || $this->packet==\app\models\User::PACKET_VIP_PLUS) &&
            ($this->validation_phone_status==static::VALIDATION_PHONE_STATUS_VALIDATED || $this->packet==\app\models\User::PACKET_VIP || $this->packet==\app\models\User::PACKET_VIP_PLUS)
        ) {
            $isUpgrade=($this->oldAttributes['packet']!=$this->packet && $this->oldAttributes['packet']!='');
            $settingName='BONUS'.($this->registered_by_become_member ? '_BECOME_MEMBER':'').'_IAM_'.($this->parent->packet!='' ? $this->parent->packet:static::PACKET_STANDART);
            $settingName.=$isUpgrade ? '_UPGRADE_'.$this->oldAttributes['packet']:'_REGISTER';
            $settingName.='_'.$this->packet;

            $settingMaxName='BONUS'.($this->registered_by_become_member ? '_BECOME_MEMBER':'').'_IAM_VIP_PLUS';
            $settingMaxName.=$isUpgrade ? '_UPGRADE_'.$this->oldAttributes['packet']:'_REGISTER';
            $settingMaxName.='_'.$this->packet;

            if (!$this->registered_by_become_member) {
                if ($isUpgrade) {
                    switch ($this->packet) {
                        case User::PACKET_VIP:
                            $comment = Yii::t('app', 'Hat ein Upgrade zu Premium gemacht. Dafür erhältst Du [sum][/sum] Jugls', [

                            ]);
                        break;
                        case User::PACKET_VIP_PLUS:
                            $comment = Yii::t('app', 'Hat ein Upgrade zu PremiumPlus gemacht. Dafür erhältst Du [sum][/sum] Jugls', [

                            ]);
                            break;
                    }
                } else {
                    $comment = Yii::t('app', 'Hat sich auf Deine Einladung hin bei jugl.net angemeldet. Dafür erhältst Du [sum][/sum]', [

                    ]);
                }
            } else {
                if ($isUpgrade) {
                    switch ($this->packet) {
                        case User::PACKET_VIP:
                            $comment = Yii::t('app', 'Hat ein Upgrade zu Premium gemacht. Dafür erhältst Du [sum][/sum] Jugls', [

                            ]);
                            break;
                        case User::PACKET_VIP_PLUS:
                            $comment = Yii::t('app', 'Hat ein Upgrade zu PremiumPlus gemacht. Dafür erhältst Du [sum][/sum] Jugls', [

                            ]);
                            break;
                    }
                } else {
                    $comment = Yii::t('app', 'Du hast die Einladungsbitte des Users [user][/user] angenommen. Dafür erhältst Du [sum][/sum]', [

                    ]);
                }
            }

            $commentOut=Yii::t('app','Hat Dich zu jugl.net eingeladen. Deshalb gibst Du [sum][/sum] Deiner Einnahmen für „{user} hat sich registriert“ an [user][/user] ab',[
                'user'=>$this->name
            ]);

            $commentInRef=Yii::t('app','Hat {user} eingeladen. Dafür erhältst Du anteilig [sum][/sum]',[
                'user'=>$this->name
            ]);

            $commentOutRef=Yii::t('app','Hat Dich zu jugl.net eingeladen. Deshalb gibst Du [sum][/sum] Deiner Einnahmen für „{user} hat ein Mitglied eingeladen“ an [user][/user] ab',[
                'user'=>$this->parent->name
            ]);

            $bonusSum=Setting::get($settingName);

            if ($this->validation_status==\app\models\User::VALIDATION_STATUS_SUCCESS) {
                $bonusSum=max($bonusSum,Setting::get('IDENTIFY_PARENT_BONUS'));
            }

            $bonusMaxSum=Setting::get($settingMaxName);
            if ($this->validation_status==\app\models\User::VALIDATION_STATUS_SUCCESS) {
                $bonusMaxSum=max($bonusMaxSum,Setting::get('IDENTIFY_PARENT_BONUS'));
            }

            $lockModel=static::findBySql("select * from user where id=:id for update",[':id'=>$this->id])->one();

            $addSum=$bonusSum-$lockModel->parent_registration_bonus;
            $addMaxSum=$bonusMaxSum-$lockModel->parent_registration_bonus;

            if ($addSum>1e-6) {
                $this->parent->distributeReferralPayment(
                    $addSum,
                    $this,
                    \app\models\BalanceLog::TYPE_IN_REG_REF,
                    \app\models\BalanceLog::TYPE_IN_REG_REF_REF,
                    \app\models\BalanceLog::TYPE_IN_REG_REF_REF,
                    $comment, 0, $commentOut, $commentInRef, $commentOutRef, false
                );

                static::updateAllCounters(['parent_registration_bonus'=>$addSum],['id'=>$this->id]);
                //$this->parent_registration_bonus += $addSum;
                //$this->save();

                if ($addMaxSum>$addSum+1e-6) {
                    UserEvent::addVipPlusBonusNotification($this->parent,$addSum,$addMaxSum);
                }
            }
        }
    }

    public function addParentPacketStats() {
        if ($this->parent) {
            if ($this->packet==static::PACKET_STANDART) {
                $this->parent->stat_referrals_standart=$this->parent->stat_referrals_standart+1;
                $this->parent->save();
            }

            if ($this->packet==static::PACKET_VIP) {
                $this->parent->stat_referrals_vip=$this->parent->stat_referrals_vip+1;
                $this->parent->save();
            }

            if ($this->packet==static::PACKET_VIP_PLUS) {
                $this->parent->stat_referrals_vip_plus=$this->parent->stat_referrals_vip_plus+1;
                $this->parent->save();
            }
        }
    }
	public function addDelayInviteMember(){
		if ($this->parent) {
			$this->parent->delay_invited_member += \app\models\Setting::get('TIME_DELAY_INVITED_MEMBER');
			$this->parent->save();
		}
	}
	
	public function resetDelayInviteMember(){
		$sql = "UPDATE user SET delay_invited_member=0";
        Yii::$app->db->createCommand($sql)->execute();
	}									

    public function updateParentPacketStatsAfterUpgradeFromStandartToVip() {
        if ($this->parent) {
            if (in_array($this->packet,[\app\models\User::PACKET_VIP,\app\models\User::PACKET_VIP_PLUS])) {
                $this->parent->stat_referrals_vip=$this->parent->stat_referrals_vip+1;
                $this->parent->stat_referrals_standart=$this->parent->stat_referrals_standart-1;
                $this->parent->save();
            }
        }
    }

    public function updateParentPacketStatsAfterUpgradeFromStandartToVipPlus() {
        if ($this->parent) {
            if (in_array($this->packet,[\app\models\User::PACKET_VIP,\app\models\User::PACKET_VIP_PLUS])) {
                $this->parent->stat_referrals_vip=$this->parent->stat_referrals_vip+1;
                $this->parent->stat_referrals_standart=$this->parent->stat_referrals_standart-1;
                $this->parent->save();
            }
        }
    }

    public function updateParentPacketStatsAfterUpgradeFromVipToVipPlus() {
        if ($this->parent) {
            if (in_array($this->packet,[\app\models\User::PACKET_VIP,\app\models\User::PACKET_VIP_PLUS])) {
                $this->parent->stat_referrals_vip=$this->parent->stat_referrals_vip+1;
                $this->parent->stat_referrals_vip=$this->parent->stat_referrals_vip-1;
                $this->parent->save();
            }
        }
    }

    public function packetSelected() {
        $now=new EDateTime();

        if ($this->oldAttributes['packet']=='' && in_array($this->packet,[static::PACKET_STANDART,static::PACKET_VIP,static::PACKET_VIP_PLUS])) {
            $this->addRegistrationBonusToParent();
            $this->addParentPacketStats();
            Yii::$app->response->on(\yii\web\Response::EVENT_AFTER_SEND, [$this, 'updateStatus']);
        }

        if ($this->oldAttributes['packet']==static::PACKET_STANDART && $this->packet==static::PACKET_VIP) {
            $this->addRegistrationBonusToParent();
            $this->updateParentPacketStatsAfterUpgradeFromStandartToVip();
            Yii::$app->response->on(\yii\web\Response::EVENT_AFTER_SEND, [$this, 'updateStatus']);
        }

        if ($this->oldAttributes['packet']==static::PACKET_STANDART && $this->packet==static::PACKET_VIP_PLUS) {
            $this->addRegistrationBonusToParent();
            $this->updateParentPacketStatsAfterUpgradeFromStandartToVipPlus();
            Yii::$app->response->on(\yii\web\Response::EVENT_AFTER_SEND, [$this, 'updateStatus']);
        }

        if ($this->oldAttributes['packet']==static::PACKET_VIP && $this->packet==static::PACKET_VIP_PLUS) {
            $this->addRegistrationBonusToParent();
            $this->updateParentPacketStatsAfterUpgradeFromVipToVipPlus();
            Yii::$app->response->on(\yii\web\Response::EVENT_AFTER_SEND, [$this, 'updateStatus']);
        }

        if ($this->oldAttributes['packet']=='' && $this->packet!='') {
            Yii::$app->db->createCommand("UPDATE user SET dt_packet_select=:dt_packet_select WHERE id=:user_id", [
                'dt_packet_select'=>$now->sqlDateTime(),
                ':user_id'=>$this->id
            ])->execute();

            if($this->packet==static::PACKET_STANDART) {
                \app\models\DailyStats::packetSelectSTANDARD();
            }

            if(in_array($this->packet,[\app\models\User::PACKET_VIP,\app\models\User::PACKET_VIP_PLUS])) {
                \app\models\DailyStats::packetSelectVIP();
            }
        }

        if ($this->oldAttributes['packet']==static::PACKET_STANDART && in_array($this->packet,[\app\models\User::PACKET_VIP,\app\models\User::PACKET_VIP_PLUS])) {
            Yii::$app->db->createCommand("UPDATE user SET dt_packet_upgrade=:dt_packet_upgrade WHERE id=:user_id", [
                'dt_packet_upgrade'=>$now->sqlDateTime(),
                ':user_id'=>$this->id
            ])->execute();
        }

    }
    
    public function setRegistrationIP() {
        $this->registration_ip=Yii::$app->request->userIP;
    }

    public function updateStatBuyedJugl($jugl_sum) {
        Yii::$app->db->createCommand("UPDATE user SET stat_buyed_jugl=stat_buyed_jugl+:jugl_sum WHERE id=:user_id", [
            ':jugl_sum'=>$jugl_sum,
            ':user_id'=>$this->id
        ])->execute();
    }

    public function hasGoodBalance() {
        $this->refresh();

        return $this->balance>0;
    }

    public function getLevel1InterestIds($type='OFFER') {
        $level1InterestsIds=Yii::$app->db->createCommand("select distinct level1_interest_id from user_interest where type=:type and user_id=:user_id",[':user_id'=>$this->id,':type'=>$type])->queryColumn();

        if (empty($level1InterestsIds)) {
            $level1InterestsIds=[0];
        }
        return $level1InterestsIds;
    }

    public function getChatUser()
    {
        return $this->hasOne('\app\models\ChatUser', ['user_id' => 'id']);
    }

    public function getInvitationWinner()
    {
        return $this->hasOne('\app\models\UserBecomeMemberInvitation', ['user_id' => 'id'])->andWhere(['is_winner'=>1]);
    }

    public function getInvitation()
    {
        return $this->hasOne('\app\models\UserBecomeMemberInvitation', ['user_id' => 'id'])->andWhere(['second_user_id'=>Yii::$app->user->id]);
    }

    public static function sendTeamleaderFeedbackNotification() {
        $time=(new EDateTime())->modify("-".\app\models\Setting::get('TEAM_CHANGE_PERIOD_DAYS')." minute");
        $query=static::findBySql("select * from user where registered_by_become_member=1 and teamleader_feedback_notified=0 and registration_dt<:dt",[':dt'=>$time->sqlDateTime()]);
        foreach($query->batch(100) as $users) {
            $trx=Yii::$app->db->beginTransaction();
            foreach($users as $user) {
                if (UserTeamFeedback::find()->where(['second_user_id'=>$user->id])->count()==0) {
                    UserEvent::addTeamChange($user->id,null,Yii::t('app','Bitte bewerte {user} für sein Teamleading. [teamleaderFeedback]',['user'=>$user->parent->name]));
                }
                $user->teamleader_feedback_notified=1;
                $user->save();
            }
            $trx->commit();
        }
    }

    public function sendEmailValidation() {
        $link=\yii\helpers\Url::to(['site/activation','code'=>Yii::$app->security->hashData($this->id,Yii::$app->params['emailValidationSecret'])],true);

        $res=Yii::$app->mailer->sendEmail($this,'email_validation',['user'=>$this,'link'=>$link]);

        if (!$res) {
            throw new \yii\web\ServerErrorHttpException("Can't send validation email");
        }
    }

    public function updateStatAwaitingFeedbacks() {
        $searchRequestOffersCnt=Yii::$app->db->createCommand("
          select count(*) 
          from search_request sr 
          join search_request_offer sro on (sro.search_request_id=sr.id and sro.status='ACCEPTED' and sro.user_feedback_id is null)
          where sr.user_id=:user_id and sr.status!='DELETED' and sr.status!='UNLINKED'
          ",[':user_id'=>$this->id])->queryScalar();

        $offerRequestCnt=Yii::$app->db->createCommand("
          select count(*) 
          from offer o 
          join offer_request ofr on (ofr.offer_id=o.id and ofr.status='ACCEPTED' and ofr.pay_status='CONFIRMED' and ofr.user_feedback_id is null)
          where o.user_id=:user_id and o.status!='DELETED' and o.status!='UNLINKED'
          ",[':user_id'=>$this->id])->queryScalar();

        $searchRequestOffersCounterCnt=Yii::$app->db->createCommand("
          select count(*) 
          from search_request sr 
          join search_request_offer sro on (sro.search_request_id=sr.id and sro.status='ACCEPTED' and sro.counter_user_feedback_id is null)
          where sro.user_id=:user_id and sr.status!='DELETED' and sr.status!='UNLINKED'
          ",[':user_id'=>$this->id])->queryScalar();

        $offerRequestCounterCnt=Yii::$app->db->createCommand("
          select count(*) 
          from offer o 
          join offer_request ofr on (ofr.offer_id=o.id and ofr.status='ACCEPTED' and ofr.pay_status='CONFIRMED' and ofr.counter_user_feedback_id is null)
          where ofr.user_id=:user_id and o.status!='DELETED' and o.status!='UNLINKED'
          ",[':user_id'=>$this->id])->queryScalar();


        $new_stat_awaiting_feedbacks=$searchRequestOffersCnt+$offerRequestCnt+$searchRequestOffersCounterCnt+$offerRequestCounterCnt;
        if ($this->stat_awaiting_feedbacks!=$new_stat_awaiting_feedbacks) {
            $this->stat_awaiting_feedbacks=$new_stat_awaiting_feedbacks;
            $this->save();
            ChatServer::updateInitInfo([$this->id]);
        }
		return $new_stat_awaiting_feedbacks;									
    }

    public function showTeamleaderFeedbackNotification() {
        if (!$this->teamleader_feedback_notification_at && !$this->parent_id) return false;

        return (new \app\components\EDateTime($this->teamleader_feedback_notification_at)<
            new \app\components\EDateTime());
    }

    public function renewTeamleaderFeedbackNotificationAt($doSave=false) {
        $time=$this->getTeamChangeFinishTime();
        // if not registered by become member
        if ($time===false) {
            $time=(new \app\components\EDateTime())->modify('+ 7 day');
        }
        $this->teamleader_feedback_notification_at=$time->sqlDateTime();

        if ($doSave) {
            $this->save();
        }
    }

    public static function processExpiredVipPackets() {
        $usersQuery=User::find()->where('vip_active_till<NOW() and (packet=:packet_vip or packet=:packet_vip_plus) and vip_lifetime=0',[
            ':packet_vip'=>static::PACKET_VIP,
            ':packet_vip_plus'=>static::PACKET_VIP_PLUS
        ]);

        foreach($usersQuery->batch(100) as $users) {
            foreach($users as $user) {
                $trx=Yii::$app->db->beginTransaction();

                $user->packet=static::PACKET_STANDART;
                //$user->vip_active_till=null;
                $user->save();

                $trx->commit();
            }
        }
    }

    public static function processVipPacketNotifications() {
        // add next time notification
        Yii::$app->db->createCommand("
            update `user`
            set next_vip_notification_at=DATE_SUB(vip_active_till, INTERVAL 1 WEEK)
            where 
                next_vip_notification_at is null 
                and packet='VIP' 
                and vip_lifetime=0
                and vip_active_till>NOW() 
                and vip_active_till<DATE_ADD(NOW(), INTERVAL 1 WEEK)
        ")->execute();

        $query=User::find()->andWhere('next_vip_notification_at<NOW()');
        foreach($query->batch(100) as $users) {
            foreach($users as $user) {
                $trx = Yii::$app->db->beginTransaction();

                UserEvent::addVipNotification($user);

                $nextVipNotification = (new \app\components\EDateTime($user->next_vip_notification_at))->modify("+1 week");
                $lastVipNotification = (new \app\components\EDateTime($user->vip_active_till))->modify("+4 week");

                if ($nextVipNotification > $lastVipNotification) {
                    $user->next_vip_notification_at = null;
                } else {
                    $user->next_vip_notification_at = $nextVipNotification->sqlDateTime();
                }
                $user->save();

                $trx->commit();
            }
        }
    }

    public function updateParentFreeRegistrations() {
	    if ($this->parent_id && !$this->registration_code_id) {
	        $this->parent->free_registrations_used++;
            $this->parent->save();
            if ($this->parent->getRegistrationsLimit()==$this->parent->free_registrations_used) {
                UserEvent::addFreeRegistrationsLimitReached($this->parent);
            }
        }
    }

    private function isParentOf($user) {
	    $visitedUserIds=[];

	    do {
	        if ($this->id==$user->id) {
	            return true;
            }

            $visitedUserIds[$user->id]=true;
	        $user=$user->parent;
        } while ($user && !$visitedUserIds[$user->id]);

	    return false;
    }

    public function getMovedUsersCount($toUser) {
        $model=\app\models\UserMovedUsersCount::findOne(['from_user_id'=>$this->id,'to_user_id'=>$toUser->id]);

        return $model ? $model->count:0;
    }

    public function increaseMovedUsersCount($toUser) {
	    Yii::$app->db->createCommand("
          insert into user_moved_users_count(from_user_id,to_user_id,`count`) values(:from_user_id,:to_user_id,1) on duplicate key update count=count+1
        ",[
            ':from_user_id'=>$this->id,
            ':to_user_id'=>$toUser->id
        ])->execute();
    }

    public function canDoNetworkMove($moveUser,$dstUser) {
	    return $this->packet==static::PACKET_VIP_PLUS &&
            $this->getMovedUsersCount($dstUser)<\app\models\Setting::get('PARENT_MOVING_USER_LIMIT') &&
            $this->isParentOf($moveUser) && $this->isParentOf($dstUser) &&
            !$dstUser->isParentOf($moveUser) && !$moveUser->isParentOf($dstUser);
    }

    public function stickRequestInProgress() {
	    return Yii::$app->db->createCommand("select count(*) from user_stick_to_parent_request where completed=0 and referral_user_id=:user_id",[
	        ':user_id'=>Yii::$app->user->id
            ])->queryScalar()>0;
    }

    public function getAvailableStickRequestsCount() {
        return $this->packet==static::PACKET_VIP_PLUS ? \app\models\Setting::get('STICK_TO_PARENT_REQUESTS_PER_MONTH')-
            \app\models\UserStickToParentRequest::find()->where('user_id=:user_id and expires_at>NOW()',[':user_id'=>$this->id])->count():0;
    }

    public function canCreateStickRequest($toUser) {
	    return $toUser->parent_id==$this->id &&
            !$toUser->is_stick_to_parent &&
            $this->getAvailableStickRequestsCount()>0 &&
            \app\models\UserStickToParentRequest::find()->where(['user_id'=>$this->id,'referral_user_id'=>$toUser->id])->count()==0;
    }

    public static function getCountryCountList() {
        $key=__CLASS__.__FUNCTION__.Yii::$app->language;
        $data=Yii::$app->cache->get($key);
        if ($data===false) {
            $query = Yii::$app->db->createCommand('SELECT COUNT(id) as count, country_id FROM user WHERE status=:status GROUP BY country_id', [
                ':status'=>static::STATUS_ACTIVE
            ])->queryAll();

            $countryCountData = [];
            foreach ($query as $item) {
                if($item['country_id']){
                    $countryCountData[$item['country_id']]=intval($item['count']);
                } else {
                    $countryCountData['no_country']=intval($item['count']);
                }
            }

            $data = [];
            foreach (Country::getList() as $country_id=>$country_name) {
                $idata['id']=$country_id;

                if($countryCountData[$country_id]) {
                    $idata['name']=$country_name.' ('.$countryCountData[$country_id].')';
                } else {
                    $idata['name']=$country_name.' (0)';
                }

                $idata['flag']=Country::getListShort()[$country_id];
                $data[]=$idata;
            }

            Yii::$app->cache->set($key,$data,300);
        }

        return $data;
    }

    public static function getCountryCountListNewUser() {

        $query = Yii::$app->db->createCommand('
            SELECT COUNT(id) as count, country_id 
            FROM user 
            WHERE status=:status AND registration_dt >= CURDATE()
            GROUP BY country_id', [
                ':status'=>static::STATUS_ACTIVE,
        ])->queryAll();

        $countryCountData = [];
        foreach ($query as $item) {
            if($item['country_id']){
                $countryCountData[$item['country_id']]=intval($item['count']);
            } else {
                $countryCountData['no_country']=intval($item['count']);
            }
        }

        $data = [];
        foreach (Country::getList() as $country_id=>$country_name) {
            $idata['id']=$country_id;

            if($countryCountData[$country_id]) {
                $idata['name']=$country_name.' ('.$countryCountData[$country_id].')';
            } else {
                $idata['name']=$country_name.' (0)';
            }

            $idata['flag']=Country::getListShort()[$country_id];
            $data[]=$idata;
        }

        return $data;
    }

    public static function saveTrollboxFilter($trollbox_filter) {
        $trollbox_filter = json_decode($trollbox_filter, true);
        $filter = json_decode($trollbox_filter['filter'], true);
        unset($filter['type']);
        $trollbox_filter['filter']=$filter;
        $trollbox_filter = json_encode($trollbox_filter);

        Yii::$app->db->createCommand('UPDATE user SET trollbox_filter=:trollbox_filter WHERE id=:user_id', [
            ':trollbox_filter'=>$trollbox_filter,
            ':user_id'=>Yii::$app->user->id
        ])->execute();
        return true;
    }

    public static function getTrollboxFilter() {
        $res = Yii::$app->db->createCommand('SELECT trollbox_filter FROM user WHERE id=:user_id', [
            ':user_id'=>Yii::$app->user->id
        ])->queryOne();
        return json_decode($res['trollbox_filter'], true);
    }

    public function getUserUsedDevice() {
        return $this->hasOne('\app\models\UserUsedDevice', ['user_id' => 'id']);
    }


    public function getPaymentMethodsBuyToken() {
        $data = [];
        if (!empty($this->payInRequests)) {
            foreach ($this->payInRequests as $payInRequest) {
                if ($payInRequest->type == PayInRequest::TYPE_PAY_IN_TOKEN) {
                    $data[]=$payInRequest->payment_method;
                }
            }
        }
        return array_unique($data);
    }

    public function canUploadVideoIdentification() {
        $trollboxMessage = TrollboxMessage::find()->where(['user_id'=>$this->id, 'status'=>TrollboxMessage::STATUS_ACTIVE, 'type'=>TrollboxMessage::TYPE_VIDEO_IDENTIFICATION])->one();
        return !$trollboxMessage || ((new \app\components\EDateTime($trollboxMessage->dt))->modify('+1 day')<(new \app\components\EDateTime()));
    }

}

\yii\base\Event::on(User::className(), \yii\db\ActiveRecord::EVENT_BEFORE_INSERT, function ($event) {
    $event->sender->setRegistrationIP();
});

\yii\base\Event::on(User::className(), \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE, function ($event) {
    if ($event->sender->oldAttributes['is_blocked_in_trollbox'] && !$event->sender->is_blocked_in_trollbox) {
        \app\components\Moderator::unblockUserInTrollbox($event->sender,true);
    }

    $event->sender->packetSelected();
    if (in_array($event->sender->status,[User::STATUS_BLOCKED,User::STATUS_DELETED])!=in_array($event->sender->oldAttributes['status'],[User::STATUS_BLOCKED,User::STATUS_DELETED])) {
        $event->sender->on(\yii\db\ActiveRecord::EVENT_AFTER_UPDATE,function($event) {
            $trx=Yii::$app->db->beginTransaction();
            $event->sender->recalcHierarchyNetworkStats();
            $trx->commit();
        });
    }
});

\yii\base\Event::on(User::className(), \yii\db\ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
    $event->sender->renewTeamleaderFeedbackNotificationAt(true);
    $event->sender->updateParentFreeRegistrations();
});
