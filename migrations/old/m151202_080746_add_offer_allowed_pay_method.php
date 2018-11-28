<?php

use yii\db\Schema;
use yii\db\Migration;

class m151202_080746_add_offer_allowed_pay_method extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer`
            ADD `pay_allow_bank` tinyint(1) NOT NULL DEFAULT '0' AFTER `auto_accept`,
            ADD `pay_allow_paypal` tinyint(1) NOT NULL DEFAULT '0' AFTER `pay_allow_bank`,
            ADD `pay_allow_jugl` tinyint(1) NOT NULL DEFAULT '0' AFTER `pay_allow_paypal`,
            ADD `pay_allow_pod` tinyint(1) NOT NULL DEFAULT '0' AFTER `pay_allow_jugl`,
            CHANGE `pay_method` `pay_method` enum('BANK','PAYPAL','JUGLS','POD') COLLATE 'utf8_general_ci' NULL AFTER `pay_status`,
            COMMENT='';
        ");

        $this->execute("
            update offer set pay_allow_bank=1,pay_allow_paypal=1,pay_allow_jugl=1,pay_allow_pod=1
        ");
    }

    public function down()
    {
        echo "m151202_080746_add_offer_allowed_pay_method cannot be reverted.\n";

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
