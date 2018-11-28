<?php

namespace app\controllers;

use Yii;
use \app\models\UserDevice;


class ExtApiSettingsController extends \app\components\ExtApiController {

    function actionSave() {
        $data=Yii::$app->request->getBodyParams();

        $device = UserDevice::find()->andWhere([
            'user_id'=>Yii::$app->user->id,
            'device_uuid'=>$data['device_uuid']
        ])->one();

        if ($device) {
            foreach($data['settings'] as $k=>$v) {
                if (preg_match('%^setting_%',$k)) {
                    $device->$k=$v;
                }
            }
            $device->save();
        }

        return $device->attributes;

        return ['result'=>true];
    }

}