<?php

use yii\db\Schema;
use yii\db\Migration;

class m150608_133147_user_events_modifications extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            CHANGE `new_activities` `new_events` int(11) NOT NULL DEFAULT '0' AFTER `new_network_members`,
            COMMENT='';
        ");

        $this->execute("
            CREATE TABLE `user_event` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `user_id` bigint(20) NOT NULL,
              `dt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              `type` enum('FRIEND_REQUEST','FRIEND_REQUEST_ACCEPTED','REGISTERED_BY_INVITATION','NEW_NETWORK_MEMBER') NOT NULL,
              `second_user_id` bigint(20) DEFAULT NULL,
              `text` mediumtext,
              PRIMARY KEY (`id`),
              KEY `second_user_id` (`second_user_id`),
              KEY `user_id_dt_type` (`user_id`,`dt`,`type`),
              CONSTRAINT `user_event_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
              CONSTRAINT `user_event_ibfk_2` FOREIGN KEY (`second_user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m150608_133147_user_events_modifications cannot be reverted.\n";

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
