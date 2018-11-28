<?php

namespace app\controllers;

use Yii;
use app\components\EDateTime;
use app\models\UserValidationPhoneNotification;

class ExtApiUserValidationPhoneNotificationController extends \app\components\ExtApiController {

    public function actionAdd() {
        if(!$this->isShowNotification()) {
            $model = new UserValidationPhoneNotification();
            $model->user_id = Yii::$app->user->id;
            $model->dt = (new EDateTime())->sql();
            $model->save();

            UserValidationPhoneNotification::deleteAll('user_id=:user_id AND dt<:dt', [
                ':user_id'=>Yii::$app->user->id,
                ':dt'=>(new EDateTime())->modify('-1 day')->sqlDateTime()
            ]);
        }

        return [
            'result'=>true
        ];
    }

    public function actionGet() {
        if($this->isShowNotification()) {
            return ['result'=>false];
        }

        $model = UserValidationPhoneNotification::find()
            ->where(['user_id'=>Yii::$app->user->id])
            ->orderBy(['id'=>SORT_DESC])
            ->limit(1)
            ->one();

        if($model->dt > (new EDateTime())->modify('-15 minute')->sqlDateTime()) {
            return ['result'=>false];
        }

        return ['result'=>true];
    }

    private function isShowNotification() {
        $model = UserValidationPhoneNotification::find()
            ->where(['user_id'=>Yii::$app->user->id])
            ->andWhere('dt>:dt', [':dt'=>(new EDateTime())->modify('-1 day')->sqlDateTime()])
            ->offset(4)->limit(1)
            ->one();

        if(!$model) {
            return false;
        }

        return true;
    }

}
