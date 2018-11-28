<?php

use yii\db\Schema;
use yii\db\Migration;

class m150730_093057_add_offer_view_table extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `offer_view` (
              `offer_id` bigint(20) NOT NULL,
              `user_id` bigint(20) NOT NULL,
              `dt` datetime NOT NULL,
              `code` varchar(16) NOT NULL,
              `got_view_bonus` decimal(14,2) NOT NULL DEFAULT '0.00',
              PRIMARY KEY (`offer_id`,`user_id`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `offer_view_ibfk_1` FOREIGN KEY (`offer_id`) REFERENCES `offer` (`id`),
              CONSTRAINT `offer_view_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m150730_093057_add_offer_view_table cannot be reverted.\n";

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
