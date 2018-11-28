<?php

use yii\db\Migration;

class m171103_110245_add_follower_tables extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `user_follower` (
              `user_id` bigint(20) NOT NULL,
              `follower_user_id` bigint(20) NOT NULL,
              PRIMARY KEY (`user_id`,`follower_user_id`),
              KEY `follower_user_id` (`follower_user_id`),
              CONSTRAINT `user_follower_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
              CONSTRAINT `user_follower_ibfk_2` FOREIGN KEY (`follower_user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            CREATE TABLE `user_follower_event` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `user_id` bigint(20) NOT NULL,
              `follower_user_id` bigint(20) NOT NULL,
              `dt` timestamp NULL DEFAULT NULL,
              `text` int(11) NOT NULL,
              `type` enum('NEW_OFFER','NEW_SEARCH_REQUEST','OFFER_BUY','OFFER_BET','NEW_SEARCH_REQUEST_OFFER','NEW_REFERRAL','NEW_TROLLBOX_MESSAGE','NEW_TROLLBOX_MESSAGE_COMMENT','NEW_INFO_COMMENT') NOT NULL,
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`),
              KEY `follower_user_id_dt` (`follower_user_id`,`dt`),
              CONSTRAINT `user_follower_event_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
              CONSTRAINT `user_follower_event_ibfk_2` FOREIGN KEY (`follower_user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            ALTER TABLE `user_follower_event`
            CHANGE `text` `text` mediumtext NULL AFTER `dt`;
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD `new_follower_events` int(11) NOT NULL DEFAULT '0' AFTER `new_events`;
        ");
    }

    public function down()
    {
        echo "m171103_110245_add_follower_tables cannot be reverted.\n";

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
