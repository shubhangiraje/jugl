<?php

use yii\db\Migration;

class m170601_081438_add_user_balances extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            ADD `balance_buyed` decimal(17,5) NOT NULL DEFAULT '0.00000' AFTER `balance`,
            ADD `balance_earned` decimal(17,5) NOT NULL DEFAULT '0.00000' AFTER `balance_buyed`;
        ");

        $this->execute("
            update `user` set balance_earned=balance,balance_buyed=0;        
        ");

        $this->execute("
            ALTER TABLE `balance_log`
            ADD `sum_earned` decimal(17,5) NOT NULL AFTER `sum`,
            ADD `sum_buyed` decimal(17,5) NOT NULL AFTER `sum_earned`;
        ");

        $this->execute("
            update `balance_log` set sum_earned=`sum` where sum_earned=0 and sum_buyed=0;        
        ");

        $this->execute("
            ALTER TABLE `balance_log`
            ADD INDEX `user_id_sum` (`user_id`, `sum`);
        ");
    }

    public function down()
    {
        echo "m170601_081438_add_user_balances cannot be reverted.\n";

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
