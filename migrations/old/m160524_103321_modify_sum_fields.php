<?php

use yii\db\Schema;
use yii\db\Migration;

class m160524_103321_modify_sum_fields extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `balance_log`
            CHANGE `sum` `sum` decimal(17,5) NOT NULL AFTER `type`,
            COMMENT='';            
        ");

        $this->execute("
            ALTER TABLE `user`
            CHANGE `balance` `balance` decimal(17,5) NOT NULL DEFAULT '0.00' AFTER `visibility_birthday`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m160524_103321_modify_sum_fields cannot be reverted.\n";

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
