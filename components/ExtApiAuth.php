<?php

namespace app\components;

use Yii;
use \app\models\User;
use \app\models\UserDevice;

class ExtApiAuth extends \yii\filters\auth\AuthMethod
{
    public function authenticate($user, $request, $response)
    {
		
        if (preg_match('%^(ext-api-base/(login|is-device-used-for-registration|facebook-login-user.*|login-facebook.*)|ext-api-become-member/.*|ext-api-registration/.*|ext-api-country-registration/.*)$%',Yii::$app->controller->route)) {
            return true;
        }

        $type = $request->getHeaders()->get('X-Ext-Api-Auth-Type');
        $device_uuid = $request->getHeaders()->get('X-Ext-Api-Auth-Device-Uuid');
        $key = $request->getHeaders()->get('X-Ext-Api-Auth-Key');

        $userDevice=UserDevice::find()->where([
            'type'=>$type,
            'device_uuid'=>$device_uuid,
        ])->joinWith(['user'])->one();

        if ($userDevice) {
            // if key doesn't match, force device to logout
            if ($key=='' || $userDevice->key!=$key) {
                $userDevice->key=null;
                $userDevice->save();
                return null;
            }

            // check user status
            if (//$userDevice->user->status==User::STATUS_AWAITING_MEMBERSHIP_PAYMENT ||
                $userDevice->user->status==User::STATUS_BLOCKED ||
                $userDevice->user->status==User::STATUS_EMAIL_VALIDATION ||
                $userDevice->user->status==User::STATUS_DELETED) {
                return null;
            }

            $userDevice->user->userDevice=$userDevice;
            $user->identity=$userDevice->user;
            if (Yii::$app->controller->route=='ext-api-base/status') {
                $user->identity->logMobileActivity();

                //enabled 23.01.2017
                $nowSqlDate=(new EDateTime())->sqlDate();
                if (!$userDevice->last_seen || $userDevice->last_seen!=$nowSqlDate) {
                    $userDevice->last_seen=$nowSqlDate;
                    $userDevice->save();
                }
            }

            //Yii::$app->db->createCommand("update user_device set last_seen=NOW() where id=:id",[':id'=>$userDevice->id])->execute();

            return $userDevice->user;
        }

        return null;
    }
}
