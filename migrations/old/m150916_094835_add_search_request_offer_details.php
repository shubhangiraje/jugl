<?php

use yii\db\Schema;
use yii\db\Migration;

class m150916_094835_add_search_request_offer_details extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `search_request_offer_details_file` (
              `search_request_offer_id` bigint(20) NOT NULL,
              `file_id` bigint(20) NOT NULL,
              `sort_order` int(11) NOT NULL,
              PRIMARY KEY (`search_request_offer_id`,`file_id`),
              KEY `file_id` (`file_id`),
              CONSTRAINT `search_request_offer_details_file_ibfk_1` FOREIGN KEY (`search_request_offer_id`) REFERENCES `search_request_offer` (`id`),
              CONSTRAINT `search_request_offer_details_file_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            ALTER TABLE `search_request_offer`
            ADD `details` mediumtext COLLATE 'utf8_general_ci' NULL AFTER `description`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150916_094835_add_search_request_offer_details cannot be reverted.\n";

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
