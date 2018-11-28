<?php

use yii\db\Migration;

class m180906_115245_add_token_deposit_guarantee_sum_cost extends Migration
{
    public function up()
    {
        $this->execute("
          ALTER TABLE `token_deposit_guarantee`
          ADD `sum_cost` decimal(10,2) NOT NULL DEFAULT '0' AFTER `sum`;
        ");
    }

    public function down()
    {
        echo "m180906_115245_add_token_deposit_guarantee_sum_cost cannot be reverted.\n";

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
