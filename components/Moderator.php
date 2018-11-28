<?php

namespace app\components;

use app\models\InfoComment;
use app\models\TrollboxMessage;
use Yii;


class Moderator {

    public static function fixGroupChatConversations($groupChatId) {
        Yii::$app->db->createCommand("
            update chat_conversation
            set last_chat_message_id=(select id from chat_message where user_id=chat_conversation.user_id and second_user_id=chat_conversation.second_user_id and deleted=0 order by id desc limit 1)
            where second_user_id=:group_chat_id or user_id=:group_chat_id
        ",[
            ':group_chat_id'=>$groupChatId,
        ])->execute();

        Yii::$app->db->createCommand("delete from chat_conversation where last_chat_message_id is null")->execute();
    }

    public static function updateInitInfo($groupChatId) {
        $userIds=Yii::$app->db->createCommand("select second_user_id from chat_user_contact where user_id=:user_id",[
            ':user_id'=>$groupChatId
        ])->queryColumn();

        \app\components\ChatServer::updateInitInfo($userIds);
    }

    public static function deleteMessage($id) {
        if (!Yii::$app->user->identity->is_moderator) {
            return ['result'=>Yii::t('app','You are not moderator')];
        }

        $trx=Yii::$app->db->beginTransaction();

        $message=\app\models\ChatMessage::findOne($id);

        if (!$message || !$message->secondUser->is_group_chat) {
            return ['result'=>Yii::t('app','Cant delete this message')];
        }

        $groupChatId=$message->second_user_id;

        Yii::$app->db->createCommand('update chat_message set deleted=1 where id=:id or outgoing_chat_message_id=:id',[
            ':id'=>$message->outgoing_chat_message_id ? $message->outgoing_chat_message_id:$message->id
        ])->execute();

        Yii::$app->db->createCommand('update chat_user set group_chat_messages_count=(select count(*) from chat_message where user_id=:id and second_user_id=:id and deleted=0) where user_id=:id',[
            ':id'=>$groupChatId
        ])->execute();

        static::fixGroupChatConversations($groupChatId);

        $trx->commit();

        static::updateInitInfo($groupChatId);

        return ['result'=>true];
    }

    public static function unblockUserInTrollbox($user,$internal=false) {
        if ($internal) {
            Yii::$app->db->createCommand("delete from chat_user_ignore where user_id<0 and ignore_user_id=:user_id",[':user_id'=>$user->id])->execute();
        } else  {
            $user->is_blocked_in_trollbox=0;
            $user->save();
        }
    }

    public static function blockUserInTrollbox($groupChatId,$userId) {
        if (!Yii::$app->user->identity->is_moderator) {
            return ['result'=>Yii::t('app','You are not moderator')];
        }

        $trx=Yii::$app->db->beginTransaction();

        $groupChatIds=[];
        foreach(\app\models\ChatUserContact::find()->where('user_id=:user_id and second_user_id<0',[':user_id'=>$userId])->all() as $chatUserContact) {
            $cui=\app\models\ChatUserIgnore::findOne(['user_id'=>$chatUserContact->second_user_id,'ignore_user_id'=>$userId]);
            if (!$cui) {
                Yii::$app->db->createCommand('update chat_message set deleted=2 where second_user_id=:group_chat_id and sender_user_id=:user_id and deleted=0',[
                    ':group_chat_id'=>$chatUserContact->second_user_id,
                    ':user_id'=>$userId
                ])->execute();

                Yii::$app->db->createCommand('update chat_user set group_chat_messages_count=(select count(*) from chat_message where user_id=:id and second_user_id=:id and deleted=0) where user_id=:id',[
                    ':id'=>$groupChatId
                ])->execute();

                $cui=new \app\models\ChatUserIgnore();
                $cui->user_id=$chatUserContact->second_user_id;
                $cui->ignore_user_id=$userId;
                $cui->moderator_user_id=Yii::$app->user->id;
                $cui->dt=(new \app\components\EDateTime())->sql();
                $cui->save();

                static::fixGroupChatConversations($chatUserContact->second_user_id);
                $groupChatIds[]=$chatUserContact->second_user_id;
            }
        }

        $user=\app\models\User::findOne($userId);
        $user->is_blocked_in_trollbox=1;
        $user->is_blocked_in_trollbox_moderator_user_id=Yii::$app->user->id;
        $user->save();

        $trx->commit();

        foreach($groupChatIds as $groupChatId) {
            static::updateInitInfo($groupChatId);
        }

        return ['result'=>true];
    }

    public static function blockUser($groupChatId,$userId) {
        if (!Yii::$app->user->identity->is_moderator) {
            return ['result'=>Yii::t('app','You are not moderator')];
        }

        $trx=Yii::$app->db->beginTransaction();

        Yii::$app->db->createCommand('update chat_message set deleted=2 where second_user_id=:group_chat_id and sender_user_id=:user_id and deleted=0',[
            ':group_chat_id'=>$groupChatId,
            ':user_id'=>$userId
        ])->execute();

        Yii::$app->db->createCommand('update chat_user set group_chat_messages_count=(select count(*) from chat_message where user_id=:id and second_user_id=:id and deleted=0) where user_id=:id',[
            ':id'=>$groupChatId
        ])->execute();

        $cui=new \app\models\ChatUserIgnore();
        $cui->user_id=$groupChatId;
        $cui->ignore_user_id=$userId;
        $cui->moderator_user_id=Yii::$app->user->id;
        $cui->dt=(new \app\components\EDateTime())->sql();
        $cui->save();

        static::fixGroupChatConversations($groupChatId);

        $trx->commit();

        static::updateInitInfo($groupChatId);

        return ['result'=>true];
    }

    public static function unblockUser($groupChatId,$userId) {
        if (!Yii::$app->user->identity->is_moderator) {
            return ['result'=>Yii::t('app','You are not moderator')];
        }

        $trx=Yii::$app->db->beginTransaction();

        \app\models\ChatUserIgnore::deleteAll(['user_id'=>$groupChatId,'ignore_user_id'=>$userId]);

        $trx->commit();

        return ['result'=>true];
    }

    private static function setStickyFlag($id,$sticky)
    {
        if (!Yii::$app->user->identity->is_moderator) {
            return ['result'=>Yii::t('app','You are not moderator')];
        }

        $trx=Yii::$app->db->beginTransaction();

        $trollboxMessage=TrollboxMessage::findOne($id);

        if (!$trollboxMessage) {
            return ['result'=>Yii::t('app','Not Found')];
        }

        $trollboxMessage->is_sticky=$sticky ? 1:0;
        $trollboxMessage->save();

        $trx->commit();

        return ['result'=>true,'trollboxMessage'=>$trollboxMessage->getFrontInfo()];
    }

    private static function setTrollboxMessageStatus($id,$status) {
        if (!Yii::$app->user->identity->is_moderator) {
            return ['result'=>Yii::t('app','You are not moderator')];
        }

        $trx=Yii::$app->db->beginTransaction();

        $trollboxMessage=TrollboxMessage::findOne($id);

        if (!$trollboxMessage) {
            return ['result'=>Yii::t('app','Not Found')];
        }


        $trollboxMessage->status=$status;
        $trollboxMessage->status_changed_dt=(new \app\components\EDateTime())->sql();
        $trollboxMessage->status_changed_user_id=Yii::$app->user->id;
        $trollboxMessage->save();

        if($status == TrollboxMessage::STATUS_REJECTED) {
            \app\models\UserEvent::addSystemMessage($trollboxMessage->user_id,Yii::t('app','Dein Beitrag wurde blockiert. Bitte halte Dich an unsere Forumsregeln.'));
        }

        $tmsh=new \app\models\TrollboxMessageStatusHistory();
        $tmsh->trollbox_message_id=$trollboxMessage->id;
        $tmsh->status=$trollboxMessage->status;
        $tmsh->dt=$trollboxMessage->status_changed_dt;
        $tmsh->user_id=$trollboxMessage->status_changed_user_id;
        $tmsh->save();


        $chatUser=$trollboxMessage->groupChatUser;

        if ($chatUser && $trollboxMessage->status==TrollboxMessage::STATUS_REJECTED) {
            $params=[
                ':chat_user_id'=>$chatUser->user_id
            ];

            Yii::$app->db->createCommand('delete from chat_conversation where (user_id!=:chat_user_id and second_user_id=:chat_user_id)',$params)->execute();
            Yii::$app->db->createCommand('update chat_message set outgoing_chat_message_id=null where (second_user_id=:chat_user_id)',$params)->execute();
            Yii::$app->db->createCommand('update chat_message,chat_file set chat_message_id=null where (chat_message_id=chat_message.id) and (chat_message.user_id!=:chat_user_id and second_user_id=:chat_user_id)',$params)->execute();
            Yii::$app->db->createCommand('delete from chat_message where (user_id!=:chat_user_id and second_user_id=:chat_user_id)',$params)->execute();

            $userIds=Yii::$app->db->createCommand("select second_user_id from chat_user_contact where user_id=:user_id",[
                ':user_id'=>$chatUser->user_id
            ])->queryColumn();

            Yii::$app->db->createCommand('delete from chat_user_contact where (user_id=:chat_user_id or second_user_id=:chat_user_id)',$params)->execute();

            $trx->commit();

            \app\components\ChatServer::updateInitInfo($userIds);

            foreach($userIds as $userId) {
                \app\models\UserEvent::addSystemMessage($userId,Yii::t('app','Moderator {user} hat den Gruppenchat "{title}" geblockt',[
                    'user'=>Yii::$app->user->identity->name,
                    'title'=>$chatUser->group_chat_title
                ]));
            }

        } else {
            $trx->commit();
        }

        return ['result'=>true,'trollboxMessage'=>$trollboxMessage->getFrontInfo()];
    }

    public static function acceptTrollboxMessage($id) {
        return static::setTrollboxMessageStatus($id,TrollboxMessage::STATUS_ACTIVE);
    }

    public static function rejectTrollboxMessage($id) {
        return static::setTrollboxMessageStatus($id,TrollboxMessage::STATUS_REJECTED);
    }

    public static function setStickyTrollboxMessage($id) {
        return static::setStickyFlag($id,true);
    }

    public static function unsetStickyTrollboxMessage($id) {
        return static::setStickyFlag($id,false);
    }


    private static function setInfoCommentStatus($id,$status) {

        if (!Yii::$app->user->identity->is_moderator) {
            return ['result'=>Yii::t('app','You are not moderator')];
        }

        $trx=Yii::$app->db->beginTransaction();

        $infoComment=InfoComment::findOne($id);

        if (!$infoComment) {
            return ['result'=>Yii::t('app','Not Found')];
        }

        $infoComment->status = $status;
        $infoComment->status_changed_dt=(new \app\components\EDateTime())->sql();
        $infoComment->status_changed_user_id=Yii::$app->user->id;
        $infoComment->save();

        if($status == InfoComment::STATUS_REJECTED) {
            \app\models\UserEvent::addSystemMessage($infoComment->user_id,Yii::t('app','Dein Beitrag wurde blockiert. Bitte halte Dich an unsere Wikiregeln.'));
        }

        $trx->commit();

        return ['result'=>true,'infoComment'=>$infoComment->getFrontInfo()];
    }

    public static function acceptInfoComment($id) {
        return static::setInfoCommentStatus($id, InfoComment::STATUS_ACTIVE);
    }

    public static function rejectInfoComment($id) {
        return static::setInfoCommentStatus($id, InfoComment::STATUS_REJECTED);
    }

}