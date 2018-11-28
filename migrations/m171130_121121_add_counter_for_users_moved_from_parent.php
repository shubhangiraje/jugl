<?php

use yii\db\Migration;

class m171130_121121_add_counter_for_users_moved_from_parent extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `user_moved_users_count` (
              `from_user_id` bigint(20) NOT NULL,
              `to_user_id` bigint(20) NOT NULL,
              `count` int(11) NOT NULL,
              PRIMARY KEY (`from_user_id`,`to_user_id`),
              KEY `to_user_id` (`to_user_id`),
              CONSTRAINT `user_moved_users_count_ibfk_1` FOREIGN KEY (`from_user_id`) REFERENCES `user` (`id`),
              CONSTRAINT `user_moved_users_count_ibfk_2` FOREIGN KEY (`to_user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m171130_121121_add_counter_for_users_moved_from_parent cannot be reverted.\n";

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
