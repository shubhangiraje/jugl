<?php

namespace app\components;

use Yii;

class MailMessage extends \yii\swiftmailer\Message {

    public function embedImage($url) {
        $fullUrl=Yii::$app->request->hostInfo.$url;

        return $this->getSwiftMessage()->embed(\Swift_Image::fromPath($fullUrl));
    }

    public function attachFile($file) {
        $fullUrl=$file['path'];
        $attachment = \Swift_Attachment::fromPath($fullUrl);
        $attachment->setFileName($file['name']);
        return $this->getSwiftMessage()->attach($attachment);
    }


}

