<?php

namespace app\models;

use Yii;

class UserMovedUsersCount extends \app\models\base\UserMovedUsersCount
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'from_user_id' => Yii::t('app','From User ID'),
            'to_user_id' => Yii::t('app','To User ID'),
            'count' => Yii::t('app','Count'),
        ];
    }
}
