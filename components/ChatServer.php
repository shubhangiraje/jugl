<?php

namespace app\components;

use Yii;


class ChatServer {
    static $requests=0;
    static $useDeferring=true;
    static $cachedRpcCalls=[];
    static $cachedStatusUpdateWithSound=[];
    static $cachedStatusUpdateWithoutSound=[];

    public static function sendTextMessage($fromUserId,$toUserId,$text) {
        static::rpcCall('/message/send',[
            'user_id'=> $fromUserId,
            'message'=> [
                'user_id'=>$toUserId,
                'content_type'=>'TEXT',
                'text'=>$text
            ]
        ]);
    }

    public static function sendSystemMessage($fromUserId,$toUserId,$text) {
        static::rpcCall('/systemMessage/send',[
            'user_id'=> $fromUserId,
            'message'=> [
                'user_id'=>$toUserId,
                'content_type'=>'TEXT',
                'text'=>$text
            ]
        ]);
    }

    public static function broadcast($message) {
        static::rpcCall('/broadcast',$message);
    }

    public static function updateInitInfo($userIds) {
        static::rpcCall('/initInfo/update',[
            'user_ids'=> $userIds,
        ]);
    }

    public static function statusUpdate($userIds,$options=null) {
        if (!is_array($userIds)) {
            $userIds=[$userIds];
        }

        static::rpcCall('/status/update',[
            'user_ids'=>$userIds,
            'options'=>$options
        ]);
    }

    public static function newEvent($event,$userIds=null) {
        if ($userIds===null) {
            $userIds=[$event->user_id];
        }

        static::statusUpdate($userIds);
        static::rpcCall('/event/new',[
            'user_ids'=> $userIds,
            'type'=>$event->type,
            'title'=> $event->pushTitle,
            'text'=> $event->pushText
        ]);

    }

    public static function pushMessage($msg)
    {
        static::rpcCall('/pushMessage/send',$msg);
    }

    public static function pushMessageExt($msg)
    {
        static::rpcCall('/pushMessage/sendExt',$msg);
    }

    public static function newMoneyIncoming($balanceLog) {
        if ($balanceLog->sum<=0.01) {
            static::statusUpdate($balanceLog->user_id);
            return;
        }

        static::statusUpdate($balanceLog->user_id,['sound'=>'coins.mp3']);

        if ($balanceLog->sum<=0.01) return false;

        if (\app\models\UserDevice::findOne(['user_id'=>$balanceLog->user_id,'setting_notification_all'=>1,'setting_notification_money'=>1])) {
            static::rpcCall('/pushMessage/send',[
                'user_id'=> $balanceLog->user_id,
                'link'=>'view-funds.html',
                'title'=> $balanceLog->initiatorUser->first_name.' '.$balanceLog->initiatorUser->last_name,
                'text'=> Yii::t('app','Deinem Jugl-Punktekonto wurden {sum} Jugls gutgeschrieben',['sum'=>floor($balanceLog->sum*100)/100]),
                'sound'=> 'coins.wav',
                'type'=>'money'
            ]);
        }
    }

    public static function newMoneyTokenIncoming($balanceLog) {
        if ($balanceLog->sum<=0.01) {
            static::statusUpdate($balanceLog->user_id);
            return;
        }

        static::statusUpdate($balanceLog->user_id,['sound'=>'coins.mp3']);

        if ($balanceLog->sum<=0.01) return false;

        if (\app\models\UserDevice::findOne(['user_id'=>$balanceLog->user_id,'setting_notification_all'=>1,'setting_notification_money'=>1])) {
            static::rpcCall('/pushMessage/send',[
                'user_id'=> $balanceLog->user_id,
                'link'=>'view-funds-token.html',
                'title'=> $balanceLog->initiatorUser->first_name.' '.$balanceLog->initiatorUser->last_name,
                'text'=> Yii::t('app','Deinem Jugl-Tokenkonto wurden {sum} Tokens gutgeschrieben',['sum'=>floor($balanceLog->sum*100)/100]),
                'sound'=> 'coins.wav',
                'type'=>'money'
            ]);
        }
    }

    public static function openConversation($userId) {
        return static::rpcCall('/user/info',[
            'user_id'=> $userId,
        ]);
    }

    private static function rpcCallInternal($url,$data) {
        $ch=curl_init();
        if (Yii::$app->db->getTransaction() || static::$useDeferring) {
            $st=microtime(true);
            \app\components\SLogger::log("START ".(++static::$requests)." $url\t (".Yii::$app->controller->route.') '.json_encode($data,JSON_UNESCAPED_UNICODE));
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_URL, Yii::$app->params['chat']['rpcUrl'].$url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, \yii\helpers\Json::encode($data));
        $out = curl_exec($ch);

        try {
            $out=\yii\helpers\Json::decode($out);
        } catch (\Exception $e) {
            $out=null;
        }

        if (Yii::$app->db->getTransaction() || static::$useDeferring) {
            $et=microtime(true);
            \app\components\SLogger::log('FINISH '.number_format($et-$st,3).' '.number_format($et-$GLOBALS['startRequestTime'],3));
        }

        return $out;
    }

    private static function rpcCall($url,$data) {
        if (static::$useDeferring && Yii::$app->db->getTransaction() &&
            !preg_match('%^(/user/info)%',$url)) {

            //SLogger::log("defer call $url ".json_encode($data));
            if ($url=='/status/update') {
                foreach($data['user_ids'] as $user_id) {
                    if (!$data['options']) {
                        if (!static::$cachedStatusUpdateWithSound[$user_id]) {
                            static::$cachedStatusUpdateWithoutSound[$user_id]=true;
                        }
                    } else {
                        static::$cachedStatusUpdateWithSound[$user_id]=true;
                        unset(static::$cachedStatusUpdateWithoutSound[$user_id]);
                    }
                }
                return;
            }

            static::$cachedRpcCalls[]=['url'=>$url,'data'=>$data];
            return;
        }

        return static::rpcCallInternal($url,$data);
    }

    public static function afterCommit() {
        //SLogger::log('DO DEFERRED CALLS');

        if (!empty(static::$cachedStatusUpdateWithSound)) {
            static::rpcCallInternal('/status/update',[
                'user_ids'=>array_keys(static::$cachedStatusUpdateWithSound),
                'options'=>['sound'=>'coins.mp3']
            ]);
        }

        if (!empty($cachedStatusUpdateWithoutSound)) {
            static::rpcCallInternal('/status/update',[
                'user_ids'=>array_keys(static::$cachedStatusUpdateWithoutSound),
                'options'=>null
            ]);
        }

        foreach(static::$cachedRpcCalls as $call) {
            static::rpcCallInternal($call['url'],$call['data']);
        }

        static::$cachedRpcCalls=[];
        static::$cachedStatusUpdateWithSound=[];
        static::$cachedStatusUpdateWithoutSound=[];
    }

    public static function afterRollback() {
        static::$cachedRpcCalls=[];
        static::$cachedStatusUpdateWithSound=[];
        static::$cachedStatusUpdateWithoutSound=[];
    }
}


\yii\base\Event::on(\yii\db\Connection::className(), \yii\db\Connection::EVENT_COMMIT_TRANSACTION, function ($event) {
    ChatServer::afterCommit();
});

\yii\base\Event::on(\yii\db\Connection::className(), \yii\db\Connection::EVENT_ROLLBACK_TRANSACTION, function ($event) {
    ChatServer::afterRollback();
});

