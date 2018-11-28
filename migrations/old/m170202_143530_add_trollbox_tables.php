<?php

use yii\db\Migration;

class m170202_143530_add_trollbox_tables extends Migration
{
    public function up()
    {
        $this->execute("
CREATE TABLE `trollbox_message` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `dt` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
  `text` varchar(1024) NOT NULL,
  `file_id` bigint(20) DEFAULT NULL,
  `group_chat_user_id` bigint(20) DEFAULT NULL,
  `votes_up` int(11) NOT NULL DEFAULT '0',
  `votes_down` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `file_id` (`file_id`),
  KEY `group_chat_user_id` (`group_chat_user_id`),
  CONSTRAINT `trollbox_message_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `trollbox_message_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`),
  CONSTRAINT `trollbox_message_ibfk_3` FOREIGN KEY (`group_chat_user_id`) REFERENCES `chat_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
CREATE TABLE `trollbox_message_vote` (
  `trollbox_message_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `vote` tinyint(1) NOT NULL,
  PRIMARY KEY (`trollbox_message_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `trollbox_message_vote_ibfk_1` FOREIGN KEY (`trollbox_message_id`) REFERENCES `trollbox_message` (`id`),
  CONSTRAINT `trollbox_message_vote_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
CREATE TABLE `chat_group_num` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m170202_143530_add_trollbox_tables cannot be reverted.\n";

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
