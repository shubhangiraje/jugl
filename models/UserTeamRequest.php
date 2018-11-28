<?php

namespace app\models;

use Yii;

class UserTeamRequest extends \app\models\base\UserTeamRequest
{
    const TYPE_PARENT_TO_REFERRAL='PARENT_TO_REFERRAL';
    const TYPE_REFERRAL_TO_PARENT='REFERRAL_TO_PARENT';

    public function scenarios()
    {
        return array_merge(parent::scenarios(),
            ['saveReferralToParent'=>['text','second_user_id']],
            ['saveParentToReferral'=>['text','second_user_id']]
        );
    }

    public function attributeLabels()
    {
        return [
            'text' => Yii::t('app','Texte'),
        ];
    }
}
