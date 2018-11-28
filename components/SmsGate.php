<?php

namespace app\components;

use yii\base\Component;


class SmsGate extends Component {
    public $key;
    public $route;
    public $from;
    public $debug;

    public function normalizePhone($number) {
        $number=str_replace(' ','',$number);
        $number=preg_replace("/^\\+/",'',$number);
        $number=preg_replace('%^0+%','49',$number);
        return $number;
    }

    public function send($number,$message) {
        $number=$this->normalizePhone($number);

        $params = [
            'recipients' => $number,
            'body' => $message,
            'originator' => $this->from,
            'datacoding' => 'unicode',
        ];

        if ($number=='') return true;

        \Yii::info("send SMS to phone '$number' with message '$message'","sms");

        $ch = curl_init(); //initialize curl handle
        curl_setopt($ch, CURLOPT_URL, 'https://rest.messagebird.com/messages');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: AccessKey '.$this->key,
        ]);

        $response = curl_exec($ch);
        $code= curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code>=200 || $code<300) {
            return true;
        }

        return \Yii::t('app','SMS sending error: ').$code;
    }
}