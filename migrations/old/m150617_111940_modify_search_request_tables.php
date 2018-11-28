<?php

use yii\db\Schema;
use yii\db\Migration;

class m150617_111940_modify_search_request_tables extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `param`
            CHANGE `type` `type` enum('LIST','TEXT') COLLATE 'utf8_general_ci' NOT NULL AFTER `title`,
            COMMENT='';
        ");

        $this->execute("
            DROP TABLE IF EXISTS `search_request_file`
        ");

        $this->execute("
            DROP TABLE IF EXISTS `search_request_interest`
        ");

        $this->execute("
            DROP TABLE IF EXISTS `search_request_param_value`
        ");

        $this->execute("
            DROP TABLE IF EXISTS `search_request`
        ");

        $this->execute("
            CREATE TABLE `search_request` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `user_id` bigint(20) NOT NULL,
              `title` varchar(200) NOT NULL,
              `description` mediumtext NOT NULL,
              `price_from` decimal(14,2) NOT NULL,
              `price_to` decimal(14,2) NOT NULL,
              `bonus` decimal(14,2) DEFAULT NULL,
              `zip` varchar(64) NOT NULL,
              `city` varchar(64) DEFAULT NULL,
              `address` varchar(128) DEFAULT NULL,
              `active_till` date DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `search_request_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");

        $this->execute("
            CREATE TABLE `search_request_file` (
              `search_request_id` bigint(20) NOT NULL,
              `file_id` bigint(20) NOT NULL,
              `sort_order` bigint(20) DEFAULT NULL,
              PRIMARY KEY (`search_request_id`,`file_id`),
              KEY `file_id` (`file_id`),
              CONSTRAINT `search_request_file_ibfk_3` FOREIGN KEY (`search_request_id`) REFERENCES `search_request` (`id`),
              CONSTRAINT `search_request_file_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");

        $this->execute("
            CREATE TABLE `search_request_interest` (
              `search_request_id` bigint(20) NOT NULL,
              `level1_interest_id` int(11) NOT NULL,
              `level2_interest_id` int(11) NOT NULL,
              `level3_interest_id` int(11) NOT NULL,
              PRIMARY KEY (`search_request_id`,`level1_interest_id`,`level2_interest_id`,`level3_interest_id`),
              KEY `level1_interest_id` (`level1_interest_id`),
              KEY `level2_interest_id` (`level2_interest_id`),
              KEY `level3_interest_id` (`level3_interest_id`),
              CONSTRAINT `search_request_interest_ibfk_5` FOREIGN KEY (`search_request_id`) REFERENCES `search_request` (`id`),
              CONSTRAINT `search_request_interest_ibfk_2` FOREIGN KEY (`level1_interest_id`) REFERENCES `interest` (`id`),
              CONSTRAINT `search_request_interest_ibfk_3` FOREIGN KEY (`level2_interest_id`) REFERENCES `interest` (`id`),
              CONSTRAINT `search_request_interest_ibfk_4` FOREIGN KEY (`level3_interest_id`) REFERENCES `interest` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");

        $this->execute("
            CREATE TABLE `search_request_param_value` (
              `id` bigint(20) NOT NULL,
              `search_request_id` bigint(20) NOT NULL,
              `param_id` int(11) NOT NULL,
              `param_value_id` int(11) DEFAULT NULL,
              `param_value` varchar(128) DEFAULT NULL,
              KEY `param_id` (`param_id`),
              KEY `param_value_id` (`param_value_id`),
              KEY `search_request_id` (`search_request_id`),
              CONSTRAINT `search_request_param_value_ibfk_4` FOREIGN KEY (`search_request_id`) REFERENCES `search_request` (`id`),
              CONSTRAINT `search_request_param_value_ibfk_2` FOREIGN KEY (`param_id`) REFERENCES `param` (`id`),
              CONSTRAINT `search_request_param_value_ibfk_3` FOREIGN KEY (`param_value_id`) REFERENCES `param_value` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");

        $this->execute("
            ALTER TABLE `search_request_file`
            CHANGE `sort_order` `sort_order` bigint(20) NOT NULL DEFAULT '0' AFTER `file_id`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `search_request_param_value`
            CHANGE `id` `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150617_111940_modify_search_request_tables cannot be reverted.\n";

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
