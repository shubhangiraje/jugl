<?php

use yii\db\Migration;

class m170523_064658_add_table_info_comment_vote extends Migration
{
    public function up()
    {
        $this->execute("CREATE TABLE `info_comment_vote` (
          `info_comment_id` bigint(20) NOT NULL,
          `user_id` bigint(20) NOT NULL,
          `vote` tinyint(1) NOT NULL,
          PRIMARY KEY (`info_comment_id`,`user_id`),
          KEY `user_id` (`user_id`),
          CONSTRAINT `info_comment_vote_ibfk_1` FOREIGN KEY (`info_comment_id`) REFERENCES `info_comment` (`id`),
          CONSTRAINT `info_comment_vote_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    public function down()
    {
        echo "m170523_064658_add_table_info_comment_vote cannot be reverted.\n";

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
