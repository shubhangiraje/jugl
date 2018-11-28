<?php

use yii\db\Schema;
use yii\db\Migration;

class m150701_133722_add_user_feedback_table extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `user_feedback` (
              `id` bigint NOT NULL,
              `user_id` bigint(20) NOT NULL,
              `second_user_id` bigint(20) NOT NULL,
              `feedback` varchar(4096) NOT NULL,
              `rating` tinyint NOT NULL,
              FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
              FOREIGN KEY (`second_user_id`) REFERENCES `user` (`id`)
            ) COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150701_133722_add_user_feedback_table cannot be reverted.\n";

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
