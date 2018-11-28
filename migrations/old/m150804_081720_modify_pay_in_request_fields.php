<?php

use yii\db\Schema;
use yii\db\Migration;

class m150804_081720_modify_pay_in_request_fields extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `pay_in_request`
            CHANGE `status` `return_status` enum('SUCCESS','ERROR','PENDING','CANCEL') COLLATE 'utf8_general_ci' NOT NULL AFTER `payment_method`,
            ADD `confirm_status` enum('AWAITING','SUCCESS','ERROR') COLLATE 'utf8_general_ci' NOT NULL AFTER `return_status`,
            COMMENT='';

            ALTER TABLE `pay_in_request`
            CHANGE `return_status` `return_status` enum('AWAITING','SUCCESS','ERROR','PENDING','CANCEL') COLLATE 'utf8_general_ci' NOT NULL AFTER `payment_method`,
            COMMENT='';

            ALTER TABLE `pay_in_request`
            CHANGE `return_status` `return_status` enum('AWAITING','SUCCESS','FAILURE','PENDING','CANCEL') COLLATE 'utf8_general_ci' NOT NULL AFTER `payment_method`,
            COMMENT='';

            ALTER TABLE `pay_in_request`
            CHANGE `confirm_status` `confirm_status` enum('AWAITING','SUCCESS','FAILURE') COLLATE 'utf8_general_ci' NOT NULL AFTER `return_status`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150804_081720_modify_pay_in_request_fields cannot be reverted.\n";

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
