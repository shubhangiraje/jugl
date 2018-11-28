<?php

use yii\db\Schema;
use yii\db\Migration;

class m151222_092016_change_search_request_structure extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer_request`
            ADD `pay_tx_id` int NULL,
            ADD `pay_status` enum('INVITED','PAYED','CONFIRMED') NULL AFTER `pay_tx_id`,
            ADD `pay_method` enum('BANK','PAYPAL','JUGLS','POD') NULL AFTER `pay_status`,
            ADD `pay_remindered_dt` timestamp NULL DEFAULT '0000-00-00 00:00:00' AFTER `pay_method`,
            ADD `pay_data` varchar(512) NULL AFTER `pay_remindered_dt`,
            ADD `delivery_address` varchar(512) NULL AFTER `pay_data`,
            COMMENT='';
        ");

        $this->execute("
            update offer_request
            join offer on (offer.accepted_offer_request_id=offer_request.id)
            set
            offer_request.pay_tx_id=offer.pay_tx_id,
            offer_request.pay_status=offer.pay_status,
            offer_request.pay_method=offer.pay_method,
            offer_request.pay_remindered_dt=offer.pay_remindered_dt,
            offer_request.pay_data=offer.pay_data,
            offer_request.delivery_address=offer.delivery_address
        ");

        $this->execute("
            ALTER TABLE `offer`
            ADD `amount` int(11) NULL AFTER `uf_messages_per_day_to`,
            DROP `pay_tx_id`,
            DROP `pay_status`,
            DROP `pay_method`,
            DROP `pay_remindered_dt`,
            DROP `pay_data`,
            DROP `delivery_address`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer`
            ADD `show_amount` tinyint(1) NOT NULL AFTER `amount`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer`
            ADD `delivery_cost` decimal(14,2) NULL AFTER `show_amount`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer_request`
            ADD `user_feedback_id` bigint(20) NULL,
            ADD FOREIGN KEY (`user_feedback_id`) REFERENCES `user_feedback` (`id`),
            COMMENT='';
        ");

        $this->execute("
            update offer_request
            join offer on (offer.id=offer_request.offer_id)
            set offer_request.user_feedback_id=offer.user_feedback_id
        ");

        $this->execute("
            ALTER TABLE `offer`
            DROP FOREIGN KEY `offer_ibfk_3`
        ");

        $this->execute("
            ALTER TABLE `offer`
            DROP `user_feedback_id`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer`
            CHANGE `show_amount` `show_amount` tinyint(1) NOT NULL DEFAULT '0' AFTER `amount`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151222_092016_change_search_request_structure cannot be reverted.\n";

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
