<?php

namespace app\models;

use app\components\EDateTime;
use Yii;

class AnnecyReward extends \app\models\base\AnnecyReward
{
    const SIGNATURE_QUERY_NAME = 'signature';
    const HASH_ALGORITHM = 'sha1';
    const SECRET_KEY = 'WGahCjScffC74r83xMc5mt9x';

    public static function reward($params) {
        if(!static::findOne(['click_id'=>$params['click_id']])) {
            $trx=Yii::$app->db->beginTransaction();
            $model = new AnnecyReward();
            $model->load($params, '');
            $model->dt = (new EDateTime())->sqlDateTime();
            $model->save();
            Yii::info("[$model->user_id:$model->credits]",'annecy');

            $comment = Yii::t('app','ExtraCashBonus [sum][/sum] von "ExtraCash" erhalten');
            $commentOut = Yii::t('app','Hat Dich zu jugl.net eingeladen. Deshalb gibst Du [sum][/sum] Deiner Einnahmen von "ExtraCash" an [user][/user] ab');
            $commentInRef = Yii::t('app','Hat ExtraCashBonus für erhalten "ExtraCash" angeschaut. Dafür erhältst Du anteilig [sum][/sum]');
            $commentOutRef = Yii::t('app','Hat Dich zu jugl.net eingeladen. Deshalb gibst Du [sum][/sum] Deiner Einnahmen von "ExtraCash" an [user][/user] ab');

            $model->user->packetCanBeSelected();
            $model->user->distributeReferralPayment($model->credits,$model->user,\app\models\BalanceLog::TYPE_IN,\app\models\BalanceLog::TYPE_IN_REF,\app\models\BalanceLog::TYPE_IN_REF_REF, $comment, 0, $commentOut, $commentInRef, $commentOutRef,true,true);

            $trx->commit();
            return true;
        }
        return false;
    }

}
