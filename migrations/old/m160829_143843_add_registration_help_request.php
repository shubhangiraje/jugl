<?php

use yii\db\Schema;
use yii\db\Migration;

class m160829_143843_add_registration_help_request extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `registration_help_request` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `dt` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
              `ip` varchar(20) DEFAULT NULL,
              `first_name` varchar(64) DEFAULT NULL,
              `last_name` varchar(64) DEFAULT NULL,
              `nick_name` varchar(64) DEFAULT NULL,
              `company_name` int(11) DEFAULT NULL,
              `birthday` date DEFAULT NULL,
              `email` varchar(128) DEFAULT NULL,
              `phone` varchar(64) DEFAULT NULL,
              `sex` enum('','M','F') NOT NULL DEFAULT '',
              `step` int(11) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
                ALTER TABLE `registration_help_request`
                ADD `user_id` bigint(20) NULL,
                ADD FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
        ");

        $this->execute("
                ALTER TABLE `registration_help_request`
                CHANGE `company_name` `company_name` varchar(64) NULL AFTER `nick_name`;
        ");
    }

    public function down()
    {
        echo "m160829_143843_add_registration_help_request cannot be reverted.\n";

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
