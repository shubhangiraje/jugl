<?php

namespace app\components;

use Yii;
use \app\models\User;


class Mailer extends \yii\swiftmailer\Mailer {

    public function sendEmail($emails,$view,$params=[],$files=[]) {
        $res=true;

        try {
            $message=$this->compose($view,$params);

            if (!is_array($emails)) {
                $emails=[$emails];
            }

            foreach($emails as $email) {
                if ($email instanceof User) {
                    if ($email->status==User::STATUS_DELETED || $email->setting_off_send_email) {
                        continue;
                    }
                    $email=$email->email;
                }

                foreach($files as $file) {
                    $message->attachFile($file);
                }

                // hack for test.jugl.net: don't send emails to addresses starting with '_'
                if ($email[0]=='_') {
                    Yii::warning('skip send email to "'.$email.'"');
                    continue;
                }

                $res=$message->setTo($email)->send();
            }

        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            $res=false;
        }
        return $res;
    }
}