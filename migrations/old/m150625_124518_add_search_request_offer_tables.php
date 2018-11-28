<?php

use yii\db\Schema;
use yii\db\Migration;

class m150625_124518_add_search_request_offer_tables extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `search_request_offer` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `search_request_id` bigint(20) NOT NULL,
              `user_id` int(11) NOT NULL,
              `description` mediumtext NOT NULL,
              `price_from` decimal(14,2) NOT NULL,
              `price_to` decimal(14,2) NOT NULL,
              `relevancy` tinyint(4) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `search_request_id` (`search_request_id`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `search_request_offer_ibfk_1` FOREIGN KEY (`search_request_id`) REFERENCES `search_request` (`id`),
              CONSTRAINT `search_request_offer_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user_device` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            CREATE TABLE `search_request_offer_file` (
              `search_request_offer_id` bigint(20) NOT NULL,
              `file_id` bigint(20) NOT NULL,
              `sort_order` int(11) NOT NULL,
              PRIMARY KEY (`search_request_offer_id`,`file_id`),
              KEY `file_id` (`file_id`),
              CONSTRAINT `search_request_offer_file_ibfk_1` FOREIGN KEY (`search_request_offer_id`) REFERENCES `search_request_offer` (`id`),
              CONSTRAINT `search_request_offer_file_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            CREATE TABLE `search_request_offer_param_value` (
              `search_request_offer_id` bigint(20) NOT NULL,
              `param_id` int(11) NOT NULL,
              `match` tinyint(1) NOT NULL,
              PRIMARY KEY (`search_request_offer_id`,`param_id`),
              KEY `param_id` (`param_id`),
              CONSTRAINT `search_request_offer_param_value_ibfk_1` FOREIGN KEY (`search_request_offer_id`) REFERENCES `search_request_offer` (`id`),
              CONSTRAINT `search_request_offer_param_value_ibfk_2` FOREIGN KEY (`param_id`) REFERENCES `param` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m150625_124518_add_search_request_offer_tables cannot be reverted.\n";

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
