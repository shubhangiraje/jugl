<?php

namespace app\models;

use Yii;

class CfrDistributionUser extends \app\models\base\CfrDistributionUser
{
    public static function process() {
        $endWorkTime=time()+60;
        $workBatchSize=5;

        while (time()<$endWorkTime) {
            $trx=Yii::$app->db->beginTransaction();

            $models=static::findBySql("select * from cfr_distribution_user where processed=0 order by id asc limit $workBatchSize for update")->all();

            foreach($models as $model) {
                if ($model->jugl_sum>0) {
                    if ($model->user->packet==\app\models\User::PACKET_VIP_PLUS) {
                        $comment=Yii::t('app','Für Deine Beiträge hast Du {votes_real} Likes erhalten mal {mult} PremiumPlus = {votes}.',[
                            'votes_real'=>$model->votes_count/CfrDistribution::VOTE_PACKET_VIP_PLUS_MULTIPLIER,
                            'votes'=>$model->votes_count,
                            'mult'=>CfrDistribution::VOTE_PACKET_VIP_PLUS_MULTIPLIER
                        ]);
                    } else {
                        $multiplier=1;
                        if ($model->user->packet==\app\models\User::PACKET_VIP) {
                            $multiplier=CfrDistribution::VOTE_PACKET_VIP_MULTIPLIER;
                        }

                        $comment=Yii::t('app','Für Deine Beiträge hast Du {votes_real} Likes erhalten. Schade, dass Du nicht PremiumPlus-Mitglied bist. Sonst wären sie als {votes} Likes vergütet worden.',[
                            'votes_real'=>$model->votes_count,
                            'votes'=>$model->votes_count/$multiplier*CfrDistribution::VOTE_PACKET_VIP_PLUS_MULTIPLIER
                        ]);
                    }

                    $model->user->distributeReferralPayment($model->jugl_sum, $model->user, \app\models\BalanceLog::TYPE_IN, \app\models\BalanceLog::TYPE_IN_REF, \app\models\BalanceLog::TYPE_IN_REF_REF, $comment);
                }
                $model->processed=1;
                $model->save();
            }

            $trx->commit();

            if (empty($models)) {
                break;
            }
        }
    }
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'cfr_distribution_id' => Yii::t('app','Cfr Distribution ID'),
            'user_id' => Yii::t('app','User ID'),
            'votes_count' => Yii::t('app','Likes'),
            'jugl_sum' => Yii::t('app','Jugl Sum'),
            'processed' => Yii::t('app','Processed'),
        ];
    }
}
