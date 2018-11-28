<?php

namespace app\models;

use Yii;

class TrollboxMessageVote extends \app\models\base\TrollboxMessageVote
{
    public function attributeLabels()
    {
        return [
            'trollbox_message_id' => Yii::t('app','Trollbox Message ID'),
            'user_id' => Yii::t('app','User ID'),
            'vote' => Yii::t('app','Vote'),
        ];
    }

    public function sendLikeEvent() {
        if ($this->vote>0) {
            \app\models\UserEvent::addLikeTrollboxMessageNotification($this);
        }
    }

}

\yii\base\Event::on(TrollboxMessageVote::className(), \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE, function ($event) {
    if ($event->sender->oldAttributes['vote']!=$event->sender->vote) {
        if ($event->sender->trollboxMessage->type!=\app\models\TrollboxMessage::TYPE_VIDEO_IDENTIFICATION) {
            $event->sender->sendLikeEvent();
        }
    }
});

\yii\base\Event::on(TrollboxMessageVote::className(), \yii\db\ActiveRecord::EVENT_AFTER_INSERT, function ($event) {
    if ($event->sender->trollboxMessage->type!=\app\models\TrollboxMessage::TYPE_VIDEO_IDENTIFICATION) {
        $event->sender->sendLikeEvent();
    }

    if ($event->sender->trollboxMessage->type==\app\models\TrollboxMessage::TYPE_VIDEO_IDENTIFICATION &&
        $event->sender->trollboxMessage->user->video_identification_status=\app\models\User::VIDEO_IDENTIFICATION_STATUS_AWAITING) {

        $acceptUser=false;
        $rejectUser=false;

        if ($event->sender->user_id==\app\models\User::SUPERADMIN_ID) {
            $acceptUser=$event->sender->vote==1;
            $rejectUser=$event->sender->vote==-1;
        } else {
            $score=intval(Yii::$app->db->createCommand("
              select sum(vote) from trollbox_message_vote 
              join user on (user.id=trollbox_message_vote.user_id and user.video_identification_score>=:VIDEOIDENT_MIN_SCORE_FOR_VOTING)
              where trollbox_message_vote.trollbox_message_id=:trollbox_message_id",[
                  ':VIDEOIDENT_MIN_SCORE_FOR_VOTING'=>\app\models\Setting::get('VIDEOIDENT_MIN_SCORE_FOR_VOTING'),
                  ':trollbox_message_id'=>$event->sender->trollbox_message_id
            ])->queryScalar());

            if ($score>=\app\models\Setting::get('VIDEOIDENT_AUTO_ACCEPT_SCORE')) {
                $acceptUser=true;
            }

            if ($score<=\app\models\Setting::get('VIDEOIDENT_AUTO_REJECT_SCORE')) {
                $rejectUser=true;
            }
        }

        if ($acceptUser || $rejectUser) {
            $status=\app\models\User::VIDEO_IDENTIFICATION_STATUS_REJECTED;
            if ($acceptUser) {
                $status=$event->sender->user_id==\app\models\User::SUPERADMIN_ID ? \app\models\User::VIDEO_IDENTIFICATION_STATUS_ACCEPTED_MANUAL:\app\models\User::VIDEO_IDENTIFICATION_STATUS_ACCEPTED_AUTO;
            }
            $event->sender->trollboxMessage->setVideoIdentStatus($status);
        }
    }
});
