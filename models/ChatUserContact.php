<?php

namespace app\models;

use Yii;

class ChatUserContact extends \app\models\base\ChatUserContact
{

    public static function add($userId,$secondUserId,$decision_needed=0) {
        $chatUserContact=self::findOne(['user_id'=>$userId,'second_user_id'=>$secondUserId]);
        if (!$chatUserContact) {
            $chatUserContact=new self;
            $chatUserContact->user_id=$userId;
            $chatUserContact->second_user_id=$secondUserId;
        }

        $chatUserContact->decision_needed=$decision_needed ? 1:0;
        $chatUserContact->save();
    }

    public function decisionSkip() {
        $trx=Yii::$app->db->beginTransaction();
        $this->decision_needed=0;
        $this->save();
        $trx->commit();
        \app\components\ChatServer::updateInitInfo([$this->user_id]);
    }

    public function decisionSpam() {
 //       $trx=Yii::$app->db->beginTransaction();


        /*
        $userSpamReport=new \app\models\UserSpamReport();
        $userSpamReport->user_id=$this->user_id;
        $userSpamReport->second_user_id=$this->second_user_id;
        $userSpamReport->object='Chat';
        $userSpamReport->comment='no comment';
        $userSpamReport->save();
        $userSpamReport->secondUser->spam_reports++;
        $userSpamReport->secondUser->save();

        */

        /*
        \app\models\User::findOne(['id'=>$this->user_id])->deleteFriend($this->second_user_id,false);

        $cui=ChatUserIgnore::find()->where(['user_id'=>$this->user_id,'ignore_user_id'=>$this->second_user_id])->one();
        if (!$cui) {
            $cui=new ChatUserIgnore();
            $cui->user_id=$this->user_id;
            $cui->ignore_user_id=$this->second_user_id;
            $cui->save();
        }
*/

 //       $trx->commit();

        \app\components\ChatServer::updateInitInfo([$this->user_id,$this->second_user_id]);
    }

    public function decisionAddToFriends() {
        $trx=Yii::$app->db->beginTransaction();

        $this->decision_needed=0;
        $this->save();

        \app\models\User::findOne(['id'=>$this->user_id])->addFriend($this->second_user_id,false);

        $trx->commit();

        \app\components\ChatServer::updateInitInfo([$this->user_id]);
    }

    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app','User ID'),
            'second_user_id' => Yii::t('app','Second User ID'),
            'decision_needed' => Yii::t('app','Decision Needed'),
        ];
    }
}
