<?php

use yii\db\Schema;
use yii\db\Migration;

class m150526_102725_add_chat_user_ignore extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `chat_user_ignore` (
              `user_id` bigint(20) NOT NULL,
              `ignore_user_id` bigint(20) NOT NULL,
              PRIMARY KEY (`user_id`,`ignore_user_id`),
              KEY `ignore_user_id` (`ignore_user_id`),
              CONSTRAINT `chat_user_ignore_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `chat_user` (`user_id`),
              CONSTRAINT `chat_user_ignore_ibfk_2` FOREIGN KEY (`ignore_user_id`) REFERENCES `chat_user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m150526_102725_add_chat_user_ignore cannot be reverted.\n";

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
