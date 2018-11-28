<?php

use yii\db\Schema;
use yii\db\Migration;

class m150302_070632_ext_api_mods extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `user_device` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user_id` bigint(20) NOT NULL,
              `type` enum('ANDROID','IOS','WP') NOT NULL,
              `device_uuid` varchar(128) NOT NULL,
              `push_token` varchar(256) DEFAULT NULL,
              `key` varchar(128) DEFAULT NULL,
              `description` varchar(256) DEFAULT NULL,
              `last_seen` date DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `type_device_id` (`type`,`device_uuid`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `user_device_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            ALTER TABLE `chat_user`
            ADD `online_mobile` tinyint(1) NOT NULL DEFAULT '0',
            ADD `mobile_last_seen` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `online_mobile`;
        ");

        $this->execute("
            ALTER TABLE `chat_user`
            ADD INDEX `online_mobile_mobile_last_seen` (`online_mobile`, `mobile_last_seen`);
        ");
    }

    public function down()
    {
        echo "m150302_070632_ext_api_mods cannot be reverted.\n";

        return false;
    }
}
