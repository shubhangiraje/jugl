<?php

use yii\db\Schema;
use yii\db\Migration;

class m150611_103503_chat_contacts extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `chat_user_contact` (
              `user_id` bigint(20) NOT NULL,
              `second_user_id` bigint(20) NOT NULL,
              `decision_needed` tinyint NOT NULL,
              FOREIGN KEY (`user_id`) REFERENCES `chat_user` (`user_id`),
              FOREIGN KEY (`second_user_id`) REFERENCES `chat_user` (`user_id`)
            ) COMMENT='';
        ");

        $this->execute("
            insert into chat_user_contact(user_id,second_user_id,decision_needed)
            select uf.user_id,uf.friend_user_id,0
            from user_friend uf
            join chat_user cu1 on (cu1.user_id=uf.user_id)
            join chat_user cu2 on (cu2.user_id=uf.friend_user_id)
        ");

        $this->execute("
            ALTER TABLE `chat_user_contact`
            ADD PRIMARY KEY `user_id_second_user_id` (`user_id`, `second_user_id`),
            DROP INDEX `user_id`;
        ");

        $this->execute("
            CREATE TABLE `user_spam_report` (
              `user_id` bigint(20) NOT NULL,
              `second_user_id` bigint(20) NOT NULL,
              `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`user_id`,`second_user_id`),
              KEY `second_user_id` (`second_user_id`),
              CONSTRAINT `user_spam_report_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
              CONSTRAINT `user_spam_report_ibfk_2` FOREIGN KEY (`second_user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m150611_103503_chat_contacts cannot be reverted.\n";

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
