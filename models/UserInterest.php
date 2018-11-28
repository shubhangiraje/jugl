<?php

namespace app\models;

use Yii;

class UserInterest extends \app\models\base\UserInterest
{
    const TYPE_OFFER='OFFER';
    const TYPE_SEARCH_REQUEST='SEARCH_REQUEST';

    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app','User ID'),
            'level1_interest_id' => Yii::t('app','Level1 Interest ID'),
            'level2_interest_id' => Yii::t('app','Level2 Interest ID'),
            'level3_interest_id' => Yii::t('app','Level3 Interest ID'),
        ];
    }








}
