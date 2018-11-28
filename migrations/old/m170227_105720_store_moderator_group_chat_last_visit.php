<?php

use yii\db\Migration;

class m170227_105720_store_moderator_group_chat_last_visit extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `group_chat_moderator_last_visit` (
              `group_chat_id` bigint(20) NOT NULL,
              `moderator_user_id` bigint(20) NOT NULL,
              `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`group_chat_id`,`moderator_user_id`),
              KEY `moderator_user_id` (`moderator_user_id`),
              CONSTRAINT `group_chat_moderator_last_visit_ibfk_1` FOREIGN KEY (`group_chat_id`) REFERENCES `chat_user` (`user_id`),
              CONSTRAINT `group_chat_moderator_last_visit_ibfk_2` FOREIGN KEY (`moderator_user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m170227_105720_store_moderator_group_chat_last_visit cannot be reverted.\n";

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
