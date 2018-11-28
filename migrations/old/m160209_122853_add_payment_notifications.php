<?php

use yii\db\Schema;
use yii\db\Migration;

class m160209_122853_add_payment_notifications extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `offer_request`
            CHANGE `pay_remindered_dt` `accepted_dt` timestamp NULL DEFAULT '0000-00-00 00:00:00' AFTER `pay_method`,
            ADD `no_payment_buyer_notified` tinyint(1) NOT NULL DEFAULT '0' AFTER `accepted_dt`,
            ADD `no_payment_seller_notified` tinyint(1) NOT NULL DEFAULT '0' AFTER `no_payment_buyer_notified`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `user`
            ADD `offer_request_no_payment_notifications` int(11) NOT NULL DEFAULT '0',
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `user_event`
            CHANGE `type` `type` enum('FRIEND_REQUEST','FRIEND_REQUEST_ACCEPTED','REGISTERED_BY_INVITATION','NEW_NETWORK_MEMBER','SEARCH_REQUEST_OFFER_NEW','SEARCH_REQUEST_OFFER_ACCEPTED','SEARCH_REQUEST_OFFER_DECLINED','SEARCH_REQUEST_OFFER_FEEDBACK','OFFER_REQUEST_NEW','OFFER_REQUEST_ACCEPTED','OFFER_REQUEST_DECLINED','OFFER_REQUEST_FEEDBACK','BROADCAST_MESSAGE','OFFER_REQUEST_PAYING_PAYED','OFFER_REQUEST_PAYING_CONFIRMED','OFFER_MY_REQUEST','SEARCH_REQUEST_MY_OFFER','NEW_PAYOUT_REQUEST','SEARCH_REQUEST_OFFER_MY_FEEDBACK','OFFER_REQUEST_MY_FEEDBACK','DOCUMENTS_VERIFICATION','CHANGE_BALANCE_ADMINISTRATION','OFFER_REQUEST_ACCEPTED_PAYED','OFFER_REQUEST_PAYING_PAYED_CONFIRMED','DOCUMENT_VALIDATION_SUCCESS','OFFER_REQUEST_PAYING_SELLER_NOTIFICATION','OFFER_REQUEST_PAYING_BUYER_NOTIFICATION') COLLATE 'utf8_general_ci' NOT NULL AFTER `dt`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `user_event`
            CHANGE `type` `type` enum('FRIEND_REQUEST','FRIEND_REQUEST_ACCEPTED','REGISTERED_BY_INVITATION','NEW_NETWORK_MEMBER','SEARCH_REQUEST_OFFER_NEW','SEARCH_REQUEST_OFFER_ACCEPTED','SEARCH_REQUEST_OFFER_DECLINED','SEARCH_REQUEST_OFFER_FEEDBACK','OFFER_REQUEST_NEW','OFFER_REQUEST_ACCEPTED','OFFER_REQUEST_DECLINED','OFFER_REQUEST_FEEDBACK','BROADCAST_MESSAGE','OFFER_REQUEST_PAYING_PAYED','OFFER_REQUEST_PAYING_CONFIRMED','OFFER_MY_REQUEST','SEARCH_REQUEST_MY_OFFER','NEW_PAYOUT_REQUEST','SEARCH_REQUEST_OFFER_MY_FEEDBACK','OFFER_REQUEST_MY_FEEDBACK','DOCUMENTS_VERIFICATION','CHANGE_BALANCE_ADMINISTRATION','OFFER_REQUEST_ACCEPTED_PAYED','OFFER_REQUEST_PAYING_PAYED_CONFIRMED','DOCUMENT_VALIDATION_SUCCESS','OFFER_REQUEST_PAYING_SELLER_NOTIFICATION','OFFER_REQUEST_PAYING_BUYER_NOTIFICATION','OFFER_REQUEST_PAYING_WARNING') COLLATE 'utf8_general_ci' NOT NULL AFTER `dt`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer_request`
            DROP `accepted_dt`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `user`
            CHANGE `offer_request_no_payment_notifications` `payment_complaints` int(11) NOT NULL DEFAULT '0' AFTER `free_registrations_limit`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `offer_request`
            ADD `payment_complaint` tinyint(1) NOT NULL DEFAULT '0' AFTER `no_payment_seller_notified`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `user_event`
            CHANGE `type` `type` enum('FRIEND_REQUEST','FRIEND_REQUEST_ACCEPTED','REGISTERED_BY_INVITATION','NEW_NETWORK_MEMBER','SEARCH_REQUEST_OFFER_NEW','SEARCH_REQUEST_OFFER_ACCEPTED','SEARCH_REQUEST_OFFER_DECLINED','SEARCH_REQUEST_OFFER_FEEDBACK','OFFER_REQUEST_NEW','OFFER_REQUEST_ACCEPTED','OFFER_REQUEST_DECLINED','OFFER_REQUEST_FEEDBACK','BROADCAST_MESSAGE','OFFER_REQUEST_PAYING_PAYED','OFFER_REQUEST_PAYING_CONFIRMED','OFFER_MY_REQUEST','SEARCH_REQUEST_MY_OFFER','NEW_PAYOUT_REQUEST','SEARCH_REQUEST_OFFER_MY_FEEDBACK','OFFER_REQUEST_MY_FEEDBACK','DOCUMENTS_VERIFICATION','CHANGE_BALANCE_ADMINISTRATION','OFFER_REQUEST_ACCEPTED_PAYED','OFFER_REQUEST_PAYING_PAYED_CONFIRMED','DOCUMENT_VALIDATION_SUCCESS','OFFER_REQUEST_PAYING_SELLER_NOTIFICATION','OFFER_REQUEST_PAYING_BUYER_NOTIFICATION','OFFER_REQUEST_PAYING_WARNING','OFFER_REQUEST_PAYING_COMPLAINT') COLLATE 'utf8_general_ci' NOT NULL AFTER `dt`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m160209_122853_add_payment_notifications cannot be reverted.\n";

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