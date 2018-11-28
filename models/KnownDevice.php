<?php

namespace app\models;

use Yii;

class KnownDevice extends \app\models\base\KnownDevice
{

    public function attributeLabels() {
        return array_merge(parent::attributeLabels(), [
            'device_uuid' => Yii::t('app', 'Device ID'),
            'user_id'=>Yii::t('app', 'Nutzer')
        ]);
    }

    public static function isDeviceUsed($uuid) {
        $count=Yii::$app->db->createCommand("select count(*) from known_device where device_uuid=:device_uuid for update",[':device_uuid'=>$uuid])->queryScalar();
        return $count>=10;
    }

    public static function registerForUser($uuid,$user) {
        $knownDevice=static::findOne(['device_uuid'=>$uuid,'user_id'=>$user->id]);

        if ($knownDevice) return true;

        $trx=Yii::$app->db->beginTransaction();

        if (static::isDeviceUsed($uuid)) {
            $trx->rollBack();
            return false;
        }

        $knownDevice=new self;
        $knownDevice->device_uuid=$uuid;
        $knownDevice->user_id=$user->id;
        $knownDevice->save();

        $trx->commit();

        return true;
    }
}
