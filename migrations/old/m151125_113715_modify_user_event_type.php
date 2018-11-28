<?php

use yii\db\Schema;
use yii\db\Migration;

class m151125_113715_modify_user_event_type extends Migration
{
    public function up()
    {
        $this->execute(
            "ALTER TABLE `user_event`
                CHANGE `type` `type` enum('FRIEND_REQUEST','FRIEND_REQUEST_ACCEPTED','REGISTERED_BY_INVITATION','NEW_NETWORK_MEMBER','SEARCH_REQUEST_OFFER_NEW','SEARCH_REQUEST_OFFER_ACCEPTED','SEARCH_REQUEST_OFFER_DECLINED','SEARCH_REQUEST_OFFER_FEEDBACK','OFFER_REQUEST_NEW','OFFER_REQUEST_ACCEPTED','OFFER_REQUEST_DECLINED','OFFER_REQUEST_FEEDBACK','BROADCAST_MESSAGE','OFFER_REQUEST_PAYING_PAYED','OFFER_REQUEST_PAYING_CONFIRMED','OFFER_MY_REQUEST','SEARCH_REQUEST_MY_OFFER','NEW_PAYOUT_REQUEST','SEARCH_REQUEST_OFFER_MY_FEEDBACK','OFFER_REQUEST_MY_FEEDBACK','DOCUMENTS_VERIFICATION','CHANGE_BALANCE_ADMINISTRATION') COLLATE 'utf8_general_ci' NOT NULL AFTER `dt`;"
        );
    }

    public function down()
    {
        echo "m151125_113715_modify_user_event_type cannot be reverted.\n";

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
