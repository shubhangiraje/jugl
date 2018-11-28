<?php

namespace app\models;

use Yii;

class InfoCommentVote extends \app\models\base\InfoCommentVote
{
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'info_comment_id' => Yii::t('app','Info Comment ID'),
            'user_id' => Yii::t('app','User ID'),
            'vote' => Yii::t('app','Vote'),
        ];
    }

    public function sendLikeEvent() {
        if ($this->vote>0) {
            \app\models\UserEvent::addLikeInfoCommentNotification($this);
        }
    }
}

\yii\base\Event::on(InfoCommentVote::className(), \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE, function ($event) {
    if ($event->sender->oldAttributes['vote']!=$event->sender->vote) {
        $event->sender->sendLikeEvent();
    }
});

\yii\base\Event::on(InfoCommentVote::className(), \yii\db\ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
    $event->sender->sendLikeEvent();
});
