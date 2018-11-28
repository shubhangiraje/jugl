<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use \app\models\AnnecyReward;


class ApiAnnecyController extends Controller {


    public function actionReward($user_id, $credits, $campaign_title, $signature, $click_id) {

        $params = [
            'user_id'=>$user_id,
            'credits'=>$credits,
            'campaign_title'=>$campaign_title,
            'signature'=>$signature,
            'click_id'=>$click_id
        ];

        ksort($params);
        $signature = $params[AnnecyReward::SIGNATURE_QUERY_NAME];
        unset($params[AnnecyReward::SIGNATURE_QUERY_NAME]);
        $query_string = http_build_query($params);

        $hashed_signature = hash_hmac(AnnecyReward::HASH_ALGORITHM, $query_string, AnnecyReward::SECRET_KEY);

        if ($hashed_signature == $signature) {
            AnnecyReward::reward($params);
            header("HTTP/1.1 200 OK");
        } else {
            header('HTTP/1.1 403 Forbidden', true, 403);
        }

        Yii::$app->end();
    }
}