<?php

use yii\db\Schema;
use yii\db\Migration;

class m150915_075646_add_offer_favorite_table extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `offer_favorite` (
                `user_id` bigint(20) NOT NULL,
                `offer_id` bigint(20) NOT NULL,
                  PRIMARY KEY (`user_id`,`offer_id`),
                  KEY `offer_id` (`offer_id`),
                  CONSTRAINT `offer_favorite_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
                  CONSTRAINT `offer_favorite_ibfk_2` FOREIGN KEY (`offer_id`) REFERENCES `offer` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        echo "m150915_075646_add_offer_favorite_table cannot be reverted.\n";

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
