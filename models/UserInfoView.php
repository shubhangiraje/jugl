<?php

namespace app\models;

use Yii;

class UserInfoView extends \app\models\base\UserInfoView {


    public static function getViews() {
        $userInfoViews = UserInfoView::findOne(Yii::$app->user->id);
        $data=json_decode($userInfoViews->views);
        if ($data===null) {
            $data=[];
        }
        return $data;
    }



}
