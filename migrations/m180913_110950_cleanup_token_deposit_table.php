<?php

use yii\db\Migration;

class m180913_110950_cleanup_token_deposit_table extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `token_deposit`
            DROP `payout_type`;
        ");

        $this->execute("
            ALTER TABLE `token_deposit`
            ADD `payout_balance_token_log_id` bigint(20) NULL,
            ADD `pay_out_request_id` bigint(20) NULL AFTER `payout_balance_token_log_id`,
            ADD FOREIGN KEY (`payout_balance_token_log_id`) REFERENCES `balance_token_log` (`id`),
            ADD FOREIGN KEY (`pay_out_request_id`) REFERENCES `pay_out_request` (`id`);
        ");

        $this->execute("
            ALTER TABLE `token_deposit`
            CHANGE `pay_out_request_id` `payout_pay_out_request_id` bigint(20) NULL AFTER `payout_balance_token_log_id`;
        ");

        $this->execute("
            ALTER TABLE `token_deposit`
            DROP FOREIGN KEY `token_deposit_ibfk_4`
        ");

        $this->execute("
            ALTER TABLE `token_deposit`
            DROP `payout_balance_token_log_id`,
            ADD `payout_balance_log_id` bigint(20) NULL,
            ADD FOREIGN KEY (`payout_balance_log_id`) REFERENCES `balance_log` (`id`);
        ");
    }

    public function down()
    {
        echo "m180913_110950_cleanup_token_deposit_table cannot be reverted.\n";

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
