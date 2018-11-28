<?php

use yii\db\Schema;
use yii\db\Migration;

class m150921_095951_add_offer_payment_fields extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            ADD `pay_status` enum('INVITATION','PAYED','CONFIRMED') NULL,
            ADD `pay_method` enum('BANK','PAYPAL','JUGLS') NULL AFTER `pay_status`,
            ADD `pay_user_bank_data_id` bigint(20) NULL AFTER `pay_method`,
            ADD `pay_paypal_email` varchar(64) NULL AFTER `pay_user_bank_data_id`,
            ADD FOREIGN KEY (`pay_user_bank_data_id`) REFERENCES `user_bank_data` (`id`),
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer`
            DROP FOREIGN KEY `offer_ibfk_6`
        ");

        $this->execute("
            ALTER TABLE `offer`
            DROP `pay_user_bank_data_id`,
            CHANGE `pay_paypal_email` `pay_data` varchar(512) COLLATE 'utf8_general_ci' NULL AFTER `pay_method`,
            ADD `delivery_address` varchar(512) COLLATE 'utf8_general_ci' NULL,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer`
            CHANGE `pay_status` `pay_status` enum('INVITED','PAYED','CONFIRMED') COLLATE 'utf8_general_ci' NULL AFTER `uf_messages_per_day_to`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer`
            ADD `pay_tx_id` int(11) NULL AFTER `uf_messages_per_day_to`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m150921_095951_add_offer_payment_fields cannot be reverted.\n";

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
