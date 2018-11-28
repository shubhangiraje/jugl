<?php

use yii\db\Schema;
use yii\db\Migration;

class m150908_102809_add_delivery_time_and_price extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            CHANGE `price_from` `price` decimal(14,2) NOT NULL AFTER `description`,
            ADD `delivery_days` int NULL AFTER `price`,
            DROP `price_to`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150908_102809_add_delivery_time_and_price cannot be reverted.\n";

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
