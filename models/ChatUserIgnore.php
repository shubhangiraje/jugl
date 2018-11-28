<?php

namespace app\models;

use Yii;

class ChatUserIgnore extends \app\models\base\ChatUserIgnore
{

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIgnoreUser()
    {
        return $this->hasOne('\app\models\User', ['id' => 'ignore_user_id']);
    }

    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app','User ID'),
            'ignore_user_id' => Yii::t('app','Ignore User ID'),
        ];
    }
}
