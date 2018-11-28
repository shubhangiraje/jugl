<?php

namespace app\models;

use Yii;

class AccessCode extends \app\models\base\AccessCode
{
    const TYPE_RESTORE_PASSWORD='restorePassword';

    const CODE_LENGTH=16;

    public static function gc() {
        static::deleteAll('expires<NOW()');
    }

    public static function generateCode($type,$object,$expiration_days=7)
    {
        static::gc();

        $accessCode=static::find()->where(['type'=>$type,'object'=>$object])->one();
        if (!$accessCode) {
            $accessCode=new AccessCode;
            $accessCode->type=$type;
            $accessCode->object=$object;
        }
        $accessCode->code=bin2hex(Yii::$app->security->generateRandomKey(self::CODE_LENGTH));
        $accessCode->expires=new \yii\db\Expression('DATE_ADD(NOW(),INTERVAL '.intval($expiration_days).' DAY)');
        $accessCode->save();

        return $accessCode->code;
    }

    public static function isCodeValid($type,$object,$code) {
        static::gc();

        $accessCode=self::find()->where(['type'=>$type,'object'=>$object,'code'=>$code])->one();

        return $accessCode ? true:false;
    }

    public static function deleteCode($type,$object) {
        static::deleteAll('type=:type and object=:object',[':type'=>$type,':object'=>$object]);
    }
}
