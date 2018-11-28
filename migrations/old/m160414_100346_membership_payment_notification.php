<?php

use yii\db\Schema;
use yii\db\Migration;

class m160414_100346_membership_payment_notification extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user`
            CHANGE `registration_dt` `registration_dt` timestamp NULL AFTER `registration_code_id`,
            ADD `no_membership_payment_notified` tinyint NOT NULL DEFAULT '0',
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `user`
            CHANGE `registration_dt` `registration_dt` timestamp NULL AFTER `registration_code_id`,
            ADD `no_membership_payment_notified` tinyint NOT NULL DEFAULT '0',
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `user`
            CHANGE `registration_dt` `registration_dt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `registration_code_id`,
            COMMENT='';
        ");

        $this->execute("
            ALTER TABLE `user_event`
            CHANGE `type` `type` enum('FRIEND_REQUEST','FRIEND_REQUEST_ACCEPTED','REGISTERED_BY_INVITATION','NEW_NETWORK_MEMBER','SEARCH_REQUEST_OFFER_NEW','SEARCH_REQUEST_OFFER_ACCEPTED','SEARCH_REQUEST_OFFER_DECLINED','SEARCH_REQUEST_OFFER_FEEDBACK','OFFER_REQUEST_NEW','OFFER_REQUEST_ACCEPTED','OFFER_REQUEST_DECLINED','OFFER_REQUEST_FEEDBACK','BROADCAST_MESSAGE','OFFER_REQUEST_PAYING_PAYED','OFFER_REQUEST_PAYING_CONFIRMED','OFFER_MY_REQUEST','SEARCH_REQUEST_MY_OFFER','NEW_PAYOUT_REQUEST','SEARCH_REQUEST_OFFER_MY_FEEDBACK','OFFER_REQUEST_MY_FEEDBACK','DOCUMENTS_VERIFICATION','CHANGE_BALANCE_ADMINISTRATION','OFFER_REQUEST_ACCEPTED_PAYED','OFFER_REQUEST_PAYING_PAYED_CONFIRMED','DOCUMENT_VALIDATION_SUCCESS','OFFER_REQUEST_PAYING_SELLER_NOTIFICATION','OFFER_REQUEST_PAYING_BUYER_NOTIFICATION','OFFER_REQUEST_PAYING_WARNING','OFFER_REQUEST_PAYING_COMPLAINT','OFFER_BUGET_USED_90','OFFER_BUGET_USED_100','NOT_FINISHED_REGISTRATION') COLLATE 'utf8_general_ci' NOT NULL AFTER `dt`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m160414_100346_membership_payment_notification cannot be reverted.\n";

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
