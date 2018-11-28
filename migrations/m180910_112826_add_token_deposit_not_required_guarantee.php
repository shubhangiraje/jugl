<?php

use yii\db\Migration;

class m180910_112826_add_token_deposit_not_required_guarantee extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `token_deposit`
            CHANGE `token_deposit_guarantee_id` `token_deposit_guarantee_id` bigint(20) NULL AFTER `completion_dt`;
        ");
    }

    public function down()
    {
        echo "m180910_112826_add_token_deposit_not_required_guarantee cannot be reverted.\n";

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
