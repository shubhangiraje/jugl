<?php

namespace app\models;

use Yii;
use app\components\EDateTime;

class DailyStats extends \app\models\base\DailyStats {

    public function attributeLabels() {
        return [
            'dt' => Yii::t('app','Dt'),
            'packet_upgrades' => Yii::t('app','Upgrades gestern'),
        ];
    }

    public static function packetUpgrades() {
        $now=new EDateTime();
        $dt=$now->sqlDate();
        Yii::$app->db->createCommand("insert into daily_stats(dt,packet_upgrades) values(:dt,1) on duplicate key update packet_upgrades=packet_upgrades+1", [
            ':dt' => $dt
        ])->execute();
    }

    public static function getPacketUpgrades() {
        $now=new EDateTime();
        $dt=$now->modify('-1 day')->sqlDate();
        $model = static::find()->select('packet_upgrades')->where(['dt'=>$dt])->one();
        return intval($model->packet_upgrades);
    }

    public static function packetSelectVIP() {
        $now=new EDateTime();
        $dt=$now->sqlDate();
        Yii::$app->db->createCommand("insert into daily_stats(dt,packet_select_vip) values(:dt,1) on duplicate key update packet_select_vip=packet_select_vip+1", [
            ':dt' => $dt
        ])->execute();
    }

    public static function packetSelectSTANDARD() {
        $now=new EDateTime();
        $dt=$now->sqlDate();
        Yii::$app->db->createCommand("insert into daily_stats(dt,packet_select_standard) values(:dt,1) on duplicate key update packet_select_standard=packet_select_standard+1", [
            ':dt' => $dt
        ])->execute();
    }


}
