<?php

namespace app\models;

use Yii;

class ChatUser extends \app\models\base\ChatUser
{
	public $country_id;
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app','User ID'),
            'online' => Yii::t('app','Online'),
            'new_messages' => Yii::t('app','New Messages'),
        ];
    }

    public function updateGroupChatTitle($text) {
        $this->group_chat_title=\yii\helpers\StringHelper::truncate($text,64);
    }

    public static function createGroupChat($text) {
        $trx=Yii::$app->db->beginTransaction();

        // get chat number
        $chatGroupNum=new ChatGroupNum();
        $chatGroupNum->save();
//        $chatGroupNum->delete();
/*
        // get user id
        $user=new User();
        $user->password=$user->access_token=$user->auth_key='DUMMY';
        $user->save();

        $trx->rollBack();

        $trx=Yii::$app->db->beginTransaction();
*/

        $chatUser=new \app\models\ChatUser();
        $chatUser->user_id=-$chatGroupNum->id;
        $chatUser->is_group_chat=1;
        $chatUser->updateGroupChatTitle($text);
        //$chatUser->group_chat_title=Yii::t('app','Gruppenchat {num}',['num'=>$chatGroupNum->id]);
        $chatUser->save();

        $trx->commit();

        return $chatUser->user_id;
    }

    public function joinUserToGroupChat($user) {
        $trx=Yii::$app->db->beginTransaction();

        //try {
            $chatUserContact=new \app\models\ChatUserContact();
            $chatUserContact->user_id=$user->id;
            $chatUserContact->second_user_id=$this->user_id;
            $chatUserContact->decision_needed=0;
            $chatUserContact->save();
        //} catch (\Exception $e) {
        //    $trx->rollBack();
        //    return;
        //}

        if ($user->is_blocked_in_trollbox) {
            $cui=\app\models\ChatUserIgnore::findOne(['user_id'=>$this->user_id,'ignore_user_id'=>$user->id]);
            if (!$cui) {
                $cui=new \app\models\ChatUserIgnore();
                $cui->user_id=$this->user_id;
                $cui->ignore_user_id=$user->id;
                $cui->moderator_user_id=$user->is_blocked_in_trollbox_moderator_user_id;
                $cui->save();
            }
        }

        // delete old chat messages
        Yii::$app->db->createCommand("update chat_message set deleted=1 where user_id=:user_id and second_user_id=:chat_user_id",[
            ":user_id"=>$user->id,
            ":chat_user_id"=>$this->user_id
        ])->execute();

        // copy chat messages
        Yii::$app->db->createCommand("
            insert into chat_message(dt,user_id,second_user_id,sender_user_id,outgoing_chat_message_id,`type`,content_type,text,extra,deleted)
            select dt,:user_id,second_user_id,sender_user_id,outgoing_chat_message_id,:type_incoming_readed,content_type,text,extra,deleted
            from chat_message
            where user_id=:chat_user_id
            order by id asc
        ",[
            ":user_id"=>$user->id,
            ":chat_user_id"=>$this->user_id,
            ":type_incoming_readed"=>'INCOMING_UNDELIVERED'
        ])->execute();

        // copy chat_files
        Yii::$app->db->createCommand("
            insert into chat_file (dt,user_id,chat_message_id,link,`size`,`name`,ext)
            select chat_file.dt,chat_file.user_id,chat_message.id,chat_file.link,chat_file.`size`,chat_file.`name`,chat_file.ext
            from chat_message
            join chat_file on (chat_file.chat_message_id=chat_message.outgoing_chat_message_id)
            where chat_message.user_id=:user_id and chat_message.second_user_id=:chat_user_id
        ",[
            ":user_id"=>$user->id,
            ":chat_user_id"=>$this->user_id,
        ])->execute();

        // get last chat message_id
        $lastMessageId=Yii::$app->db->createCommand("select id from chat_message where user_id=:user_id and second_user_id=:chat_user_id order by id desc limit 1",[
            ":user_id"=>$user->id,
            ":chat_user_id"=>$this->user_id,
        ])->queryScalar();


        if ($lastMessageId) {
            // insert/update chat_conversation
            Yii::$app->db->createCommand("insert into chat_conversation(user_id,second_user_id,last_chat_message_id) values (:user_id,:chat_user_id,:last_chat_message_id) on duplicate key update last_chat_message_id=:last_chat_message_id",[
                ":user_id"=>$user->id,
                ":chat_user_id"=>$this->user_id,
                ":last_chat_message_id"=>$lastMessageId
            ])->execute();
        }

        $trx->commit();
        Yii::$app->response->on(\yii\web\Response::EVENT_AFTER_SEND, [$user,'updateInitInfo']);
    }
}
