<?php

namespace app\controllers;

use Yii;
use app\models\Setting;

class ApiFriendsInvitationController extends \app\components\ApiController {

    public function actionIndex() {
        $regCostJugl=Setting::get('VIP_COST_JUGL');
        $pdJuglPercent=Setting::get('PROFIT_DISTRIBUTION_JUGL_PERCENT');
        $pdParentsPercent=Setting::get('PROFIT_DISTRIBUTION_PARENTS_PERCENT');
        return [
            'directEarn'=>$regCostJugl*(100-$pdJuglPercent-$pdParentsPercent)/100,
            'referralEarn'=>$regCostJugl*$pdParentsPercent/100
        ];
    }
}