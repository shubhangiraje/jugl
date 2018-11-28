<?php

use yii\db\Schema;
use yii\db\Migration;

class m150702_112401_add_offer_and_offer_request_tables extends Migration
{
    public function up()
    {
        $this->execute("

            CREATE TABLE `offer` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `create_dt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              `user_id` bigint(20) NOT NULL,
              `title` varchar(200) NOT NULL,
              `description` mediumtext NOT NULL,
              `price_from` decimal(14,2) NOT NULL,
              `price_to` decimal(14,2) NOT NULL,
              `view_bonus` decimal(14,2) DEFAULT NULL,
              `buy_bonus` decimal(14,2) DEFAULT NULL,
              `zip` varchar(64) NOT NULL,
              `city` varchar(64) DEFAULT NULL,
              `address` varchar(128) DEFAULT NULL,
              `active_till` date NOT NULL,
              `accepted_offer_request_id` bigint(20) DEFAULT NULL,
              `status` enum('ACTIVE','EXPIRED','CLOSED','DELETED') NOT NULL DEFAULT 'ACTIVE',
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`),
              KEY `accepted_offer_request_id` (`accepted_offer_request_id`),
              CONSTRAINT `offer_ibfk_2` FOREIGN KEY (`accepted_offer_request_id`) REFERENCES `offer_request` (`id`),
              CONSTRAINT `offer_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            CREATE TABLE `offer_file` (
              `offer_id` bigint(20) NOT NULL,
              `file_id` bigint(20) NOT NULL,
              `sort_order` bigint(20) NOT NULL DEFAULT '0',
              PRIMARY KEY (`offer_id`,`file_id`),
              KEY `file_id` (`file_id`),
              CONSTRAINT `offer_file_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`),
              CONSTRAINT `offer_file_ibfk_3` FOREIGN KEY (`offer_id`) REFERENCES `offer` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            CREATE TABLE `offer_interest` (
              `offer_id` bigint(20) NOT NULL,
              `level1_interest_id` int(11) NOT NULL,
              `level2_interest_id` int(11) NOT NULL,
              `level3_interest_id` int(11) NOT NULL,
              PRIMARY KEY (`offer_id`,`level1_interest_id`,`level2_interest_id`,`level3_interest_id`),
              KEY `level1_interest_id` (`level1_interest_id`),
              KEY `level2_interest_id` (`level2_interest_id`),
              KEY `level3_interest_id` (`level3_interest_id`),
              CONSTRAINT `offer_interest_ibfk_5` FOREIGN KEY (`offer_id`) REFERENCES `offer` (`id`),
              CONSTRAINT `offer_interest_ibfk_2` FOREIGN KEY (`level1_interest_id`) REFERENCES `interest` (`id`),
              CONSTRAINT `offer_interest_ibfk_3` FOREIGN KEY (`level2_interest_id`) REFERENCES `interest` (`id`),
              CONSTRAINT `offer_interest_ibfk_4` FOREIGN KEY (`level3_interest_id`) REFERENCES `interest` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            CREATE TABLE `offer_param_value` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `offer_id` bigint(20) NOT NULL,
              `param_id` int(11) NOT NULL,
              `param_value_id` int(11) DEFAULT NULL,
              `param_value` varchar(128) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `param_id` (`param_id`),
              KEY `param_value_id` (`param_value_id`),
              KEY `offer_id` (`offer_id`),
              CONSTRAINT `offer_param_value_ibfk_2` FOREIGN KEY (`param_id`) REFERENCES `param` (`id`),
              CONSTRAINT `offer_param_value_ibfk_3` FOREIGN KEY (`param_value_id`) REFERENCES `param_value` (`id`),
              CONSTRAINT `offer_param_value_ibfk_4` FOREIGN KEY (`offer_id`) REFERENCES `offer` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            CREATE TABLE `offer_request` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `offer_id` bigint(20) NOT NULL,
              `user_id` bigint(20) NOT NULL,
              `description` mediumtext NOT NULL,
              `price_from` decimal(14,2) NOT NULL,
              `price_to` decimal(14,2) NOT NULL,
              `relevancy` tinyint(4) NOT NULL,
              `status` enum('ACTIVE','ACCEPTED','REJECTED','DELETED') NOT NULL DEFAULT 'ACTIVE',
              PRIMARY KEY (`id`),
              KEY `offer_id` (`offer_id`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `offer_request_ibfk_1` FOREIGN KEY (`offer_id`) REFERENCES `offer` (`id`),
              CONSTRAINT `offer_request_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            CREATE TABLE `offer_request_file` (
              `offer_request_id` bigint(20) NOT NULL,
              `file_id` bigint(20) NOT NULL,
              `sort_order` int(11) NOT NULL,
              PRIMARY KEY (`offer_request_id`,`file_id`),
              KEY `file_id` (`file_id`),
              CONSTRAINT `offer_request_file_ibfk_1` FOREIGN KEY (`offer_request_id`) REFERENCES `offer_request` (`id`),
              CONSTRAINT `offer_request_file_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            CREATE TABLE `offer_request_param_value` (
              `offer_request_id` bigint(20) NOT NULL,
              `param_id` int(11) NOT NULL,
              `match` tinyint(1) NOT NULL,
              PRIMARY KEY (`offer_request_id`,`param_id`),
              KEY `param_id` (`param_id`),
              CONSTRAINT `offer_request_param_value_ibfk_1` FOREIGN KEY (`offer_request_id`) REFERENCES `offer_request` (`id`),
              CONSTRAINT `offer_request_param_value_ibfk_2` FOREIGN KEY (`param_id`) REFERENCES `param` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            CREATE TABLE `search_request_offer` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `search_request_id` bigint(20) NOT NULL,
              `user_id` bigint(20) NOT NULL,
              `description` mediumtext NOT NULL,
              `price_from` decimal(14,2) NOT NULL,
              `price_to` decimal(14,2) NOT NULL,
              `relevancy` tinyint(4) NOT NULL,
              `status` enum('ACTIVE','ACCEPTED','REJECTED','DELETED') NOT NULL DEFAULT 'ACTIVE',
              PRIMARY KEY (`id`),
              KEY `search_request_id` (`search_request_id`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `search_request_offer_ibfk_1` FOREIGN KEY (`search_request_id`) REFERENCES `search_request` (`id`),
              CONSTRAINT `search_request_offer_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        ");
    }

    public function down()
    {
        echo "m150702_112401_add_offer_and_offer_request_tables cannot be reverted.\n";

        return false;
    }
}
