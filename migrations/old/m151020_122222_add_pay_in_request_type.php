<?php

use yii\db\Schema;
use yii\db\Migration;

class m151020_122222_add_pay_in_request_type extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `pay_in_request`
            ADD `type` enum('PAY_IN','PACKET') COLLATE 'utf8_general_ci' NOT NULL AFTER `confirm_status`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151020_122222_add_pay_in_request_type cannot be reverted.\n";

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
