<?php

use yii\db\Migration;

class m180911_133645_add_token_deposit_payout_sum_and_last_payout_dt extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `token_deposit`
            ADD `percents_payed_sum` decimal(17,5) NOT NULL DEFAULT '0',
            ADD `last_percents_payout_dt` timestamp NULL AFTER `percents_payed_sum`;
        ");
    }

    public function down()
    {
        echo "m180911_133645_add_token_deposit_payout_sum_and_last_payout_dt cannot be reverted.\n";

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
