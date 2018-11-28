<?php

use yii\db\Schema;
use yii\db\Migration;

class m151013_123033_add_new_event_types extends Migration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE `user_event`
            CHANGE `type` `type` enum('FRIEND_REQUEST','FRIEND_REQUEST_ACCEPTED','REGISTERED_BY_INVITATION','NEW_NETWORK_MEMBER','SEARCH_REQUEST_OFFER_NEW','SEARCH_REQUEST_OFFER_ACCEPTED','SEARCH_REQUEST_OFFER_DECLINED','SEARCH_REQUEST_OFFER_FEEDBACK','OFFER_REQUEST_NEW','OFFER_REQUEST_ACCEPTED','OFFER_REQUEST_DECLINED','OFFER_REQUEST_FEEDBACK','BROADCAST_MESSAGE','OFFER_REQUEST_PAYING_PAYED','OFFER_REQUEST_PAYING_CONFIRMED','OFFER_MY_REQUEST','SEARCH_REQUEST_MY_OFFER') COLLATE 'utf8_general_ci' NOT NULL AFTER `dt`,
            COMMENT='';
        ");
    }

    public function down()
    {
        echo "m151013_123033_add_new_event_types cannot be reverted.\n";

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
