<?php

use yii\db\Migration;

class m180913_111543_update_payout_request_table extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `pay_out_request`
            ADD `type` enum('JUGLS','TOKEN_DEPOSIT','TOKEN_DEPOSIT_PERCENT') NOT NULL DEFAULT 'JUGLS' AFTER `user_id`;
        ");
    }

    public function down()
    {
        echo "m180913_111543_update_payout_request_table cannot be reverted.\n";

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
