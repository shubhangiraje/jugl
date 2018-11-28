<?php

use yii\db\Migration;

class m170523_075233_add_table_search_request_comment extends Migration
{
    public function up()
    {
        $this->execute("CREATE TABLE `search_request_comment` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `search_request_id` bigint(20) NOT NULL,
          `user_id` bigint(20) NOT NULL,
          `comment` varchar(4096) NOT NULL,
          `create_dt` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
          `response` varchar(4096) DEFAULT NULL,
          `response_dt` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `search_request_id` (`search_request_id`),
          KEY `user_id` (`user_id`),
          CONSTRAINT `search_request_comment_ibfk_1` FOREIGN KEY (`search_request_id`) REFERENCES `search_request` (`id`),
          CONSTRAINT `search_request_comment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    public function down()
    {
        echo "m170523_075233_add_table_search_request_comment cannot be reverted.\n";

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
