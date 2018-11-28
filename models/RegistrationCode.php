<?php

namespace app\models;

use Yii;

class RegistrationCode extends \app\models\base\RegistrationCode
{

    public function generateCode() {
        $symbols='23456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $code='';
        for ($i=0;$i<8;$i++) {
            $code.=$symbols[rand(0,strlen($symbols)-1)];
        }

        $this->code=$code;
    }

}
