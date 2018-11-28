<?php

use yii\db\Migration;

class m180911_112653_add_sum_token_deposit_percent_in_tables extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `balance_log`
            ADD `sum_token_deposit_percent` decimal(17,5) NOT NULL DEFAULT '0' AFTER `sum_buyed`;
        ");

        $this->execute("
            ALTER TABLE `balance_log`
            CHANGE `sum_token_deposit_percent` `sum_token_deposit_percent` decimal(17,5) NOT NULL AFTER `sum_buyed`;
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD `balance_token_deposit_percent` decimal(17,5) NOT NULL DEFAULT '0.00000' AFTER `balance_token_earned`;
        ");
    }

    public function down()
    {
        echo "m180911_112653_add_sum_token_deposit_percent_in_tables cannot be reverted.\n";

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
