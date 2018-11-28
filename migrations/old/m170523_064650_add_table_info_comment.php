<?php

use yii\db\Migration;

class m170523_064650_add_table_info_comment extends Migration
{
    public function up()
    {
        $this->execute("CREATE TABLE `info_comment` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `user_id` bigint(20) NOT NULL,
          `info_id` int(11) NOT NULL,
          `dt` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
          `comment` mediumtext NOT NULL,
          `file_id` bigint(20) DEFAULT NULL,
          `votes_up` int(11) NOT NULL DEFAULT '0',
          `votes_down` int(11) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          KEY `info_id` (`info_id`),
          KEY `file_id` (`file_id`),
          CONSTRAINT `info_comment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
          CONSTRAINT `info_comment_ibfk_2` FOREIGN KEY (`info_id`) REFERENCES `info` (`id`),
          CONSTRAINT `info_comment_ibfk_3` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    public function down()
    {
        echo "m170523_064650_add_table_info_comment cannot be reverted.\n";

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
