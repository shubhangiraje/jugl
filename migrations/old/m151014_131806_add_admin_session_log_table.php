<?php

use yii\db\Schema;
use yii\db\Migration;

class m151014_131806_add_admin_session_log_table extends Migration
{
    public function up()
    {
        $this->execute("
              CREATE TABLE `admin_session_log` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `admin_id` bigint(20) NOT NULL,
              `session` varchar(64) NOT NULL,
              `dt_start` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              `dt_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              `ip` varchar(64) DEFAULT NULL,
              `user_agent` varchar(256) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `admin_id` (`admin_id`),
              KEY `dt_start` (`dt_start`),
              KEY `dt_end` (`dt_end`),
              CONSTRAINT `admin_session_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m151014_131806_add_admin_session_log_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
