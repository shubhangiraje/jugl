<?php

use yii\db\Migration;

class m180910_122701_add_token_deposit_buy_price extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `token_deposit`
            ADD `buy_sum` decimal(10,2) NOT NULL,
            ADD `buy_currency` enum('EUR','JUGLS') COLLATE 'utf8_general_ci' NOT NULL AFTER `buy_sum`;
        ");
    }

    public function down()
    {
        echo "m180910_122701_add_token_deposit_buy_price cannot be reverted.\n";

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
