<?php

namespace app\models;

use Yii;

class GroupChatModeratorLastVisit extends \app\models\base\GroupChatModeratorLastVisit
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'group_chat_id' => Yii::t('app','Group Chat ID'),
            'moderator_user_id' => Yii::t('app','Moderator User ID'),
            'dt' => Yii::t('app','Dt'),
        ];
    }
}
