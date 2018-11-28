<?php

use yii\db\Schema;
use yii\db\Migration;

class m150915_075710_add_search_request_favorite_table extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `search_request_favorite` (
                `user_id` bigint(20) NOT NULL,
                `search_request_id` bigint(20) NOT NULL,
                  PRIMARY KEY (`user_id`,`search_request_id`),
                  KEY `search_request_id` (`search_request_id`),
                  CONSTRAINT `search_request_favorite_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
                  CONSTRAINT `search_request_favorite_ibfk_2` FOREIGN KEY (`search_request_id`) REFERENCES `search_request` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m150915_075710_add_search_request_favorite_table cannot be reverted.\n";

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
