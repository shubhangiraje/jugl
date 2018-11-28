<?php

namespace app\models;

use Yii;

class AdminActionLog extends \app\models\base\AdminActionLog
{
    public static function getModulesMapping() {
        return [
            '^admin-admin/'=>Yii::t('app','Admins'),
            '^admin-user/'=>Yii::t('app','Users'),
            '^admin-user-validation/'=>Yii::t('app', 'Users Validation'),
            '^admin-pay-out-request/'=>Yii::t('app', 'Payout Requests'),
            '^admin-(pay-(out|in)-packet|setting|broadcast)/'=>Yii::t('app', 'Settings'),
            '^admin-(interest|param|param-value)/'=>Yii::t('app', 'Interests'),
            '^admin-(search-request|search-request-offer)'=>Yii::t('app', 'Suchanzeige'),
            '^admin-(offer|offer-request)'=>Yii::t('app', 'Angebote')
        ];
    }

    public function getModuleName() {
        foreach($this->getModulesMapping() as $regex=>$moduleName) {
            if (preg_match("%$regex%",$this->action)) return $moduleName;
        }
        return '';
    }

    public function attributeLabels()
    {
        return [
            'dt' => Yii::t('app','Datum'),
            'action' => Yii::t('app','Module'),
            'comment' => Yii::t('app','Kommentar'),

        ];
    }
}
