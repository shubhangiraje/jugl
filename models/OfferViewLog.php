<?php

namespace app\models;

use app\components\EDateTime;
use Yii;

class OfferViewLog extends \app\models\base\OfferViewLog {

    public static function addView($offer_id) {
        $model=new static();
        $model->offer_id=$offer_id;
        $model->user_id=Yii::$app->user->id;
        $now=new EDateTime();
        $model->create_dt=$now->sqlDateTime();
        $model->duration=5;
        $model->save();

        return $model->id;
    }

}
