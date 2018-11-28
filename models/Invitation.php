<?php

namespace app\models;

use Yii;
use app\components\EDateTime;
use yii\helpers\Url;
use app\components\SmsGate;

class Invitation extends \app\models\base\Invitation
{
    const TYPE_SMS='SMS';
    const TYPE_EMAIL='EMAIL';

    const STATUS_OPEN='OPEN';
    const STATUS_CLICKED='CLICKED';
    const STATUS_REGISTERED='REGISTERED';

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_id' => Yii::t('app','User ID'),
            'dt' => Yii::t('app','Dt'),
            'type' => Yii::t('app','Type'),
            'address' => Yii::t('app','Address'),
            'referral_user_id' => Yii::t('app','Referral User ID'),
        ];
    }

    static function normalizePhone($phone) {
        $digits=preg_replace('%[^0-9]+%','',$phone);
        return substr($digits,-10);
    }

    static function normalizeEmail($email) {
        return trim(strtolower($email));
    }

    public function normalizeAddress() {
        switch ($this->type) {
            case static::TYPE_EMAIL:
                $this->address=static::normalizeEmail($this->address);
                break;
            case static::TYPE_SMS:
                $this->address=static::normalizePhone($this->address);
                break;
        }
    }

    public static function getStatusList()
    {
        return [
            static::STATUS_OPEN => Yii::t('app', 'Offen'),
            static::STATUS_CLICKED => Yii::t('app', 'Akzeptiert'),
            static::STATUS_REGISTERED => Yii::t('app', 'Registriert'),
        ];
    }

    public function getStatusLabel() {
        return $this->getStatusList()[$this->status];
    }

    private function sendEmail() {
        $validator = new \yii\validators\EmailValidator();

        if (!$validator->validate($this->address,$error)) {
            return $error;
        }

        $link=Url::to(['registration/index','invId'=>$this->id],true);
        $res=Yii::$app->mailer->sendEmail($this->address,'invitation',['link'=>$link,'text'=>$this->text,'user'=>$this->user]);

        return boolval($res) ?:Yii::t('app','Sending failed');
    }

    private function sendSMS() {
        $link=Url::to(['registration/index','invId'=>$this->id],true);

        $res=Yii::$app->user->identity->getCanSendInvitationSMS();

        if ($res===true) {
            $res = Yii::$app->sms->send($this->name, str_replace('{link}',$link,$this->text));
            if ($res === true) {
                Yii::$app->user->identity->invitationSmsSent();
            }
        }

        return $res;
    }

    public function send() {
        $result=false;

        switch ($this->type) {
            case static::TYPE_EMAIL:
                $result=$this->sendEmail();
                break;
            case static::TYPE_SMS:
                $result=$this->sendSMS();
                break;
        }

        if ($result===true) {
            $this->dt=(new EDateTime())->sqlDateTime();
            $this->save();
        }

        return $result;
    }

    public static function getInvitationUrls($type,$contacts,$text) {
        $result=[];

        $dt=(new EDateTime)->sql();

        $trx=Yii::$app->db->beginTransaction();

        foreach($contacts as $contact) {

            $sModel=new Invitation;
            $sModel->type=$type;
            $sModel->address=$contact['address'];
            $sModel->normalizeAddress();

            $model=Invitation::findOne([
                'user_id'=>Yii::$app->user->id,
                'type'=>$type,
                'address'=>$sModel->address
            ]);

            if ($model) {
                $result[$contact['address']]=Url::to(['registration/index','invId'=>$model->id],true);
                continue;
            }

            $invitation=new Invitation();

            $invitation->user_id=Yii::$app->user->id;
            $invitation->dt=$dt;
            $invitation->type=$type;
            $invitation->status=static::STATUS_OPEN;
            $invitation->address=$contact['address'];
            $invitation->name=$contact['name'];
            $invitation->text=$text;

            $invitation->normalizeAddress();

            $invitation->save();

            $result[$contact['address']]=Url::to(['registration/index','invId'=>$invitation->id],true);
        }

        $trx->commit();

        return $result;
    }

    public function addInvitationStats() {
        switch ($this->type) {
            case static::TYPE_EMAIL:
                \app\models\User::updateAllCounters(['stat_invitations_email'=>1],['id'=>Yii::$app->user->id]);
                break;
            case static::TYPE_SMS:
                \app\models\User::updateAllCounters(['stat_invitations_sms'=>1],['id'=>Yii::$app->user->id]);
                break;
        }
    }

    public static function Invite($type,$addresses,$text) {
        $result=[];

        $dt=(new EDateTime)->sql();

        $count=0;

        foreach($addresses as $address) {

            $sModel=new Invitation();
            $sModel->type=$type;
            $sModel->address=$address;
            $sModel->normalizeAddress();

            if ($type==Invitation::TYPE_SMS) {
                $sModel=new Invitation();
                $sModel->type=Invitation::TYPE_SMS;
                $sModel->address=$address;
                $sModel->normalizeAddress();
                $phone=$sModel->address;

                $res=Yii::$app->user->identity->getCanSendInvitationSMS();
                if ($res!==true) {
                    $result[$address]=$res;
                    continue;
                }
                
                if (User::find()->where(['phone'=>$phone])->one()) {
                    $result[$address]=Yii::t('app','Benutzer mit dieser Telefonnummer ist bereits registriert');
                    continue;
                }
            } else {
                $sModel = new Invitation();
                $sModel->type = Invitation::TYPE_EMAIL;
                $sModel->address = $address;
                $sModel->normalizeAddress();
                $email = $sModel->address;

                if (User::find()->where(['email'=>$email])->one()) {
                    $result[$address]=Yii::t('app','Benutzer mit dieser E-Mail-Adresse ist bereits registriert');
                    continue;
                }
            }

            if (Invitation::findOne([
                'user_id'=>Yii::$app->user->id,
                'type'=>$type,
                'address'=>$sModel->address
            ])) {
                $result[$address]=$type==Invitation::TYPE_EMAIL ? Yii::t('app','Benutzer mit dieser E-Mail-Adresse ist bereits eingeladen') : Yii::t('app','Benutzer mit dieser Telefonnummer ist bereits eingeladen');
                continue;
            }

            $trx=Yii::$app->db->beginTransaction();

            $invitation=new Invitation();

            $invitation->user_id=Yii::$app->user->id;
            $invitation->dt=$dt;
            $invitation->type=$type;
            $invitation->status=static::STATUS_OPEN;
            $invitation->address=$address;
            $invitation->name=$address;
            $invitation->text=$text;

            $invitation->normalizeAddress();

            $invitation->save();

            $res = $invitation->send();

            $result[$address]=$res;
            if ($res===true) {
                $trx->commit();
            } else {
                $trx->rollback();
            }
        }

        return $result;
    }
}

\yii\base\Event::on(Invitation::className(), \yii\db\ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
    switch ($event->sender->type) {
        case Invitation::TYPE_EMAIL:
            Yii::$app->db->createCommand('UPDATE invite_me SET invited_count=invited_count+1 WHERE email=:email', [
                'email'=>$event->sender->address,
            ])->query();
            break;
        case Invitation::TYPE_SMS:
            Yii::$app->db->createCommand('UPDATE invite_me SET invited_count=invited_count+1 WHERE normalized_phone=:phone', [
                'phone'=>$event->sender->address,
            ])->query();
            break;
        default;
    }
});

\yii\base\Event::on(Invitation::className(), \yii\db\ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
    $event->sender->addInvitationStats();
});
