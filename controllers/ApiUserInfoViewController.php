<?php

namespace app\controllers;

use app\models\UserInfoView;
use Yii;

class ApiUserInfoViewController extends \app\components\ApiController {

    public function actionUpdate($view) {
        $data = [];
        if($model = UserInfoView::findOne(['user_id'=>Yii::$app->user->id])) {
            $data=json_decode($model->views);
            if(!in_array($view,$data)) {
                $data[]=$view;
                $model->views = json_encode($data);
                $model->save();
            }
        } else{
            $model = new UserInfoView();
            $model->user_id = Yii::$app->user->id;
            $data[]=$view;
            $model->views = json_encode($data);
            $model->save();
        }

        return [
            'result'=>$data
        ];

    }


}